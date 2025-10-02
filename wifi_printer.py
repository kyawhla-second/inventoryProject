import socket
import json
import time
from typing import Dict, Any, Optional

class PrinterConnectionError(Exception):
    """Exception raised when printer connection fails"""
    pass

class WiFiPrinter:
    def __init__(self, ip_address: str, port: int = 9100):
        """
        Initialize WiFi printer connection
        
        Args:
            ip_address: IP address of the printer
            port: Port number (default is 9100 for most network printers)
        """
        self.ip_address = ip_address
        self.port = port
        self.socket = None
        self.connected = False
    
    def connect(self, timeout: int = 5) -> bool:
        """
        Connect to the WiFi printer
        
        Args:
            timeout: Connection timeout in seconds
            
        Returns:
            bool: True if connection successful, False otherwise
        """
        try:
            self.socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            self.socket.settimeout(timeout)
            self.socket.connect((self.ip_address, self.port))
            self.connected = True
            return True
        except (socket.timeout, socket.error) as e:
            self.connected = False
            raise PrinterConnectionError(f"Failed to connect to printer at {self.ip_address}:{self.port}. Error: {str(e)}")
    
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
            raise PrinterConnectionError("Printer is not connected")
        
        try:
            self.socket.sendall(text.encode('utf-8'))
            return True
        except socket.error as e:
            raise PrinterConnectionError(f"Failed to print. Error: {str(e)}")
    
    def print_raw(self, data: bytes) -> bool:
        """
        Send raw data to printer
        
        Args:
            data: Raw bytes to send to printer
            
        Returns:
            bool: True if printing successful
        """
        if not self.connected:
            raise PrinterConnectionError("Printer is not connected")
        
        try:
            self.socket.sendall(data)
            return True
        except socket.error as e:
            raise PrinterConnectionError(f"Failed to print. Error: {str(e)}")
    
    def __enter__(self):
        self.connect()
        return self
    
    def __exit__(self, exc_type, exc_val, exc_tb):
        self.disconnect()