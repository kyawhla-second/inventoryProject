# Bluetooth and WiFi Printer Integration Guide

This document provides comprehensive information on using and extending the Bluetooth and WiFi printer functionality in the Invoice System.

## Table of Contents

- [User Guide](#user-guide)
  - [Setup Requirements](#setup-requirements)
  - [Connecting to Printers](#connecting-to-printers)
  - [Printing Invoices](#printing-invoices)
  - [Troubleshooting](#troubleshooting)
- [Developer Guide](#developer-guide)
  - [Architecture Overview](#architecture-overview)
  - [Adding New Printer Types](#adding-new-printer-types)
  - [Extending Printer Functionality](#extending-printer-functionality)
  - [Testing Printer Integration](#testing-printer-integration)

---

## User Guide

### Setup Requirements

#### WiFi Printer Setup

1. **Hardware Requirements**:
   - WiFi-enabled printer
   - Printer and computer on same network
   - Stable network connection

2. **Printer Configuration**:
   - Ensure printer has a static IP address
   - Enable raw printing on port 9100 (if available)
   - Check printer documentation for specific settings

3. **Finding Your Printer's IP Address**:
   - Check printer display menu (if available)
   - Print network configuration page
   - Check router's connected devices list
   - Use printer manufacturer's utility software

#### Bluetooth Printer Setup

1. **Hardware Requirements**:
   - Bluetooth-enabled printer
   - Computer with Bluetooth capability
   - Bluetooth adapter (if computer lacks built-in Bluetooth)

2. **Pairing Process**:
   - Turn on printer and set to discoverable mode
   - Enable Bluetooth on computer
   - Open Bluetooth settings and select "Add device"
   - Select your printer from the list
   - Follow pairing instructions (may require PIN)
   - Note: Complete pairing before using the application

3. **Finding Your Printer's MAC Address**:
   - Check printer documentation
   - View in Bluetooth settings after pairing
   - Print configuration page from printer

### Connecting to Printers

#### Configuring WiFi Printer

1. Open `invoice_demo.py` in a text editor
2. Locate the `print_with_wifi` function
3. Update the `printer_ip` variable with your printer's IP address:
   ```python
   printer_ip = "192.168.1.100"  # Replace with your printer's IP