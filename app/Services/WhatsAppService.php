<?php

namespace App\Services;

use App\Models\Token;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class WhatsAppService
{
    private ?Client $client = null;
    private string $from;
    private ?string $templateSid;

    public function __construct()
    {
        $sid   = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from        = 'whatsapp:+' . ltrim(config('services.twilio.whatsapp_from', ''), '+');
        $this->templateSid = config('services.twilio.template_sid');

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send the "token approved" notification to the token holder.
     * Silently logs errors so approval flow is never interrupted.
     */
    public function sendTokenApproved(Token $token): void
    {
        if (!$this->client) {
            Log::warning('WhatsApp: Twilio client not initialised — check TWILIO_ACCOUNT_SID / TWILIO_AUTH_TOKEN');
            return;
        }

        $phone = $this->normalisePhone($token->phone_number);
        if (!$phone) {
            Log::warning('WhatsApp: invalid phone number for token ' . $token->id);
            return;
        }

        $to = 'whatsapp:' . $phone;

        try {
            if ($this->templateSid) {
                // Content template (pre-approved WhatsApp template)
                $this->client->messages->create($to, [
                    'from'             => $this->from,
                    'contentSid'       => $this->templateSid,
                    'contentVariables' => json_encode($this->templateVariables($token)),
                ]);
            } else {
                // Plain-text fallback — useful until a template is approved
                $this->client->messages->create($to, [
                    'from' => $this->from,
                    'body' => $this->plainTextMessage($token),
                ]);
            }

            Log::info('WhatsApp: approved message sent to ' . $phone . ' for token ' . $token->token_number);
        } catch (\Exception $e) {
            Log::error('WhatsApp: failed to send message — ' . $e->getMessage());
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function templateVariables(Token $token): array
    {
        return [
            '1' => $token->user_name,
            '2' => $token->token_number,
            '3' => $token->venue?->venue_name ?? '',
        ];
    }

    private function plainTextMessage(Token $token): string
    {
        $typeMap = [
            'working_lady' => 'Working Lady Token',
        ];
        $typeLabel = $typeMap[$token->user_type]
            ?? (strtolower($token->service_type) === 'dum' ? 'Dum Token' : 'Dua Token');

        $showUrl = url('/show/' . $token->id);

        return implode("\n", [
            "✅ *Your Dua Token has been Approved!*",
            "",
            "Name: {$token->user_name}",
            "Token No: *{$token->token_number}*",
            "Type: {$typeLabel}",
            "Venue: " . ($token->venue?->venue_name ?? '—'),
            "",
            "View your token: {$showUrl}",
            "",
            "Please arrive at least 5 minutes early with this token ready.",
        ]);
    }

    private function normalisePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) === 10) {
            return '+92' . $digits;
        }
        if (strlen($digits) >= 11) {
            return '+' . ltrim($digits, '0');
        }
        return null;
    }
}
