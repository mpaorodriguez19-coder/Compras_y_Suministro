@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div
                            class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                            <h6 class="text-white text-capitalize ps-3">Configuración del Sistema</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="p-4">
                            @if (session('success'))
                                <div class="alert alert-success text-white" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="alert alert-danger text-white" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger text-white">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('configuracion.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <h5 class="mb-3 text-primary">Firmas en Órdenes de Compra</h5>
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <h6 class="text-muted">Firma Izquierda (Solicitante/Jefe)</h6>
                                        <div class="input-group input-group-outline mb-3 is-filled">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="firma_oc_nombre_1" class="form-control"
                                                value="{{ $configs['firma_oc_nombre_1']->value ?? '' }}">
                                        </div>
                                        <div class="input-group input-group-outline mb-3 is-filled">
                                            <label class="form-label">Puesto / Cargo</label>
                                            <input type="text" name="firma_oc_puesto_1" class="form-control"
                                                value="{{ $configs['firma_oc_puesto_1']->value ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted">Firma Centro (Administración)</h6>
                                        <div class="input-group input-group-outline mb-3 is-filled">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="firma_oc_nombre_2" class="form-control"
                                                value="{{ $configs['firma_oc_nombre_2']->value ?? '' }}">
                                        </div>
                                        <div class="input-group input-group-outline mb-3 is-filled">
                                            <label class="form-label">Puesto / Cargo</label>
                                            <input type="text" name="firma_oc_puesto_2" class="form-control"
                                                value="{{ $configs['firma_oc_puesto_2']->value ?? '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-muted">Firma Derecha (Alcalde)</h6>
                                        <div class="input-group input-group-outline mb-3 is-filled">
                                            <label class="form-label">Nombre</label>
                                            <input type="text" name="firma_oc_nombre_3" class="form-control"
                                                value="{{ $configs['firma_oc_nombre_3']->value ?? '' }}">
                                        </div>
                                        <div class="input-group input-group-outline mb-3 is-filled">
                                            <label class="form-label">Puesto / Cargo</label>
                                            <input type="text" name="firma_oc_puesto_3" class="form-control"
                                                value="{{ $configs['firma_oc_puesto_3']->value ?? '' }}">
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <h5 class="mb-3 text-primary">Secuencia de Documentos</h5>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted">Contador de Órdenes de Compra</h6>
                                        <p class="text-sm">El siguiente número de orden que se generará es el:
                                            <strong>{{ $nextId }}</strong>
                                        </p>

                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Establecer siguiente número (Opcional)</label>
                                            <input type="number" name="next_oc_id" class="form-control">
                                        </div>
                                        <small class="text-warning">
                                            ⚠️ Advertencia: Solo modifique este valor si necesita saltar la numeración. No
                                            puede ser menor al actual.
                                        </small>
                                    </div>

                                    <div class="col-md-6">
                                        <h6 class="text-muted">Historial de Cambios de Secuencia</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                              <style>
.input-group.input-group-outline .form-control {
    padding-top: 1.25rem !important;
}
.input-group.input-group-outline .form-label {
    top: -0.65rem !important;
    font-size: 1.00rem !important;
    background: #fff;
    padding: 0 10px;
}
</style>  
                                                <thead>
                                                    <tr>
                                                        <th>Fecha</th>
                                                        <th>Usuario</th>
                                                        <th>Ant.</th>
                                                        <th>Nuevo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($historial as $cambio)
                                                        <tr>
                                                            <td>{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                                                            <td>{{ $cambio->user_name ?? ($cambio->user->name ?? 'Sistema') }}
                                                            </td>
                                                            <td>{{ $cambio->valor_anterior }}</td>
                                                            <td>{{ $cambio->valor_nuevo }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-sm">Sin cambios
                                                                registrados</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                            {{ $historial->links() }}
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
