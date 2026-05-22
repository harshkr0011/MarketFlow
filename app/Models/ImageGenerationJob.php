<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageGenerationJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prompt',
        'status', // pending, processing, completed, failed
        'image_url',
        'error_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
