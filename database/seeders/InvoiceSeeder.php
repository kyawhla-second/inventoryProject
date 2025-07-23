<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::all();
        $products = Product::all();
        $admin = User::where('role', 'admin')->first();

        if ($customers->isEmpty() || $products->isEmpty() || !$admin) {
            $this->command->info('Please ensure you have customers, products, and admin user before running this seeder.');
            return;
        }

        // Create 15 sample invoices
        for ($i = 1; $i <= 15; $i++) {
            $invoiceDate = Carbon::now()->subDays(rand(1, 90));
            $dueDate = $invoiceDate->copy()->addDays(30);
            
            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateInvoiceNumber(),
                'customer_id' => $customers->random()->id,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'tax_rate' => rand(0, 1) ? rand(5, 10) : 0,
                'discount_rate' => rand(0, 2) ? rand(5, 15) : 0,
                'notes' => rand(0, 3) == 0 ? 'Sample invoice notes for testing purposes.' : null,
                'payment_terms' => ['Net 30', 'Net 15', 'Due on Receipt'][rand(0, 2)],
                'status' => ['draft', 'sent', 'paid', 'overdue'][rand(0, 3)],
                'created_by' => $admin->id,
                'subtotal' => 0,
                'total_amount' => 0,
                'print_count' => rand(0, 3),
                'printed_at' => rand(0, 1) ? $invoiceDate->copy()->addHours(rand(1, 24)) : null,
            ]);

            // Add 1-5 items to each invoice
            $itemCount = rand(1, 5);
            for ($j = 0; $j < $itemCount; $j++) {
                $product = $products->random();
                $quantity = rand(1, 10);
                $unitPrice = $product->price * (rand(80, 120) / 100); // Vary price by ±20%

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'description' => $product->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                ]);
            }

            // Calculate totals
            $invoice->calculateTotals();
            $invoice->save();

            // Set some invoices as overdue if their due date has passed
            if ($invoice->due_date < now() && $invoice->status !== 'paid') {
                $invoice->update(['status' => 'overdue']);
            }
        }

        $this->command->info('Sample invoices created successfully!');
    }
}