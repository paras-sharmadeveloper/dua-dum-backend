<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Token extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'venue_id',
        'venue_category_group_id',
        'venue_category_id',
        'token_number',
        'token_code',
        'user_type',
        'service_type',
        'user_name',
        'city',
        'phone_number',
        'user_image_path',
        'qr_code_path',
        'status',
        'checked_in_count',
        'print_count'
    ];

    protected $appends = ['user_image_url'];

    protected $casts = [
        'id' => 'string',
    ];

    public function getUserImageUrlAttribute(): ?string
    {
        if (!$this->user_image_path) return null;

        if (str_starts_with($this->user_image_path, 'bookdua-v2/')) {
            try {
                return Storage::disk('s3')->temporaryUrl($this->user_image_path, now()->addHours(6));
            } catch (\Exception $e) {
                // A single misconfigured/unreachable S3 disk shouldn't crash every
                // token listing or the public share page — degrade to no image.
                Log::error('Failed to generate S3 URL for token image: ' . $e->getMessage(), [
                    'token_id' => $this->id,
                    'user_image_path' => $this->user_image_path,
                ]);
                return null;
            }
        }

        return asset('storage/' . $this->user_image_path);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the venue that owns this token
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    /**
     * The face-match record for this token's booking photo, if the async
     * recognition job has finished (may briefly be null right after booking).
     */
    public function faceRecordDetail()
    {
        return $this->hasOne(FaceRecordDetail::class, 'token_id');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by user type
     */
    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    /**
     * Scope for filtering by service type
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }
}
