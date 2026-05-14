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
        $clientes = Cliente::pluck('id');

        // Crear 1000 pedidos asignados a clientes existentes
        for ($i = 0; $i < 1000; $i++) {
            $pedido = Pedido::create([
                'cliente_id' => $clientes->random(),
                'total' => fake()->randomFloat(2, 100, 5000),
                'estado' => fake()->randomElement(['pendiente', 'entregado', 'cancelado']),
                'fecha_entrega' => fake()->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
            ]);

            $pedido->productos()->attach(
                $productos->random(rand(1, 5))->pluck('id')
            );
        }
    }
}