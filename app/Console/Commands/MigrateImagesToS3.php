<?php

namespace App\Console\Commands;

use App\Models\Token;
use App\Models\WorkingLady;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateImagesToS3 extends Command
{
    protected $signature   = 'images:migrate-to-s3 {--dry-run : Preview without uploading}';
    protected $description = 'Migrate locally stored images to S3 (bookdua-v2 folder)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('DRY RUN — no files will be uploaded.');
        }

        $this->migrateTokenImages($dryRun);
        $this->migrateWorkingLadyImages($dryRun);

        $this->info('Done.');
        return 0;
    }

    // ── Token user images ──────────────────────────────────────────────────────

    private function migrateTokenImages(bool $dryRun): void
    {
        $tokens = Token::whereNotNull('user_image_path')
            ->where('user_image_path', '!=', 'placeholder')
            ->where('user_image_path', 'not like', 'bookdua-v2/%')
            ->get();

        $this->info("Tokens with local images: {$tokens->count()}");

        foreach ($tokens as $token) {
            $localPath = storage_path('app/public/' . $token->user_image_path);

            if (!file_exists($localPath)) {
                $this->warn("  MISSING  {$token->user_image_path} (token {$token->id})");
                continue;
            }

            $s3Path = 'bookdua-v2/user_images/' . basename($token->user_image_path);

            if ($dryRun) {
                $this->line("  WOULD upload  {$token->user_image_path}  →  {$s3Path}");
                continue;
            }

            try {
                Storage::disk('s3')->put($s3Path, file_get_contents($localPath), 'public');
                $token->user_image_path = $s3Path;
                $token->save();
                $this->line("  ✓  {$s3Path}");
            } catch (\Exception $e) {
                $this->error("  FAILED  {$token->user_image_path}: {$e->getMessage()}");
            }
        }
    }

    // ── Working lady profile images & QR codes ────────────────────────────────

    private function migrateWorkingLadyImages(bool $dryRun): void
    {
        $ladies = WorkingLady::where(function ($q) {
            $q->whereNotNull('profile_image_path')
              ->where('profile_image_path', 'not like', 'bookdua-v2/%');
        })->orWhere(function ($q) {
            $q->whereNotNull('qr_code_path')
              ->where('qr_code_path', 'not like', 'bookdua-v2/%');
        })->get();

        $this->info("Working ladies with local images: {$ladies->count()}");

        foreach ($ladies as $lady) {
            $changed = false;

            // Profile image
            if ($lady->profile_image_path && !str_starts_with($lady->profile_image_path, 'bookdua-v2/')) {
                $localPath = storage_path('app/public/' . $lady->profile_image_path);
                $s3Path    = 'bookdua-v2/working_ladies/profile_images/' . basename($lady->profile_image_path);

                if (file_exists($localPath)) {
                    if ($dryRun) {
                        $this->line("  WOULD upload  {$lady->profile_image_path}  →  {$s3Path}");
                    } else {
                        try {
                            Storage::disk('s3')->put($s3Path, file_get_contents($localPath), 'public');
                            $lady->profile_image_path = $s3Path;
                            $changed = true;
                            $this->line("  ✓  {$s3Path}");
                        } catch (\Exception $e) {
                            $this->error("  FAILED  {$lady->profile_image_path}: {$e->getMessage()}");
                        }
                    }
                } else {
                    $this->warn("  MISSING  {$lady->profile_image_path} (lady {$lady->id})");
                }
            }

            // QR code
            if ($lady->qr_code_path && !str_starts_with($lady->qr_code_path, 'bookdua-v2/')) {
                $localPath = storage_path('app/public/' . $lady->qr_code_path);
                $s3Path    = 'bookdua-v2/qr_codes/working_ladies/' . basename($lady->qr_code_path);

                if (file_exists($localPath)) {
                    if ($dryRun) {
                        $this->line("  WOULD upload  {$lady->qr_code_path}  →  {$s3Path}");
                    } else {
                        try {
                            Storage::disk('s3')->put($s3Path, file_get_contents($localPath), 'public');
                            $lady->qr_code_path = $s3Path;
                            $changed = true;
                            $this->line("  ✓  {$s3Path}");
                        } catch (\Exception $e) {
                            $this->error("  FAILED  {$lady->qr_code_path}: {$e->getMessage()}");
                        }
                    }
                } else {
                    $this->warn("  MISSING  {$lady->qr_code_path} (lady {$lady->id})");
                }
            }

            if ($changed) {
                $lady->save();
            }
        }
    }
}
