<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Proveedor;

class OrdenStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_order_with_one_item()
    {
        // Create a provider to reference (optional)
        $proveedor = Proveedor::factory()->create();

        $response = $this->post(route('orden.reponer.guardar'), [
            'fecha' => now()->toDateString(),
            'proveedor' => $proveedor->id,
            'lugar' => 'Oficina Central',
            'solicitado' => '',
            'concepto' => 'Compra de prueba',
            'descripcion' => ['Item prueba'],
            'cantidad' => [2],
            'unidad' => ['unid'],
            'precio_unitario' => [10.5],
            'descuento' => [0]
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('ordenes', [
            'lugar' => 'Oficina Central',
            'concepto' => 'Compra de prueba'
        ]);

        $this->assertDatabaseHas('orden_items', [
            'descripcion' => 'Item prueba',
            'cantidad' => 2,
        ]);
    }
}
