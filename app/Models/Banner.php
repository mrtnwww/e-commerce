<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title', 'subtitle', 'image',
        'button_text', 'button_url',
        'active', 'order',
    ];

    protected $casts = ['active' => 'boolean'];

    public function getImageUrlAttribute(): string
    {
        return asset('storage/'.$this->image);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true)->orderBy('order');
    }
}
