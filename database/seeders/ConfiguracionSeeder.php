<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'key' => 'firma_oc_nombre_1',
                'value' => 'Jefe de Compras y Suministros',
                'label' => 'Nombre Firma 1 (OC)',
                'type' => 'text'
            ],
            [
                'key' => 'firma_oc_puesto_1',
                'value' => 'Jefe de Departamento',
                'label' => 'Puesto Firma 1 (OC)',
                'type' => 'text'
            ],
            [
                'key' => 'firma_oc_nombre_2',
                'value' => 'Administrador',
                'label' => 'Nombre Firma 2 (OC)',
                'type' => 'text'
            ],
            [
                'key' => 'firma_oc_puesto_2',
                'value' => 'Gerencia Administrativa',
                'label' => 'Puesto Firma 2 (OC)',
                'type' => 'text'
            ],
        ];

        foreach ($configs as $config) {
            \App\Models\Configuracion::updateOrCreate(
                ['key' => $config['key']],
                $config
            );
        }
    }
}
