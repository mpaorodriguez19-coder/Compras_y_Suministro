<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupController extends Controller
{
    /**
     * Muestra la vista de configuración de respaldos.
     */
    public function index()
    {
        return view('configuracion.backup');
    }

    /**
     * Genera un respaldo de la base de datos y lo descarga.
     */
    public function create()
    {
        $filename = "backup-" . Carbon::now()->format('Y-m-d-H-i-s') . ".sql";
        $handle = fopen('php://memory', 'r+');

        // 1. Obtener todas las tablas
        $tables = DB::select('SHOW TABLES');
        $dbName = env('DB_DATABASE');
        $tablesKey = "Tables_in_" . $dbName;

        fwrite($handle, "-- Respaldo Generado: " . Carbon::now() . "\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

        foreach ($tables as $tableRow) {
            $table = $tableRow->$tablesKey;
            
            // Estructura de la tabla
            $createTable = DB::select("SHOW CREATE TABLE `$table`");
            fwrite($handle, "DROP TABLE IF EXISTS `$table`;\n");
            fwrite($handle, $createTable[0]->{'Create Table'} . ";\n\n");

            // Datos de la tabla
            $rows = DB::table($table)->get();
            foreach ($rows as $row) {
                $values = array_map(function ($value) {
                    if (is_null($value)) return "NULL";
                    return "'" . addslashes($value) . "'";
                }, (array) $row);
                
                fwrite($handle, "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n");
            }
            fwrite($handle, "\n");
        }

        fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename);
    }

    /**
     * Restaura la base de datos desde un archivo SQL.
     */
    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,txt',
        ]);

        try {
            $file = $request->file('backup_file');
            $sql = file_get_contents($file->getRealPath());

            // Ejecutar el SQL (DB::unprepared puede ejecutar múltiples sentencias)
            DB::unprepared($sql);

            return back()->with('success', 'Base de datos restaurada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }
}
