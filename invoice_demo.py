import datetime
from invoice_system import Invoice, InvoiceItem
from invoice_printer import InvoicePrinter, PrinterConnectionError

def create_sample_invoice():
    """Create a sample invoice for testing"""
    items = [
        InvoiceItem(
            product_id="PROD001",
            description="Laptop",
            quantity=1,
            unit_price=999.99
        ),
        InvoiceItem(
            product_id="PROD002",
            description="Mouse",
            quantity=2,
            unit_price=24.99
        ),
        InvoiceItem(
            product_id="PROD003",
            description="Keyboard",
            quantity=1,
            unit_price=59.99
        )
    ]
    
    invoice = Invoice(
        invoice_id="INV-2023-001",
        customer_name="John Doe",
        customer_address="123 Main St, Anytown, AN 12345",
        date=datetime.date.today(),
        items=items
    )
    
    return invoice

def print_with_wifi(invoice):
    """Print invoice using WiFi printer"""
    # Replace with your actual printer IP address
    printer_ip = "192.168.1.100"  # Example IP
    
    invoice_printer = InvoicePrinter(
        connection_type="wifi",
        printer_ip=printer_ip
    )
    
    try:
        success = invoice_printer.print_invoice(invoice)
        if success:
            print("Invoice printed successfully via WiFi!")
        return success
    except PrinterConnectionError as e:
        print(f"WiFi printing error: {str(e)}")
        return False

def print_with_bluetooth(invoice):
    """Print invoice using Bluetooth printer"""
    # Replace with your actual Bluetooth printer MAC address
    bluetooth_mac = "00:11:22:33:44:55"  # Example MAC
    
    invoice_printer = InvoicePrinter(
        connection_type="bluetooth",
        bluetooth_mac=bluetooth_mac
    )
    
    try:
        success = invoice_printer.print_invoice(invoice)
        if success:
            print("Invoice printed successfully via Bluetooth!")
        return success
    except PrinterConnectionError as e:
        print(f"Bluetooth printing error: {str(e)}")
        return False

def main():
    # Create a sample invoice
    invoice = create_sample_invoice()
    
    # Save invoice to file (always do this as a backup)
    invoice_printer = InvoicePrinter()
    file_path = invoice_printer.save_invoice_to_file(invoice)
    print(f"Invoice saved to {file_path}")
    
    # Ask user which printing method to use
    print("\nSelect printing method:")
    print("1. WiFi Printer")
    print("2. Bluetooth Printer")
    print("3. Save to file only")
    
    choice = input("Enter your choice (1-3): ")
    
    if choice == "1":
        print_with_wifi(invoice)
    elif choice == "2":
        print_with_bluetooth(invoice)
    elif choice == "3":
        print("Invoice saved to file only.")
    else:
        print("Invalid choice. Invoice saved to file only.")

if __name__ == "__main__":
    main()