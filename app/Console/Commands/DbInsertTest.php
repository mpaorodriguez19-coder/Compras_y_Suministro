<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DbInsertTest extends Command
{
    protected $signature = 'db:test-insert';
    protected $description = 'Insert a test row into ordenes table and print ID';

    public function handle()
    {
        try {
            $id = DB::table('ordenes')->insertGetId([
                'numero' => 'TEST'.time(),
                'fecha' => now()->toDateString(),
                'subtotal' => 0,
                'descuento' => 0,
                'impuesto' => 0,
                'total' => 0,
                'estado' => 'pendiente',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info('Inserted ID: ' . $id);
        } catch (\Exception $e) {
            $this->error('Insert failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
