<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Prueba básica de disponibilidad del sistema.
 * Verifica que la ruta raíz responde correctamente.
 *
 * @version 2.5
 */
class ExampleTest extends TestCase
{
    /**
     * Verifica que el sistema redirige al login cuando no hay sesión activa.
     */
    public function test_la_aplicacion_redirige_al_login(): void
    {
        $response = $this->get('/');

        // Sin sesión activa debe redirigir al login
        $response->assertRedirect('/login');
    }
}
