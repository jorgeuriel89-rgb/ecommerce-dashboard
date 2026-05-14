<?php

namespace App\Console\Commands;

use App\Models\Pedido;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AplicarCargoExpres extends Command
{
    protected $signature = 'pedidos:cargo-expres';
    protected $description = 'Aplica un recargo del 10% a pedidos pendientes con entrega mañana que incluyan el producto de Manejo Especial (id=5)';

    public function handle()
    {
        $mañana = now()->addDay()->toDateString();

        $ids = Pedido::where('estado', 'pendiente')
            ->whereDate('fecha_entrega', $mañana)
            ->whereHas('productos', function ($query) {
                $query->where('productos.id', 5);
            })
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->info('No hay pedidos que aplicar el recargo hoy.');
            return;
        }

        Pedido::whereIn('id', $ids)->update([
            'total' => DB::raw('total * 1.10')
        ]);

        $this->info("Recargo aplicado a {$ids->count()} pedidos.");
    }
}