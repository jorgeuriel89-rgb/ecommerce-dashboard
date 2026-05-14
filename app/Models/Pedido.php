<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = ['cliente_id', 'total', 'estado', 'fecha_entrega'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'pedido_producto');
    }

    // Local Scopes
    public function scopePorEnviar($query)
    {
        return $query->where('estado', 'pendiente')
                    ->whereDate('fecha_entrega', '>=', now()->toDateString())
                    ->whereDate('fecha_entrega', '<=', now()->addDays(3)->toDateString());
    }


    public function scopeRetrasados($query)
    {
        return $query->where('estado', 'pendiente')
                     ->where('fecha_entrega', '<', now()->startOfDay());
    }

    public function scopeEntregados($query)
    {
        return $query->where('estado', 'entregado');
    }

    public function scopeCancelados($query)
    {
        return $query->where('estado', 'cancelado');
    }
}