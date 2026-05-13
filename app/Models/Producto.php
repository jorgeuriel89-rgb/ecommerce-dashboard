<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'sku', 'precio'];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_producto');
    }
}