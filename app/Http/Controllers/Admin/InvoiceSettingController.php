<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvoiceSetting;
use Illuminate\Http\Request;

class InvoiceSettingController extends Controller
{
    public function edit()
    {
        $invoiceSetting = InvoiceSetting::current();

        return view('admin.invoice-settings.edit', compact('invoiceSetting'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'invoice_title' => ['required', 'string', 'max:255'],
            'invoice_prefix' => ['required', 'string', 'max:50'],
            'default_template' => ['required', 'in:global,premium,letterhead,minimal,gst,compact-slip'],
            'paper_size' => ['required', 'in:A4,A5'],
            'accent_color' => ['required', 'string', 'max:20'],

            'authorized_signature_label' => ['nullable', 'string', 'max:255'],
            'terms' => ['nullable', 'string'],
            'footer_note' => ['nullable', 'string'],

            'show_logo' => ['nullable'],
            'show_address' => ['nullable'],
            'show_phone' => ['nullable'],
            'show_email' => ['nullable'],
            'show_signature' => ['nullable'],
            'show_balance' => ['nullable'],
        ]);

        $data['show_logo'] = $request->boolean('show_logo');
        $data['show_address'] = $request->boolean('show_address');
        $data['show_phone'] = $request->boolean('show_phone');
        $data['show_email'] = $request->boolean('show_email');
        $data['show_signature'] = $request->boolean('show_signature');
        $data['show_balance'] = $request->boolean('show_balance');

        InvoiceSetting::current()->update($data);

        return redirect()
            ->route('admin.invoice-settings.edit')
            ->with('success', 'Invoice settings updated successfully.');
    }
}