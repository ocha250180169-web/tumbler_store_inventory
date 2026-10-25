<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name'];

    // Satu kategori punya banyak produk (FK category_id ada di tabel products)
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
