<?php

namespace App\Jobs;

use App\Services\FaceRecognitionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessFaceRecognitionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;
    public int $timeout = 60;

    public function __construct(
        protected string $userImage,
        protected string $userName,
        protected string $tokenId,
    ) {
    }

    public function handle(FaceRecognitionService $faceRecognitionService): void
    {
        try {
            // Always use userImage as imagePath
            $imagePath = $this->userImage;

            // Extract base64 if image is stored as data URI
            $imageBase64 = $this->userImage;
            if (strpos($this->userImage, 'data:image') === 0) {
                $imageBase64 = explode(',', $this->userImage)[1];
            } elseif (file_exists(storage_path('app/public/' . $this->userImage))) {
                // If it's a file path, read and encode it
                $imageContent = file_get_contents(storage_path('app/public/' . $this->userImage));
                $imageBase64 = base64_encode($imageContent);
            } elseif (file_exists(public_path($this->userImage))) {
                // Assume it's already a path stored in DB
                $imageContent = file_get_contents(public_path($this->userImage));
                $imageBase64 = base64_encode($imageContent);
            }

            $faceRecognitionService->recognizeFace($imageBase64, $this->userName, $this->tokenId, $imagePath);
        } catch (\Exception $e) {
            Log::error('Face recognition job failed: ' . $e->getMessage(), ['token_id' => $this->tokenId]);
        }
    }
}
