<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Family extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image', 'active', 'order'];

    protected $casts = ['active' => 'boolean'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn ($m) => $m->slug ??= Str::slug($m->name));
        static::updating(fn ($m) => $m->slug = Str::slug($m->name));
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
