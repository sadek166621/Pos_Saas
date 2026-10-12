<?php

namespace App\Http\Controllers;

use App\Models\SmsTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $tenant = app('currentTenant');
        $smsTemplates = SmsTemplate::where('tenant_id', $tenant->id)->get();

        return view('settings.index', compact('tenant', 'smsTemplates'));
    }

    public function updateBusiness(Request $request): RedirectResponse
    {
        $tenant = app('currentTenant');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'city' => 'required|string',
            'bin_number' => 'nullable|string|max:50',
            'tin_number' => 'nullable|string|max:50',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:5',
            'timezone' => 'required|string',
        ]);

        $tenant->update($validated);

        return back()->with('success', 'ব্যবসার সেটিংস সফলভাবে আপডেট করা হয়েছে!');
    }

    public function updateSmsTemplate(Request $request, int $id): RedirectResponse
    {
        $tenant = app('currentTenant');
        $template = SmsTemplate::where('tenant_id', $tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'template_en' => 'required|string',
            'template_bn' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $template->update([
            'template_en' => $validated['template_en'],
            'template_bn' => $validated['template_bn'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'এসএমএস টেমপ্লেট সফলভাবে আপডেট হয়েছে!');
    }
}
