import datetime
from dataclasses import dataclass
from typing import List, Optional

@dataclass
class InvoiceItem:
    product_id: str
    description: str
    quantity: int
    unit_price: float
    
    @property
    def total_price(self) -> float:
        return self.quantity * self.unit_price

@dataclass
class Invoice:
    invoice_id: str
    customer_name: str
    customer_address: str
    date: datetime.date
    items: List[InvoiceItem]
    tax_rate: float = 0.1  # 10% tax by default
    
    @property
    def subtotal(self) -> float:
        return sum(item.total_price for item in self.items)
    
    @property
    def tax_amount(self) -> float:
        return self.subtotal * self.tax_rate
    
    @property
    def total(self) -> float:
        return self.subtotal + self.tax_amount
    
    def to_dict(self) -> dict:
        return {
            "invoice_id": self.invoice_id,
            "customer_name": self.customer_name,
            "customer_address": self.customer_address,
            "date": self.date.strftime("%Y-%m-%d"),
            "items": [
                {
                    "product_id": item.product_id,
                    "description": item.description,
                    "quantity": item.quantity,
                    "unit_price": item.unit_price,
                    "total_price": item.total_price
                } for item in self.items
            ],
            "subtotal": self.subtotal,
            "tax_rate": self.tax_rate,
            "tax_amount": self.tax_amount,
            "total": self.total
        }