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

        // Crear 50 clientes
        Cliente::factory(50)->create();

        $productos = Producto::all();

        // Crear 1000 pedidos
        Pedido::factory(1000)->create()->each(function ($pedido) use ($productos) {
            $randomProductos = $productos->random(rand(1, 5))->pluck('id');
            $pedido->productos()->attach($randomProductos);
        });
    }
}