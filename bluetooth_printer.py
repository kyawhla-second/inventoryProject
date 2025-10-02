import bluetooth
import time
from typing import Optional

class BluetoothConnectionError(Exception):
    """Exception raised when Bluetooth printer connection fails"""
    pass

class BluetoothPrinter:
    def __init__(self, mac_address: str, port: int = 1):
        """
        Initialize Bluetooth printer connection
        
        Args:
            mac_address: MAC address of the Bluetooth printer
            port: Bluetooth port (default is 1 for most Bluetooth printers)
        """
        self.mac_address = mac_address
        self.port = port
        self.socket = None
        self.connected = False
    
    def connect(self, timeout: int = 5) -> bool:
        """
        Connect to the Bluetooth printer
        
        Args:
            timeout: Connection timeout in seconds
            
        Returns:
            bool: True if connection successful, False otherwise
        """
        try:
            self.socket = bluetooth.BluetoothSocket(bluetooth.RFCOMM)
            self.socket.settimeout(timeout)
            self.socket.connect((self.mac_address, self.port))
            self.connected = True
            return True
        except (bluetooth.btcommon.BluetoothError) as e:
            self.connected = False
            raise BluetoothConnectionError(f"Failed to connect to printer at {self.mac_address}. Error: {str(e)}")
    
    def disconnect(self) -> None:
        """Close the connection to the printer"""
        if self.socket and self.connected:
            self.socket.close()
            self.connected = False
    
    def print_text(self, text: str) -> bool:
        """
        Print plain text
        
        Args:
            text: Text to print
            
        Returns:
            bool: True if printing successful
        """
        if not self.connected:
            raise BluetoothConnectionError("Printer is not connected")
        
        try:
            self.socket.send(text.encode('utf-8'))
            return True
        except bluetooth.btcommon.BluetoothError as e:
            raise BluetoothConnectionError(f"Failed to print. Error: {str(e)}")
    
    def print_raw(self, data: bytes) -> bool:
        """
        Send raw data to printer
        
        Args:
            data: Raw bytes to send to printer
            
        Returns:
            bool: True if printing successful
        """
        if not self.connected:
            raise BluetoothConnectionError("Printer is not connected")
        
        try:
            self.socket.send(data)
            return True
        except bluetooth.btcommon.BluetoothError as e:
            raise BluetoothConnectionError(f"Failed to print. Error: {str(e)}")
    
    def __enter__(self):
        self.connect()
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        self.disconnect()