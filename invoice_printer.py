import os
import datetime
from typing import Optional, List, Literal
from invoice_system import Invoice, InvoiceItem

# Import both printer types
from wifi_printer import WiFiPrinter, PrinterConnectionError as WiFiPrinterError
from bluetooth_printer import BluetoothPrinter, BluetoothConnectionError

class PrinterConnectionError(Exception):
    """Generic printer connection error"""
    pass

class InvoicePrinter:
    def __init__(self, 
                 connection_type: Literal["wifi", "bluetooth"] = "wifi",
                 printer_ip: Optional[str] = None, 
                 printer_port: int = 9100,
                 bluetooth_mac: Optional[str] = None,
                 bluetooth_port: int = 1):
        """
        Initialize the invoice printer
        
        Args:
            connection_type: Type of connection ("wifi" or "bluetooth")
            printer_ip: IP address of the WiFi printer (required for WiFi)
            printer_port: Port number for the WiFi printer
            bluetooth_mac: MAC address of the Bluetooth printer (required for Bluetooth)
            bluetooth_port: Port number for the Bluetooth printer
        """
        self.connection_type = connection_type
        self.printer_ip = printer_ip
        self.printer_port = printer_port
        self.bluetooth_mac = bluetooth_mac
        self.bluetooth_port = bluetooth_port
    
    def format_invoice(self, invoice: Invoice) -> str:
        """
        Format an invoice for printing
        
        Args:
            invoice: The invoice to format
            
        Returns:
            str: Formatted invoice text
        """
        # Create a nicely formatted invoice
        lines = []
        
        # Header
        lines.append("=" * 40)
        lines.append(f"INVOICE #{invoice.invoice_id}".center(40))
        lines.append("=" * 40)
        lines.append("")
        
        # Date
        lines.append(f"Date: {invoice.date.strftime('%Y-%m-%d')}")
        lines.append("")
        
        # Customer info
        lines.append("BILL TO:")
        lines.append(invoice.customer_name)
        lines.append(invoice.customer_address)
        lines.append("")
        
        # Items header
        lines.append("-" * 40)
        lines.append(f"{'Item':<20}{'Qty':<5}{'Price':<8}{'Total':<7}")
        lines.append("-" * 40)
        
        # Items
        for item in invoice.items:
            lines.append(f"{item.description[:19]:<20}{item.quantity:<5}{item.unit_price:<8.2f}{item.total_price:<7.2f}")
        
        # Totals
        lines.append("-" * 40)
        lines.append(f"{'Subtotal:':<33}{invoice.subtotal:>7.2f}")
        lines.append(f"{'Tax (' + str(int(invoice.tax_rate * 100)) + '%):':<33}{invoice.tax_amount:>7.2f}")
        lines.append(f"{'TOTAL:':<33}{invoice.total:>7.2f}")
        lines.append("=" * 40)
        lines.append("")
        lines.append("Thank you for your business!")
        lines.append("")
        
        return "\n".join(lines)
    
    def print_invoice(self, invoice: Invoice) -> bool:
        """
        Print an invoice to the connected printer
        
        Args:
            invoice: The invoice to print
            
        Returns:
            bool: True if printing successful
        """
        formatted_invoice = self.format_invoice(invoice)
        
        if self.connection_type == "wifi":
            if not self.printer_ip:
                raise PrinterConnectionError("No WiFi printer IP configured")
            
            try:
                with WiFiPrinter(self.printer_ip, self.printer_port) as printer:
                    # Add printer control codes for page feed at the end
                    printer.print_text(formatted_invoice + "\n\n\n\n\n\n")
                    return True
            except WiFiPrinterError as e:
                raise PrinterConnectionError(f"WiFi printer error: {str(e)}")
                
        elif self.connection_type == "bluetooth":
            if not self.bluetooth_mac:
                raise PrinterConnectionError("No Bluetooth MAC address configured")
            
            try:
                with BluetoothPrinter(self.bluetooth_mac, self.bluetooth_port) as printer:
                    # Add printer control codes for page feed at the end
                    printer.print_text(formatted_invoice + "\n\n\n\n\n\n")
                    return True
            except BluetoothConnectionError as e:
                raise PrinterConnectionError(f"Bluetooth printer error: {str(e)}")
        else:
            raise ValueError(f"Unsupported connection type: {self.connection_type}")
    
    def save_invoice_to_file(self, invoice: Invoice, directory: str = "invoices") -> str:
        """
        Save the invoice to a text file
        
        Args:
            invoice: The invoice to save
            directory: Directory to save the invoice in
            
        Returns:
            str: Path to the saved invoice file
        """
        # Create directory if it doesn't exist
        os.makedirs(directory, exist_ok=True)
        
        # Generate filename
        filename = f"{directory}/invoice_{invoice.invoice_id}_{invoice.date.strftime('%Y%m%d')}.txt"
        
        # Write invoice to file
        with open(filename, 'w') as f:
            f.write(self.format_invoice(invoice))
        
        return filename