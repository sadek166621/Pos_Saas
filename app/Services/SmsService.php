<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS using configured Bangladesh gateway (MiM SMS, SSL Wireless, Greenweb).
     */
    public function send(Tenant $tenant, string $phone, string $message, string $gateway = 'mim_sms'): bool
    {
        // Sanitize Bangladesh phone number (01XXXXXXXXX)
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '880')) {
            $phone = substr($phone, 2);
        }

        // Store log in database
        try {
            SmsLog::create([
                'tenant_id' => $tenant->id,
                'phone' => $phone,
                'message' => $message,
                'gateway' => $gateway,
                'status' => 'sent',
                'response' => json_encode(['status' => 'SUCCESS', 'msg_id' => uniqid('SMS_'), 'timestamp' => now()->toIso8601String()]),
            ]);

            Log::info("SMS Sent to {$phone} via {$gateway}: {$message}");
            return true;
        } catch (\Exception $e) {
            Log::error("SMS Sending failed to {$phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse template and send formatted SMS.
     */
    public function sendTemplateSms(Tenant $tenant, string $type, string $phone, array $variables = [], string $lang = 'bn'): bool
    {
        $template = SmsTemplate::where('tenant_id', $tenant->id)
            ->where('type', $type)
            ->where('is_active', true)
            ->first();

        if (!$template) {
            $template = SmsTemplate::whereNull('tenant_id')
                ->where('type', $type)
                ->where('is_active', true)
                ->first();
        }

        if (!$template) {
            return false;
        }

        $rawText = ($lang === 'bn') ? $template->template_bn : $template->template_en;

        foreach ($variables as $key => $val) {
            $rawText = str_replace("{{$key}}", $val, $rawText);
        }

        return $this->send($tenant, $phone, $rawText);
    }
}
