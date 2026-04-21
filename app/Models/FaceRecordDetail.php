<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FaceRecordDetail extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'face_record_id',
        'token_id',
        'status',
        'image_path',
        'face_encoding'
    ];

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
     * Get the face record
     */
    public function faceRecord()
    {
        return $this->belongsTo(FaceRecord::class, 'face_record_id');
    }

    /**
     * Get the token
     */
    public function token()
    {
        return $this->belongsTo(Token::class, 'token_id');
    }
}
