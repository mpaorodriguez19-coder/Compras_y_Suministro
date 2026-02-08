<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\Auth\LoginController;

/* ==============================================
   AUTENTICACIÓN (LOGIN)
============================================== */
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/setup/first-admin', [LoginController::class, 'setupFirstAdmin'])->name('setup.firstAdmin');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// RUTAS DE RECUPERACIÓN DE CONTRASEÑA
Route::get('password/reset', [App\Http\Controllers\Auth\AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\AdminResetPasswordController::class, 'reset'])->name('password.update');


/* ==============================================
   RUTAS PROTEGIDAS (SISTEMA)
   Solo accesibles si el usuario está logueado como 'admin'
============================================== */
Route::middleware(['auth:admin,web', 'check.session'])->group(function () {

    // DASHBOARD PRINCIPAL
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    /* GESTIÓN DE ÓRDENES (LEGACY & NEW) */
    Route::get('/orden/reponer', [OrdenController::class, 'index'])->name('orden.reponer'); 
    Route::get('/ordenes/crear', [OrdenController::class, 'index'])->name('ordenes.index');
    
    // NUEVAS RUTAS DE GESTIÓN
    Route::get('/ordenes/lista', [OrdenController::class, 'lista'])->name('ordenes.lista');
    Route::get('/ordenes/{id}/editar', [OrdenController::class, 'edit'])->name('ordenes.edit');
    Route::put('/ordenes/{id}', [OrdenController::class, 'update'])->name('ordenes.update');
    Route::post('/ordenes/{id}/anular', [OrdenController::class, 'anular'])->name('ordenes.anular');

    Route::post('/orden/reponer/guardar', [OrdenController::class, 'store'])->name('orden.reponer.guardar');
    Route::get('/orden/espera/{id}', [OrdenController::class, 'verEspera'])->name('orden.espera');
    Route::get('/orden/{id}/pdf', [OrdenController::class, 'pdf'])->name('orden.pdf');

    /* GESTIÓN DE PROVEEDORES */
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');

    /* GESTIÓN DE USUARIOS (Solo Super Admin puede crear/ver lista completa) */
    Route::middleware(['super.admin'])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('usuarios.store');
        Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
        
        // GESTION DE ADMINISTRADORES
        Route::get('/admins', [App\Http\Controllers\AdminController::class, 'index'])->name('admins.index');
        Route::post('/admins', [App\Http\Controllers\AdminController::class, 'store'])->name('admins.store');
        Route::put('/admins/{id}', [App\Http\Controllers\AdminController::class, 'update'])->name('admins.update');

        // BITÁCORA DE AUDITORÍA
        Route::get('/bitacora', [App\Http\Controllers\BitacoraController::class, 'index'])->name('bitacora.index');

        // RESPALDO Y RESTAURACIÓN
        Route::get('/configuracion/backup', [App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
        Route::get('/configuracion/backup/download', [App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
        Route::post('/configuracion/backup/restore', [App\Http\Controllers\BackupController::class, 'restore'])->name('backup.restore');
    });

    /* API AUTOCOMPLETADO */
    Route::get('/api/buscar-proveedores', [OrdenController::class, 'buscarProveedores'])->name('api.proveedores');
    Route::get('/api/buscar-usuarios', [OrdenController::class, 'buscarUsuarios'])->name('api.usuarios');
    Route::get('/api/rrhh/empleado', [UserController::class, 'buscarEmpleadoRRHH'])->name('api.rrhh.empleado');

    /* PANELES Y REPORTES */
    Route::get('/informe-detallado', [PanelController::class, 'informeDetallado'])->name('informe.detallado');
    Route::get('/panel/informe-detallado', [PanelController::class, 'informeDetallado']);
    
    Route::get('/compras-proveedor', [PanelController::class, 'comprasProveedor'])->name('compras.proveedor');
    Route::get('/panel/compras-proveedor', [PanelController::class, 'comprasProveedor']);
    
    Route::get('/resumen-proveedor', [PanelController::class, 'resumenProveedor'])->name('resumen.proveedor');
    Route::get('/panel/resumen-proveedor', [PanelController::class, 'resumenProveedor']);
    
    Route::get('/informe', [PanelController::class, 'informe'])->name('informe');
    
    Route::get('/transparencia', [PanelController::class, 'transparencia'])->name('transparencia');
    Route::get('/panel/transparencia', [PanelController::class, 'transparencia']);

});
