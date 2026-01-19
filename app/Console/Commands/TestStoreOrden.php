<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;
use App\Http\Controllers\OrdenController;
use App\Models\Proveedor;

class TestStoreOrden extends Command
{
    protected $signature = 'orden:test-store';
    protected $description = 'Test creating an Orden by calling OrdenController::store()';

    public function handle()
    {
        // Create or get a proveedor
        $proveedor = Proveedor::first() ?? Proveedor::factory()->create(['nombre' => 'Proveedor CLI']);

        $data = [
            'fecha' => now()->toDateString(),
            'proveedor' => $proveedor->id,
            'lugar' => 'CLI Oficina',
            'solicitado' => '',
            'concepto' => 'Orden desde comando',
            'descripcion' => ['Item CLI'],
            'cantidad' => [3],
            'unidad' => ['unid'],
            'precio_unitario' => [15.75],
            'descuento' => [0]
        ];

        $request = Request::create('/orden/reponer/guardar', 'POST', $data);

        $controller = new OrdenController();

        try {
            $response = $controller->store($request);
            $this->info('Controller returned: ' . get_class($response));
            $this->info('Response: ' . (method_exists($response, 'getTargetUrl') ? $response->getTargetUrl() : 'no redirect'));
        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
