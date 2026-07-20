<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPaymentSettingController extends Controller
{
    public function edit()
    {
        $setting = PaymentSetting::first();

        return view('admin.payment-settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'bep20_address' => ['required', 'string', 'max:100'],
            'barcode' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $setting = PaymentSetting::first() ?? new PaymentSetting();
        $setting->bep20_address = $validated['bep20_address'];
        $setting->updated_by = $request->user()->id;

        if ($request->hasFile('barcode')) {
            if ($setting->barcode_path) {
                Storage::disk('public')->delete($setting->barcode_path);
            }

            $setting->barcode_path = $request->file('barcode')->store('payment-barcodes', 'public');
        }

        $setting->save();

        return back()->with('status', 'Payment receiving wallet updated.');
    }
}
