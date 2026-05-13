<?php

namespace Database\Factories;

use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * Factory para el modelo Usuario de SISGECLIN.
 * Genera usuarios de prueba con datos ficticios.
 *
 * @extends Factory<Usuario>
 * @version 2.5
 */
class UserFactory extends Factory
{
    /**
     * Modelo asociado a esta factory.
     */
    protected $model = Usuario::class;

    /**
     * Define el estado por defecto de un usuario generado.
     */
    public function definition(): array
    {
        return [
            'nombre_completo' => fake()->name(),
            'username'        => fake()->unique()->userName(),
            'email'           => fake()->unique()->safeEmail(),
            'password'        => Hash::make('password'),
            'rol'             => fake()->randomElement(['medico', 'recepcionista']),
            'activo'          => true,
        ];
    }

    /**
     * Estado: usuario desactivado (pendiente de aprobación).
     */
    public function inactivo(): static
    {
        return $this->state(fn (array $attributes) => [
            'activo' => false,
        ]);
    }
}
