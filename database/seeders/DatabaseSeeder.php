<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Pedido;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear 20 productos
        Producto::factory(20)->create();

        // Crear 100 clientes
        Cliente::factory(100)->create();

        $productos = Producto::all();
        $clientes = Cliente::all();

        // Crear 1000 pedidos asignados a clientes existentes
        Pedido::factory(1000)->make()->each(function ($pedido) use ($productos, $clientes) {
            $pedido->cliente_id = $clientes->random()->id;
            $pedido->save();
            $randomProductos = $productos->random(rand(1, 5))->pluck('id');
            $pedido->productos()->attach($randomProductos);
        });
    }
}