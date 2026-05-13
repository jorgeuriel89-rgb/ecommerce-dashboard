<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'total' => fake()->randomFloat(2, 100, 5000),
            'estado' => fake()->randomElement(['pendiente', 'entregado', 'cancelado']),
            'fecha_entrega' => fake()->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d'),
        ];
    }
}