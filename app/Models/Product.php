<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'name',
        'description',
        'image_path',
        'price',
        'subcategory_id'
    ];

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class); // un producto pertenece a una subcategoria (uno a uno)
    }

    public function variants()
    {
        return $this->hasMany(Variant::class); // un producto puede tener muchas variantes (uno a muchos)
    }

    public function options()
    {
        return $this->belongsToMany(Option::class)
            ->withPivot('value') // recuperar el value de la tabla intermedia option_product
            ->withTimestamps(); // un producto puede tener muchas opciones (muchos a muchos)
    }
}
