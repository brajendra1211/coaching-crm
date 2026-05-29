<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceSetting extends Model
{
    protected $fillable = [
        'invoice_title',
        'invoice_prefix',
        'default_template',
        'paper_size',
        'accent_color',
        'show_logo',
        'show_address',
        'show_phone',
        'show_email',
        'show_signature',
        'show_balance',
        'authorized_signature_label',
        'terms',
        'footer_note',
    ];

    protected $casts = [
        'show_logo' => 'boolean',
        'show_address' => 'boolean',
        'show_phone' => 'boolean',
        'show_email' => 'boolean',
        'show_signature' => 'boolean',
        'show_balance' => 'boolean',
    ];

    public static function current(): self
    {
        return self::firstOrCreate(
            ['id' => 1],
            [
                'invoice_title' => 'Fee Receipt',
                'invoice_prefix' => 'RCPT',
                'default_template' => 'global',
                'paper_size' => 'A4',
                'accent_color' => '#2563eb',
                'show_logo' => true,
                'show_address' => true,
                'show_phone' => true,
                'show_email' => true,
                'show_signature' => true,
                'show_balance' => true,
                'authorized_signature_label' => 'Authorized Signature',
                'terms' => 'Fees once paid are non-refundable. This is a computer generated receipt.',
                'footer_note' => 'Thank you for your payment.',
            ]
        );
    }
}