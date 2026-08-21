<?php

namespace App\Jobs;

use App\Models\Token;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTokenApprovedWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;
    public int $timeout = 30;

    public function __construct(protected string $tokenId)
    {
    }

    public function handle(WhatsAppService $whatsAppService): void
    {
        $token = Token::with('venue')->find($this->tokenId);

        if (!$token) {
            Log::warning('SendTokenApprovedWhatsAppJob: token not found', ['token_id' => $this->tokenId]);
            return;
        }

        $whatsAppService->sendTokenApproved($token);
    }
}
