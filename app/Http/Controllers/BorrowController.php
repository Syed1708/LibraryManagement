<?php

namespace App\Http\Controllers;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Party;
use LaravelDaily\Invoices\Classes\InvoiceItem;

use App\Models\Borrow;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    

    public function downloadPdf(Borrow $record){

        // dd($record);
        
        $client = new Party([
            'name'          => 'Roosevelt Lloyd',
            'phone'         => '(520) 318-9486',
            'custom_fields' => [
                'note'        => 'IDDQD',
                'business id' => '365#GG',
            ],
        ]);
        
        $customer = new Party([
            'name'          => $record->student->name,
            'address'       => 'The Green Street 12',
            'code'          => '#22663214',
            'custom_fields' => [
                'order number' => '> 654321 <',
            ],
        ]);
        
        $items = $record->items->map(function ($item) {
            return InvoiceItem::make($item->book->title)
                ->pricePerUnit($item->fees)
                ->quantity($item->qty);
        })->toArray();

        

        
        $notes = [
            'your multiline',
            'additional notes',
            'in regards of delivery or something else',
        ];
        $notes = implode("<br>", $notes);
        
        $invoice = Invoice::make('receipt')
            ->series('BIG')
            ->template('my_company')
            // ability to include translated invoice status
            // in case it was paid
            ->status(__('invoices::invoice.paid'))
            ->sequence(667)
            ->serialNumberFormat('{SEQUENCE}/{SERIES}')
            ->seller($client)
            ->buyer($customer)
            ->date(now()->subWeeks(3))
            ->dateFormat('m/d/Y')
            ->payUntilDays(14)
            ->currencySymbol('$')
            ->currencyCode('USD')
            ->currencyFormat('{SYMBOL}{VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($client->name . ' ' . $customer->name)
            ->addItems($items)
            ->notes($notes)
            ->shipping($record->late_fee)
            ->logo(public_path('vendor/invoices/sample-logo.png'))
            // You can additionally save generated invoice to configured disk
            ->save('public');
        
        $link = $invoice->url();
        // Then send email to party with link
        
        // And return invoice itself to browser or have a different view
        return $invoice->stream();

    }
}
