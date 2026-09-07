<?php

include '../config/database.php';
session_start();

$clientes = $conexion->query('SELECT id, nombre FROM clientes ORDER BY nombre')
    ->fetchAll(PDO::FETCH_ASSOC);
$productos = $conexion->query('SELECT id, nombre, unidad_medida, stock, precio FROM productos WHERE stock > 0 ORDER BY nombre')
    ->fetchAll(PDO::FETCH_ASSOC);
$bloques = $conexion->query(
    'SELECT id, anio, mes, saldo_actual, unidad_medida FROM bloques_reembarque WHERE activo = 1 ORDER BY anio DESC, mes DESC'
)->fetchAll(PDO::FETCH_ASSOC);

$mensaje = $_SESSION['mensaje'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['mensaje'], $_SESSION['error']);

function e($value): string { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar salida</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tipo-salida-card { cursor:pointer; transition:.2s; }
        .tipo-salida-card:hover, .tipo-salida-card.seleccionada { border-color:#0d6efd; background:#f0f6ff; }
        .campo-requerido::after { content:' *'; color:#dc3545; }
        .importe, .solo-lectura { background:#f8f9fa; font-weight:600; }
    </style>
</head>
<body class="bg-light">
<main class="container my-5">
    <?php if ($mensaje): ?><div class="alert alert-success"><?= e($mensaje) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>

    <form action="guardar_salida.php" method="post" id="formSalida">
        <section class="card shadow mb-4">
            <div class="card-header bg-primary text-white"><h4 class="mb-0">Registrar salida</h4></div>
            <div class="card-body">
                <p class="text-muted">Seleccione el documento que desea registrar.</p>
                <div class="row g-3">
                    <div class="col-md-6"><label id="cardNormal" class="card h-100 tipo-salida-card"><div class="card-body">
                        <input class="form-check-input" type="radio" name="tipo" value="normal" required> <strong>Nota normal</strong>
                        <p class="text-muted mb-0 mt-2">Para público local y ventas generales.</p>
                    </div></label></div>
                    <div class="col-md-6"><label id="cardRemision" class="card h-100 tipo-salida-card"><div class="card-body">
                        <input class="form-check-input" type="radio" name="tipo" value="remision_forestal" required> <strong>Reembarque forestal</strong>
                        <p class="text-muted mb-0 mt-2">Salida con un producto y saldo encadenado por bloque mensual.</p>
                    </div></label></div>
                </div>
            </div>
        </section>

        <section id="formNotaNormal" class="card shadow mb-4 d-none">
            <div class="card-header bg-primary text-white"><h5 class="mb-0">Datos de la nota normal</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3"><label class="form-label campo-requerido">Número de nota</label><input name="numero_nota" class="form-control campo-normal" maxlength="30" disabled></div>
                    <div class="col-md-5 mb-3"><label class="form-label campo-requerido">Nombre del cliente</label><input name="nombre_cliente" class="form-control campo-normal" maxlength="150" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Teléfono</label><input name="telefono" class="form-control campo-normal" maxlength="30" disabled></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Dirección</label><input name="direccion" class="form-control campo-normal" maxlength="200" disabled></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Ciudad</label><input name="ciudad" class="form-control campo-normal" maxlength="100" disabled></div>
                </div>
                <hr><h6>Productos</h6>
                <div class="table-responsive"><table class="table"><thead><tr><th>Cantidad</th><th>Producto</th><th>Precio</th><th>Importe</th><th></th></tr></thead>
                <tbody id="detalleProductos"></tbody></table></div>
                <button class="btn btn-outline-primary" type="button" id="agregarProducto">+ Agregar producto</button>
            </div>
        </section>

        <section id="formRemisionForestal" class="card shadow mb-4 d-none">
            <div class="card-header bg-success text-white"><h5 class="mb-0">Datos del reembarque forestal</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label campo-requerido">Cliente</label><select name="cliente_id" class="form-select campo-remision" disabled><option value="">Seleccione un cliente</option><?php foreach ($clientes as $cliente): ?><option value="<?= (int) $cliente['id'] ?>"><?= e($cliente['nombre']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label campo-requerido">Bloque mensual</label><select name="bloque_reembarque_id" id="bloqueReembarque" class="form-select campo-remision" disabled><option value="">Seleccione el bloque</option><?php foreach ($bloques as $bloque): ?><option value="<?= (int) $bloque['id'] ?>" data-saldo="<?= e($bloque['saldo_actual']) ?>" data-unidad="<?= e($bloque['unidad_medida']) ?>"><?= e(sprintf('%02d/%d', $bloque['mes'], $bloque['anio'])) ?> — saldo <?= e($bloque['saldo_actual']) ?> <?= e($bloque['unidad_medida']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label campo-requerido">Folio progresivo</label><input name="folio_progresivo" class="form-control campo-remision" maxlength="50" disabled><div class="form-text">Captúralo tal como aparece en el documento oficial.</div></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Folio autorizado</label><input name="folio_autorizado" class="form-control campo-remision" maxlength="50" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Fecha de vencimiento</label><input name="fecha_vencimiento" type="date" class="form-control campo-remision" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label campo-requerido">Destinatario</label><input name="destinatario" class="form-control campo-remision" maxlength="180" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Domicilio del destino</label><input name="domicilio_destino" class="form-control campo-remision" maxlength="200" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Municipio</label><input name="municipio" class="form-control campo-remision" maxlength="100" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Estado</label><input name="entidad" class="form-control campo-remision" maxlength="100" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Medio de transporte</label><input name="medio_transporte" class="form-control campo-remision" maxlength="100" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Marca del vehículo</label><input name="marca_vehiculo" class="form-control campo-remision" maxlength="100" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Tipo de vehículo</label><input name="tipo_vehiculo" class="form-control campo-remision" maxlength="100" disabled></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Placas</label><input name="placas" class="form-control campo-remision" maxlength="50" disabled></div>
                </div>
                <hr><h6>Producto amparado</h6>
                <div class="row align-items-end">
                    <div class="col-md-5 mb-3"><label class="form-label campo-requerido">Producto</label><select id="productoRemision" name="producto_id_remision" class="form-select campo-remision" disabled><option value="">Seleccione un producto</option><?php foreach ($productos as $producto): ?><option value="<?= (int) $producto['id'] ?>" data-precio="<?= e($producto['precio']) ?>" data-stock="<?= e($producto['stock']) ?>" data-unidad="<?= e($producto['unidad_medida']) ?>"><?= e($producto['nombre']) ?> — Stock: <?= e($producto['stock']) ?> <?= e($producto['unidad_medida']) ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-3 mb-3"><label class="form-label campo-requerido">Cantidad que ampara</label><input id="cantidadRemision" name="cantidad_remision" type="number" step="0.001" min="0.001" class="form-control campo-remision" disabled></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Precio</label><input id="precioRemision" class="form-control solo-lectura" readonly></div>
                    <div class="col-md-2 mb-3"><label class="form-label">Importe</label><input id="importeRemision" class="form-control importe" readonly></div>
                </div>
                <div class="row"><div class="col-md-6 mb-3"><label class="form-label">Saldo disponible (documento anterior)</label><input id="saldoDisponible" class="form-control solo-lectura" readonly></div><div class="col-md-6 mb-3"><label class="form-label">Saldo que pasa al siguiente documento</label><input id="saldoSiguiente" class="form-control solo-lectura" readonly></div></div>
            </div>
        </section>

        <section id="datosComunes" class="card shadow mb-4 d-none"><div class="card-body"><div class="row"><div class="col-md-3 mb-3"><label class="form-label campo-requerido">Fecha</label><input name="fecha" type="date" value="<?= date('Y-m-d') ?>" class="form-control campo-comun" disabled></div><div class="col-md-9 mb-3"><label class="form-label">Observaciones</label><textarea name="observaciones" class="form-control campo-comun" rows="2" disabled></textarea></div></div><button class="btn btn-success" type="submit">Guardar salida</button></div></section>
    </form>
</main>
<script>
const productos = <?= json_encode($productos, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
const formNormal = document.getElementById('formNotaNormal'), formRemision = document.getElementById('formRemisionForestal'), comunes = document.getElementById('datosComunes');
const setDisabled = (selector, disabled) => document.querySelectorAll(selector).forEach(el => el.disabled = disabled);
function activar(tipo) {
    const normal = tipo === 'normal';
    formNormal.classList.toggle('d-none', !normal); formRemision.classList.toggle('d-none', normal); comunes.classList.remove('d-none');
    setDisabled('.campo-normal', !normal); setDisabled('.campo-remision', normal); setDisabled('.campo-comun', false);
    document.getElementById('cardNormal').classList.toggle('seleccionada', normal); document.getElementById('cardRemision').classList.toggle('seleccionada', !normal);
    if (normal && !document.querySelector('#detalleProductos tr')) agregarFila();
}
document.querySelectorAll('input[name="tipo"]').forEach(r => r.addEventListener('change', () => activar(r.value)));
function opcionesProductos() { return '<option value="">Seleccione un producto</option>' + productos.map(p => `<option value="${p.id}" data-precio="${p.precio}" data-stock="${p.stock}" data-unidad="${p.unidad_medida}">${p.nombre} — Stock: ${p.stock} ${p.unidad_medida}</option>`).join(''); }
function agregarFila() {
    const tr = document.createElement('tr'); tr.innerHTML = `<td><input name="cantidad[]" type="number" step="0.001" min="0.001" class="form-control cantidad" required></td><td><select name="producto_id[]" class="form-select producto" required>${opcionesProductos()}</select></td><td><input class="form-control precio solo-lectura" readonly></td><td><input class="form-control importe" readonly></td><td><button type="button" class="btn btn-outline-danger eliminar">Eliminar</button></td>`;
    document.getElementById('detalleProductos').appendChild(tr); enlazarFila(tr);
}
function enlazarFila(tr) { tr.querySelectorAll('.cantidad,.producto').forEach(x => x.addEventListener('input', () => calcularFila(tr))); tr.querySelector('.eliminar').addEventListener('click', () => { if (document.querySelectorAll('#detalleProductos tr').length > 1) tr.remove(); }); }
function calcularFila(tr) { const op = tr.querySelector('.producto').selectedOptions[0], cant = Number(tr.querySelector('.cantidad').value || 0), precio = Number(op?.dataset.precio || 0); tr.querySelector('.precio').value = precio.toFixed(2); tr.querySelector('.importe').value = (cant * precio).toFixed(2); }
document.getElementById('agregarProducto').addEventListener('click', agregarFila);
const bloque = document.getElementById('bloqueReembarque'), productoR = document.getElementById('productoRemision'), cantidadR = document.getElementById('cantidadRemision');
function calcularRemision() { const b = bloque.selectedOptions[0], p = productoR.selectedOptions[0], saldo = Number(b?.dataset.saldo || 0), cant = Number(cantidadR.value || 0), precio = Number(p?.dataset.precio || 0), unidad = b?.dataset.unidad || ''; document.getElementById('saldoDisponible').value = b ? `${saldo.toFixed(3)} ${unidad}` : ''; document.getElementById('saldoSiguiente').value = b ? `${Math.max(0, saldo - cant).toFixed(3)} ${unidad}` : ''; document.getElementById('precioRemision').value = p ? precio.toFixed(2) : ''; document.getElementById('importeRemision').value = p ? (cant * precio).toFixed(2) : ''; if (p) cantidadR.max = Math.min(b ? saldo : Number.MAX_SAFE_INTEGER, Number(p.dataset.stock || 0)); }
[bloque, productoR, cantidadR].forEach(el => el.addEventListener('input', calcularRemision));
document.getElementById('formSalida').addEventListener('submit', e => { if (document.querySelector('input[name="tipo"]:checked')?.value === 'remision_forestal') { const saldo = Number(bloque.selectedOptions[0]?.dataset.saldo || 0); if (Number(cantidadR.value) > saldo) { e.preventDefault(); alert('La cantidad amparada no puede exceder el saldo disponible.'); } } });
</script>
</body>
</html>
