@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="mb-4 text-center fw-bold">Configuración y Respaldo</h2>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-download"></i> Crear Respaldo (Backup)</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Genere un archivo <code>.sql</code> con toda la información actual de la base de datos.
                            Guarde este archivo en un lugar seguro.
                        </p>
                        <a href="{{ route('backup.create') }}" class="btn btn-primary">
                            <i class="bi bi-cloud-download"></i> Descargar Respaldo Actual
                        </a>
                    </div>
                </div>

                <div class="card shadow-sm border-0 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-upload"></i> Restaurar Datos (Peligroso)</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning">
                            <strong><i class="bi bi-exclamation-triangle"></i> ADVERTENCIA:</strong>
                            Esta acción borrará los datos actuales y los reemplazará con los del archivo subido.
                            Asegúrese de tener un respaldo reciente antes de continuar.
                        </div>

                        <form action="{{ route('backup.restore') }}" method="POST" enctype="multipart/form-data"
                            onsubmit="return confirm('¿ESTÁ COMPLETAMENTE SEGURO? ESTA ACCIÓN ES IRREVERSIBLE.');">
                            @csrf
                            <div class="mb-3">
                                <label for="backup_file" class="form-label">Seleccione el archivo de respaldo (.sql)</label>
                                <input type="file" class="form-control" name="backup_file" id="backup_file" required
                                    accept=".sql">
                            </div>
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-arrow-counterclockwise"></i> Restaurar Base de Datos
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al Dashboard</a>
                </div>

            </div>
        </div>
    </div>
@endsection
