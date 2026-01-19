<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Compra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">

<h4 class="text-center mb-4">ORDEN DE COMPRA</h4>

<form method="POST" action="{{ route('orden.reponer.guardar') }}">
@csrf

<div class="row mb-3">
    <div class="col-md-3">
        <label>Fecha</label>
        <input type="date" name="fecha" class="form-control" required>
    </div>

    <div class="col-md-5">
        <label>Proveedor</label>
        <input type="text" name="proveedor" class="form-control" required>
    </div>

    <div class="col-md-4">
        <label>Lugar</label>
        <input type="text" name="lugar" class="form-control">
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <label>Solicitado por</label>
        <input type="text" name="solicitado" class="form-control">
    </div>

    <div class="col-md-6">
        <label>Concepto</label>
        <textarea name="concepto" class="form-control"></textarea>
    </div>
</div>

<table class="table table-bordered">
<thead class="table-light text-center">
<tr>
    <th>Cant</th>
    <th>Descripción</th>
    <th>Unidad</th>
    <th>Precio</th>
    <th>Descuento</th>
</tr>
</thead>
<tbody>
<tr>
    <td><input type="number" name="cantidad[]" class="form-control" required></td>
    <td><input type="text" name="descripcion[]" class="form-control" required></td>
    <td><input type="text" name="unidad[]" class="form-control"></td>
    <td><input type="number" name="precio_unitario[]" step="0.01" class="form-control" required></td>
    <td><input type="number" name="descuento[]" step="0.01" class="form-control"></td>
</tr>
</tbody>
</table>

<div class="text-center">
    <button type="submit" class="btn btn-success px-4">Guardar y Generar PDF</button>
</div>

</form>
</body>
</html>
