<?php

include '../config/database.php';
session_start();

/*
|--------------------------------------------------------------------------
| Consultar clientes
|--------------------------------------------------------------------------
*/

$clientes = $conexion
    ->query("
        SELECT id, nombre
        FROM clientes
        ORDER BY nombre
    ")
    ->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Consultar productos
|--------------------------------------------------------------------------
*/

$productos = $conexion
    ->query("
        SELECT
            id,
            nombre,
            tipo,
            unidad_medida,
            stock,
            precio
        FROM productos
        ORDER BY nombre
    ")
    ->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Mensajes
|--------------------------------------------------------------------------
*/

$mensaje = $_SESSION['mensaje'] ?? null;
$error = $_SESSION['error'] ?? null;

unset(
    $_SESSION['mensaje'],
    $_SESSION['error']
);

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Registrar salida</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        .tipo-salida-card {
            cursor: pointer;
            transition:
                border-color 0.2s,
                background-color 0.2s,
                transform 0.2s;
        }

        .tipo-salida-card:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
        }

        .tipo-salida-card.seleccionada {
            border: 2px solid #0d6efd;
            background-color: #f0f6ff;
        }

        .campo-requerido::after {
            content: " *";
            color: #dc3545;
        }

        .tabla-productos th {
            white-space: nowrap;
        }

        .importe {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        #total {
            background-color: #f8f9fa;
            font-size: 1.3rem;
        }

        .stock-disponible {
            font-size: 0.9rem;
            color: #198754;
            margin-top: 5px;
        }

        .producto-remision-container {
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            background-color: #fff;
        }

    </style>

</head>


<body class="bg-light">


<div class="container my-5">


    <?php if ($mensaje): ?>

        <div class="alert alert-success alert-dismissible fade show">

            <?= htmlspecialchars(
                $mensaje,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar"
            ></button>

        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="alert alert-danger alert-dismissible fade show">

            <?= htmlspecialchars(
                $error,
                ENT_QUOTES,
                'UTF-8'
            ) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Cerrar"
            ></button>

        </div>

    <?php endif; ?>


    <form
        action="guardar_salida.php"
        method="POST"
        id="formSalida"
    >


        <!-- ==========================================================
             SELECCIÓN DEL TIPO DE SALIDA
        =========================================================== -->

        <div class="card shadow mb-4">

            <div class="card-header bg-primary text-white">

                <h4 class="mb-0">
                    Registrar salida
                </h4>

            </div>


            <div class="card-body">

                <h5>
                    Seleccione el tipo de salida
                </h5>

                <p class="text-muted">
                    El formulario cambiará según el documento seleccionado.
                </p>


                <div class="row g-3">


                    <!-- NOTA NORMAL -->

                    <div class="col-md-6">

                        <label
                            for="tipoNormal"
                            id="cardNormal"
                            class="card h-100 tipo-salida-card"
                        >

                            <div class="card-body">

                                <div class="form-check">

                                    <input
                                        type="radio"
                                        name="tipo"
                                        id="tipoNormal"
                                        value="normal"
                                        class="form-check-input"
                                        required
                                    >

                                    <span class="form-check-label">

                                        <strong>
                                            Nota normal
                                        </strong>

                                    </span>

                                </div>


                                <p class="text-muted mt-2 mb-0">

                                    Para público local y ventas generales.

                                </p>

                            </div>

                        </label>

                    </div>


                    <!-- REMISIÓN FORESTAL -->

                    <div class="col-md-6">

                        <label
                            for="tipoRemision"
                            id="cardRemision"
                            class="card h-100 tipo-salida-card"
                        >

                            <div class="card-body">

                                <div class="form-check">

                                    <input
                                        type="radio"
                                        name="tipo"
                                        id="tipoRemision"
                                        value="remision_forestal"
                                        class="form-check-input"
                                        required
                                    >

                                    <span class="form-check-label">

                                        <strong>
                                            Remisión forestal
                                        </strong>

                                    </span>

                                </div>


                                <p class="text-muted mt-2 mb-0">

                                    Para clientes que requieren documentación
                                    forestal para transportar el producto.

                                </p>

                            </div>

                        </label>

                    </div>

                </div>

            </div>

        </div>



        <!-- ==========================================================
             DATOS DE LA NOTA NORMAL
        =========================================================== -->

        <div
            id="formNotaNormal"
            class="card shadow mb-4 d-none"
        >

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    Datos de la nota normal
                </h5>

            </div>


            <div class="card-body">

                <div class="row">


                    <div class="col-md-3 mb-3">

                        <label
                            for="numeroNota"
                            class="form-label campo-requerido"
                        >
                            Número de nota
                        </label>

                        <input
                            type="text"
                            name="numero_nota"
                            id="numeroNota"
                            class="form-control campo-normal"
                            maxlength="30"
                            autocomplete="off"
                            disabled
                        >

                    </div>


                    <div class="col-md-5 mb-3">

                        <label
                            for="nombreCliente"
                            class="form-label campo-requerido"
                        >
                            Nombre del cliente
                        </label>

                        <input
                            type="text"
                            name="nombre_cliente"
                            id="nombreCliente"
                            class="form-control campo-normal"
                            maxlength="150"
                            autocomplete="off"
                            disabled
                        >

                    </div>


                    <div class="col-md-4 mb-3">

                        <label
                            for="telefono"
                            class="form-label"
                        >
                            Teléfono
                        </label>

                        <input
                            type="text"
                            name="telefono"
                            id="telefono"
                            class="form-control campo-normal"
                            maxlength="30"
                            disabled
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label
                            for="direccion"
                            class="form-label"
                        >
                            Dirección
                        </label>

                        <input
                            type="text"
                            name="direccion"
                            id="direccion"
                            class="form-control campo-normal"
                            maxlength="200"
                            disabled
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label
                            for="ciudad"
                            class="form-label"
                        >
                            Ciudad
                        </label>

                        <input
                            type="text"
                            name="ciudad"
                            id="ciudad"
                            class="form-control campo-normal"
                            maxlength="100"
                            disabled
                        >

                    </div>

                </div>

            </div>

        </div>



        <!-- ==========================================================
             DATOS DE LA REMISIÓN FORESTAL
        =========================================================== -->

        <div
            id="formRemisionForestal"
            class="card shadow mb-4 d-none"
        >

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">
                    Datos de la remisión forestal
                </h5>

            </div>


            <div class="card-body">

                <div class="row">


                    <!-- CLIENTE -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="clienteId"
                            class="form-label campo-requerido"
                        >
                            Cliente
                        </label>


                        <select
                            name="cliente_id"
                            id="clienteId"
                            class="form-select campo-remision"
                            disabled
                        >

                            <option value="">
                                Seleccione un cliente
                            </option>


                            <?php foreach ($clientes as $cliente): ?>

                                <option
                                    value="<?= (int) $cliente['id'] ?>"
                                >

                                    <?= htmlspecialchars(
                                        $cliente['nombre'],
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- FOLIO PROGRESIVO -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="folioProgresivo"
                            class="form-label campo-requerido"
                        >
                            Folio progresivo
                        </label>


                        <input
                            type="text"
                            name="folio_progresivo"
                            id="folioProgresivo"
                            class="form-control campo-remision"
                            maxlength="50"
                            disabled
                        >

                    </div>


                    <!-- FOLIO AUTORIZADO -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="folioAutorizado"
                            class="form-label"
                        >
                            Folio autorizado
                        </label>


                        <input
                            type="text"
                            name="folio_autorizado"
                            id="folioAutorizado"
                            class="form-control campo-remision"
                            maxlength="50"
                            disabled
                        >

                    </div>


                    <!-- FECHA VENCIMIENTO -->

                    <div class="col-md-3 mb-3">

                        <label
                            for="fechaVencimiento"
                            class="form-label"
                        >
                            Fecha de vencimiento
                        </label>


                        <input
                            type="date"
                            name="fecha_vencimiento"
                            id="fechaVencimiento"
                            class="form-control campo-remision"
                            disabled
                        >

                    </div>


                    <!-- DESTINATARIO -->

                    <div class="col-md-5 mb-3">

                        <label
                            for="destinatario"
                            class="form-label campo-requerido"
                        >
                            Destinatario
                        </label>


                        <input
                            type="text"
                            name="destinatario"
                            id="destinatario"
                            class="form-control campo-remision"
                            maxlength="180"
                            disabled
                        >

                    </div>


                    <!-- DOMICILIO -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="domicilioDestino"
                            class="form-label"
                        >
                            Domicilio del destino
                        </label>


                        <input
                            type="text"
                            name="domicilio_destino"
                            id="domicilioDestino"
                            class="form-control campo-remision"
                            maxlength="200"
                            disabled
                        >

                    </div>


                    <!-- MUNICIPIO -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="municipio"
                            class="form-label"
                        >
                            Municipio
                        </label>


                        <input
                            type="text"
                            name="municipio"
                            id="municipio"
                            class="form-control campo-remision"
                            maxlength="100"
                            disabled
                        >

                    </div>


                    <!-- ESTADO -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="entidad"
                            class="form-label"
                        >
                            Estado
                        </label>


                        <input
                            type="text"
                            name="entidad"
                            id="entidad"
                            class="form-control campo-remision"
                            maxlength="100"
                            disabled
                        >

                    </div>


                    <!-- MEDIO DE TRANSPORTE -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="medioTransporte"
                            class="form-label"
                        >
                            Medio de transporte
                        </label>


                        <input
                            type="text"
                            name="medio_transporte"
                            id="medioTransporte"
                            class="form-control campo-remision"
                            maxlength="100"
                            placeholder="Ej. Camión"
                            disabled
                        >

                    </div>


                    <!-- MARCA -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="marcaVehiculo"
                            class="form-label"
                        >
                            Marca del vehículo
                        </label>


                        <input
                            type="text"
                            name="marca_vehiculo"
                            id="marcaVehiculo"
                            class="form-control campo-remision"
                            maxlength="80"
                            disabled
                        >

                    </div>


                    <!-- TIPO VEHÍCULO -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="tipoVehiculo"
                            class="form-label"
                        >
                            Tipo de vehículo
                        </label>


                        <input
                            type="text"
                            name="tipo_vehiculo"
                            id="tipoVehiculo"
                            class="form-control campo-remision"
                            maxlength="80"
                            disabled
                        >

                    </div>


                    <!-- PLACAS -->

                    <div class="col-md-4 mb-3">

                        <label
                            for="placas"
                            class="form-label"
                        >
                            Placas
                        </label>


                        <input
                            type="text"
                            name="placas"
                            id="placas"
                            class="form-control campo-remision"
                            maxlength="30"
                            disabled
                        >

                    </div>

                </div>

            </div>

        </div>



        <!-- ==========================================================
             DATOS DE PRODUCTOS Y SALIDA
        =========================================================== -->

        <div
            id="datosSalida"
            class="card shadow mb-4 d-none"
        >

            <div class="card-header bg-dark text-white">

                <h5
                    class="mb-0"
                    id="tituloProductos"
                >
                    Datos de productos y salida
                </h5>

            </div>


            <div class="card-body">


                <!-- FECHA -->

                <div class="row mb-3">

                    <div class="col-md-3">

                        <label
                            for="fecha"
                            class="form-label campo-requerido"
                        >
                            Fecha
                        </label>


                        <input
                            type="date"
                            name="fecha"
                            id="fecha"
                            class="form-control campo-comun"
                            value="<?= date('Y-m-d') ?>"
                            disabled
                        >

                    </div>

                </div>



                <!-- ==================================================
                     PRODUCTOS PARA NOTA NORMAL
                =================================================== -->

                <div
                    id="productosNormal"
                    class="d-none"
                >

                    <div class="table-responsive">

                        <table
                            class="table table-bordered align-middle tabla-productos"
                        >

                            <thead class="table-primary">

                                <tr>

                                    <th style="width: 15%">
                                        Cantidad
                                    </th>

                                    <th style="width: 40%">
                                        Producto
                                    </th>

                                    <th style="width: 18%">
                                        Precio unitario
                                    </th>

                                    <th style="width: 18%">
                                        Importe
                                    </th>

                                    <th style="width: 9%">
                                        Acción
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="detalleProductos">

                                <tr class="fila-producto">

                                    <td>

                                        <input
                                            type="number"
                                            name="cantidad[]"
                                            class="form-control cantidad campo-comun"
                                            min="0.001"
                                            step="0.001"
                                            placeholder="0.000"
                                            disabled
                                            required
                                        >

                                    </td>


                                    <td>

                                        <select
                                            name="producto_id[]"
                                            class="form-select producto campo-comun"
                                            disabled
                                            required
                                        >

                                            <option value="">
                                                Seleccione un producto
                                            </option>


                                            <?php foreach ($productos as $producto): ?>

                                                <option
                                                    value="<?= (int) $producto['id'] ?>"
                                                    data-precio="<?= htmlspecialchars(
                                                        $producto['precio'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                                    data-stock="<?= htmlspecialchars(
                                                        $producto['stock'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                                    data-unidad="<?= htmlspecialchars(
                                                        $producto['unidad_medida'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>"
                                                >

                                                    <?= htmlspecialchars(
                                                        $producto['nombre'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                    — Stock:
                                                    <?= htmlspecialchars(
                                                        $producto['stock'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                    <?= htmlspecialchars(
                                                        $producto['unidad_medida'],
                                                        ENT_QUOTES,
                                                        'UTF-8'
                                                    ) ?>

                                                </option>

                                            <?php endforeach; ?>

                                        </select>

                                    </td>


                                    <td>

                                        <input
                                            type="number"
                                            name="precio_unitario[]"
                                            class="form-control precio-unitario campo-comun"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                            disabled
                                            required
                                        >

                                    </td>


                                    <td>

                                        <input
                                            type="text"
                                            class="form-control importe"
                                            value="$0.00"
                                            readonly
                                        >

                                    </td>


                                    <td class="text-center">

                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-sm eliminar-fila"
                                            disabled
                                        >
                                            Eliminar
                                        </button>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    <button
                        type="button"
                        id="agregarProducto"
                        class="btn btn-outline-primary mb-4 campo-comun"
                        disabled
                    >
                        + Agregar producto
                    </button>

                </div>



                <!-- ==================================================
                     PRODUCTO ÚNICO PARA REMISIÓN FORESTAL
                =================================================== -->

                <div
                    id="productoRemision"
                    class="d-none"
                >

                    <div class="producto-remision-container">


                        <div class="row">


                            <!-- PRODUCTO -->

                            <div class="col-md-12 mb-3">

                                <label
                                    for="productoRemisionId"
                                    class="form-label campo-requerido"
                                >
                                    Producto
                                </label>


                                <select
                                    name="producto_id"
                                    id="productoRemisionId"
                                    class="form-select campo-remision-producto"
                                    disabled
                                >

                                    <option value="">
                                        Seleccione un producto
                                    </option>


                                    <?php foreach ($productos as $producto): ?>

                                        <option
                                            value="<?= (int) $producto['id'] ?>"
                                            data-precio="<?= htmlspecialchars(
                                                $producto['precio'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            data-stock="<?= htmlspecialchars(
                                                $producto['stock'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                            data-unidad="<?= htmlspecialchars(
                                                $producto['unidad_medida'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                            <?= htmlspecialchars(
                                                $producto['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>


                                <div
                                    id="stockRemision"
                                    class="stock-disponible"
                                >
                                    Stock disponible:
                                    --
                                </div>

                            </div>



                            <!-- CANTIDAD -->

                            <div class="col-md-4 mb-3">

                                <label
                                    for="cantidadRemision"
                                    class="form-label campo-requerido"
                                >
                                    Cantidad
                                </label>


                                <div class="input-group">

                                    <input
                                        type="number"
                                        name="cantidad"
                                        id="cantidadRemision"
                                        class="form-control"
                                        min="0.001"
                                        step="0.001"
                                        placeholder="0.000"
                                        disabled
                                    >


                                    <span
                                        class="input-group-text"
                                        id="unidadRemision"
                                    >
                                        --
                                    </span>

                                </div>

                            </div>



                            <!-- PRECIO -->

                            <div class="col-md-4 mb-3">

                                <label
                                    for="precioRemision"
                                    class="form-label"
                                >
                                    Precio unitario
                                </label>


                                <input
                                    type="number"
                                    id="precioRemision"
                                    class="form-control"
                                    step="0.01"
                                    readonly
                                    value="0.00"
                                >

                            </div>



                            <!-- IMPORTE -->

                            <div class="col-md-4 mb-3">

                                <label
                                    for="importeRemision"
                                    class="form-label"
                                >
                                    Importe
                                </label>


                                <input
                                    type="text"
                                    id="importeRemision"
                                    class="form-control importe"
                                    value="$0.00"
                                    readonly
                                >

                            </div>

                        </div>

                    </div>

                </div>



                <!-- ==================================================
                     TOTAL
                =================================================== -->

                <div class="row justify-content-end">

                    <div class="col-md-4 mb-3">

                        <label
                            for="total"
                            class="form-label fw-bold"
                        >
                            Total
                        </label>


                        <input
                            type="text"
                            id="total"
                            class="form-control form-control-lg fw-bold"
                            value="$0.00"
                            readonly
                        >


                        <input
                            type="hidden"
                            name="total_mostrado"
                            id="totalMostrado"
                            value="0.00"
                        >

                    </div>

                </div>



                <!-- ==================================================
                     OBSERVACIONES
                =================================================== -->

                <div class="mb-4">

                    <label
                        for="observaciones"
                        class="form-label"
                    >
                        Observaciones
                    </label>


                    <textarea
                        name="observaciones"
                        id="observaciones"
                        class="form-control campo-comun"
                        rows="3"
                        maxlength="500"
                        placeholder="Observaciones de la salida"
                        disabled
                    ></textarea>

                </div>



                <!-- ==================================================
                     BOTONES
                =================================================== -->

                <div class="d-flex justify-content-between">

                    <a
                        href="dashboard.php"
                        class="btn btn-secondary"
                    >
                        ← Volver
                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Guardar salida
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>



<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
        |--------------------------------------------------------------------------
        | Elementos principales
        |--------------------------------------------------------------------------
        */

        const tipoNormal =
            document.getElementById('tipoNormal');

        const tipoRemision =
            document.getElementById('tipoRemision');


        const cardNormal =
            document.getElementById('cardNormal');

        const cardRemision =
            document.getElementById('cardRemision');


        const formNotaNormal =
            document.getElementById('formNotaNormal');

        const formRemisionForestal =
            document.getElementById('formRemisionForestal');


        const datosSalida =
            document.getElementById('datosSalida');


        const productosNormal =
            document.getElementById('productosNormal');

        const productoRemision =
            document.getElementById('productoRemision');


        const detalleProductos =
            document.getElementById('detalleProductos');


        const agregarProducto =
            document.getElementById('agregarProducto');


        const fecha =
            document.getElementById('fecha');


        const observaciones =
            document.getElementById('observaciones');


        const total =
            document.getElementById('total');


        const totalMostrado =
            document.getElementById('totalMostrado');



        /*
        |--------------------------------------------------------------------------
        | Campos de nota normal
        |--------------------------------------------------------------------------
        */

        const numeroNota =
            document.getElementById('numeroNota');

        const nombreCliente =
            document.getElementById('nombreCliente');



        /*
        |--------------------------------------------------------------------------
        | Campos de remisión
        |--------------------------------------------------------------------------
        */

        const clienteId =
            document.getElementById('clienteId');

        const folioProgresivo =
            document.getElementById('folioProgresivo');

        const destinatario =
            document.getElementById('destinatario');


        const productoRemisionId =
            document.getElementById('productoRemisionId');

        const cantidadRemision =
            document.getElementById('cantidadRemision');

        const precioRemision =
            document.getElementById('precioRemision');

        const importeRemision =
            document.getElementById('importeRemision');

        const unidadRemision =
            document.getElementById('unidadRemision');

        const stockRemision =
            document.getElementById('stockRemision');



        /*
        |--------------------------------------------------------------------------
        | Activar / desactivar campos
        |--------------------------------------------------------------------------
        */

        function activarCampos(
            selector,
            activar
        ) {

            document
                .querySelectorAll(selector)
                .forEach(
                    function (campo) {

                        campo.disabled = !activar;

                    }
                );

        }



        /*
        |--------------------------------------------------------------------------
        | Campos requeridos
        |--------------------------------------------------------------------------
        */

        function establecerRequeridos(
            tipoSeleccionado
        ) {

            const esNormal =
                tipoSeleccionado === 'normal';

            const esRemision =
                tipoSeleccionado === 'remision_forestal';


            numeroNota.required =
                esNormal;

            nombreCliente.required =
                esNormal;


            clienteId.required =
                esRemision;

            folioProgresivo.required =
                esRemision;

            destinatario.required =
                esRemision;


            fecha.required =
                esNormal || esRemision;


            productoRemisionId.required =
                esRemision;

            cantidadRemision.required =
                esRemision;

        }



        /*
        |--------------------------------------------------------------------------
        | Mostrar formulario según tipo
        |--------------------------------------------------------------------------
        */

        function mostrarFormulario() {

            let tipoSeleccionado = '';


            if (tipoNormal.checked) {

                tipoSeleccionado =
                    'normal';

            }


            if (tipoRemision.checked) {

                tipoSeleccionado =
                    'remision_forestal';

            }


            const esNormal =
                tipoSeleccionado === 'normal';


            const esRemision =
                tipoSeleccionado ===
                'remision_forestal';


            const haySeleccion =
                esNormal || esRemision;



            /*
            |--------------------------------------------------------------------------
            | Mostrar bloques
            |--------------------------------------------------------------------------
            */

            formNotaNormal.classList.toggle(
                'd-none',
                !esNormal
            );


            formRemisionForestal.classList.toggle(
                'd-none',
                !esRemision
            );


            datosSalida.classList.toggle(
                'd-none',
                !haySeleccion
            );


            productosNormal.classList.toggle(
                'd-none',
                !esNormal
            );


            productoRemision.classList.toggle(
                'd-none',
                !esRemision
            );



            /*
            |--------------------------------------------------------------------------
            | Tarjetas
            |--------------------------------------------------------------------------
            */

            cardNormal.classList.toggle(
                'seleccionada',
                esNormal
            );


            cardRemision.classList.toggle(
                'seleccionada',
                esRemision
            );



            /*
            |--------------------------------------------------------------------------
            | Activar campos
            |--------------------------------------------------------------------------
            */

            activarCampos(
                '.campo-normal',
                esNormal
            );


            activarCampos(
                '.campo-remision',
                esRemision
            );


            activarCampos(
                '.campo-comun',
                haySeleccion
            );


            /*
            |--------------------------------------------------------------------------
            | Producto único de remisión
            |--------------------------------------------------------------------------
            */

            activarCampos(
                '.campo-remision-producto',
                esRemision
            );


            /*
            |--------------------------------------------------------------------------
            | Botón agregar
            |
            | SOLO existe para nota normal.
            |--------------------------------------------------------------------------
            */

            agregarProducto.disabled =
                !esNormal;


            /*
            |--------------------------------------------------------------------------
            | Requeridos
            |--------------------------------------------------------------------------
            */

            establecerRequeridos(
                tipoSeleccionado
            );


            actualizarBotonesEliminar();

        }



        /*
        |--------------------------------------------------------------------------
        | Formato moneda
        |--------------------------------------------------------------------------
        */

        function formatoMoneda(valor) {

            return valor.toLocaleString(
                'es-MX',
                {
                    style: 'currency',
                    currency: 'MXN'
                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Calcular fila nota normal
        |--------------------------------------------------------------------------
        */

        function calcularFila(fila) {

            const cantidadInput =
                fila.querySelector(
                    '.cantidad'
                );


            const precioInput =
                fila.querySelector(
                    '.precio-unitario'
                );


            const importeInput =
                fila.querySelector(
                    '.importe'
                );


            const cantidad =
                Number.parseFloat(
                    cantidadInput.value
                ) || 0;


            const precio =
                Number.parseFloat(
                    precioInput.value
                ) || 0;


            const importe =
                cantidad * precio;


            importeInput.value =
                formatoMoneda(
                    importe
                );


            return importe;

        }



        /*
        |--------------------------------------------------------------------------
        | Calcular total nota normal
        |--------------------------------------------------------------------------
        */

        function calcularTotalNormal() {

            let totalGeneral = 0;


            document
                .querySelectorAll(
                    '#detalleProductos .fila-producto'
                )
                .forEach(
                    function (fila) {

                        totalGeneral +=
                            calcularFila(fila);

                    }
                );


            total.value =
                formatoMoneda(
                    totalGeneral
                );


            totalMostrado.value =
                totalGeneral.toFixed(2);

        }



        /*
        |--------------------------------------------------------------------------
        | Calcular remisión forestal
        |--------------------------------------------------------------------------
        */

        function calcularTotalRemision() {

            const cantidad =
                Number.parseFloat(
                    cantidadRemision.value
                ) || 0;


            const precio =
                Number.parseFloat(
                    precioRemision.value
                ) || 0;


            const importe =
                cantidad * precio;


            importeRemision.value =
                formatoMoneda(
                    importe
                );


            total.value =
                formatoMoneda(
                    importe
                );


            totalMostrado.value =
                importe.toFixed(2);

        }



        /*
        |--------------------------------------------------------------------------
        | Actualizar producto de remisión
        |--------------------------------------------------------------------------
        */

        function actualizarProductoRemision() {

            const opcion =
                productoRemisionId
                    .selectedOptions[0];


            if (
                !opcion ||
                !productoRemisionId.value
            ) {

                precioRemision.value =
                    '0.00';

                unidadRemision.textContent =
                    '--';

                stockRemision.textContent =
                    'Stock disponible: --';

                importeRemision.value =
                    '$0.00';

                total.value =
                    '$0.00';

                totalMostrado.value =
                    '0.00';

                cantidadRemision.value =
                    '';

                cantidadRemision.removeAttribute(
                    'max'
                );

                return;

            }


            const precio =
                Number.parseFloat(
                    opcion.dataset.precio
                ) || 0;


            const stock =
                Number.parseFloat(
                    opcion.dataset.stock
                ) || 0;


            const unidad =
                opcion.dataset.unidad ||
                '';


            precioRemision.value =
                precio.toFixed(2);


            unidadRemision.textContent =
                unidad;


            stockRemision.textContent =
                `Stock disponible: ${stock.toFixed(3)} ${unidad}`;


            cantidadRemision.max =
                stock;


            calcularTotalRemision();

        }



        /*
        |--------------------------------------------------------------------------
        | Actualizar botones eliminar
        |--------------------------------------------------------------------------
        */

        function actualizarBotonesEliminar() {

            const filas =
                document.querySelectorAll(
                    '#detalleProductos .fila-producto'
                );


            const esNormal =
                tipoNormal.checked;


            filas.forEach(
                function (fila) {

                    const boton =
                        fila.querySelector(
                            '.eliminar-fila'
                        );


                    boton.disabled =
                        !esNormal ||
                        filas.length === 1;

                }
            );

        }



        /*
        |--------------------------------------------------------------------------
        | Agregar producto a nota normal
        |--------------------------------------------------------------------------
        */

        function agregarFilaProducto() {

            /*
            | Protección:
            | nunca permitir agregar filas
            | si estamos en remisión.
            */

            if (!tipoNormal.checked) {
                return;
            }


            const primeraFila =
                detalleProductos.querySelector(
                    '.fila-producto'
                );


            const nuevaFila =
                primeraFila.cloneNode(
                    true
                );


            /*
            |--------------------------------------------------------------------------
            | Limpiar campos
            |--------------------------------------------------------------------------
            */

            nuevaFila
                .querySelectorAll('input, select')
                .forEach(
                    function (campo) {

                        if (
                            campo.classList.contains(
                                'importe'
                            )
                        ) {

                            campo.value =
                                '$0.00';

                        }

                        else {

                            campo.value =
                                '';

                        }


                        campo.disabled =
                            false;

                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Restaurar botón eliminar
            |--------------------------------------------------------------------------
            */

            const botonEliminar =
                nuevaFila.querySelector(
                    '.eliminar-fila'
                );


            botonEliminar.disabled =
                false;


            /*
            |--------------------------------------------------------------------------
            | Agregar fila
            |--------------------------------------------------------------------------
            */

            detalleProductos.appendChild(
                nuevaFila
            );


            actualizarBotonesEliminar();


            calcularTotalNormal();


            /*
            |--------------------------------------------------------------------------
            | Enfocar producto
            |--------------------------------------------------------------------------
            */

            nuevaFila
                .querySelector(
                    '.producto'
                )
                .focus();

        }



        /*
        |--------------------------------------------------------------------------
        | Cambiar producto en nota normal
        |--------------------------------------------------------------------------
        */

        detalleProductos.addEventListener(
            'change',
            function (evento) {

                if (
                    !evento.target.classList.contains(
                        'producto'
                    )
                ) {
                    return;
                }


                const fila =
                    evento.target.closest(
                        '.fila-producto'
                    );


                const opcion =
                    evento.target
                        .selectedOptions[0];


                if (
                    !opcion ||
                    !evento.target.value
                ) {
                    return;
                }


                const precio =
                    Number.parseFloat(
                        opcion.dataset.precio
                    ) || 0;


                const precioInput =
                    fila.querySelector(
                        '.precio-unitario'
                    );


                precioInput.value =
                    precio.toFixed(2);


                const cantidadInput =
                    fila.querySelector(
                        '.cantidad'
                    );


                const stock =
                    Number.parseFloat(
                        opcion.dataset.stock
                    ) || 0;


                cantidadInput.max =
                    stock;


                calcularTotalNormal();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Cambios en cantidades / precios
        |--------------------------------------------------------------------------
        */

        detalleProductos.addEventListener(
            'input',
            function (evento) {

                if (
                    evento.target.classList.contains(
                        'cantidad'
                    ) ||
                    evento.target.classList.contains(
                        'precio-unitario'
                    )
                ) {

                    calcularTotalNormal();

                }

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Eliminar producto
        |--------------------------------------------------------------------------
        */

        detalleProductos.addEventListener(
            'click',
            function (evento) {

                const boton =
                    evento.target.closest(
                        '.eliminar-fila'
                    );


                if (!boton) {
                    return;
                }


                const filas =
                    document.querySelectorAll(
                        '#detalleProductos .fila-producto'
                    );


                if (filas.length <= 1) {
                    return;
                }


                boton
                    .closest(
                        '.fila-producto'
                    )
                    .remove();


                actualizarBotonesEliminar();


                calcularTotalNormal();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Cambiar producto de remisión
        |--------------------------------------------------------------------------
        */

        productoRemisionId.addEventListener(
            'change',
            actualizarProductoRemision
        );



        /*
        |--------------------------------------------------------------------------
        | Cambiar cantidad de remisión
        |--------------------------------------------------------------------------
        */

        cantidadRemision.addEventListener(
            'input',
            function () {

                const opcion =
                    productoRemisionId
                        .selectedOptions[0];


                if (
                    opcion &&
                    productoRemisionId.value
                ) {

                    const stock =
                        Number.parseFloat(
                            opcion.dataset.stock
                        ) || 0;


                    const cantidad =
                        Number.parseFloat(
                            cantidadRemision.value
                        ) || 0;


                    if (
                        cantidad > stock
                    ) {

                        cantidadRemision.setCustomValidity(
                            `La cantidad no puede ser mayor al stock disponible (${stock}).`
                        );

                    }

                    else {

                        cantidadRemision.setCustomValidity(
                            ''
                        );

                    }

                }


                calcularTotalRemision();

            }
        );



        /*
        |--------------------------------------------------------------------------
        | Eventos tipo
        |--------------------------------------------------------------------------
        */

        tipoNormal.addEventListener(
            'change',
            mostrarFormulario
        );


        tipoRemision.addEventListener(
            'change',
            mostrarFormulario
        );


        /*
        |--------------------------------------------------------------------------
        | Agregar producto
        |--------------------------------------------------------------------------
        */

        agregarProducto.addEventListener(
            'click',
            agregarFilaProducto
        );



        /*
        |--------------------------------------------------------------------------
        | Validación antes de enviar
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('formSalida')
            .addEventListener(
                'submit',
                function (evento) {


                    /*
                    |--------------------------------------------------------------------------
                    | REMISIÓN FORESTAL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        tipoRemision.checked
                    ) {

                        const opcion =
                            productoRemisionId
                                .selectedOptions[0];


                        if (
                            !productoRemisionId.value
                        ) {

                            evento.preventDefault();

                            alert(
                                'Debe seleccionar un producto.'
                            );

                            return;

                        }


                        const stock =
                            Number.parseFloat(
                                opcion.dataset.stock
                            ) || 0;


                        const cantidad =
                            Number.parseFloat(
                                cantidadRemision.value
                            ) || 0;


                        if (
                            cantidad <= 0
                        ) {

                            evento.preventDefault();

                            alert(
                                'La cantidad debe ser mayor que cero.'
                            );

                            return;

                        }


                        if (
                            cantidad > stock
                        ) {

                            evento.preventDefault();

                            alert(
                                `La cantidad solicitada (${cantidad}) supera el stock disponible (${stock}).`
                            );

                            return;

                        }


                        calcularTotalRemision();

                        return;

                    }



                    /*
                    |--------------------------------------------------------------------------
                    | NOTA NORMAL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        tipoNormal.checked
                    ) {

                        const filas =
                            document.querySelectorAll(
                                '#detalleProductos .fila-producto'
                            );


                        if (
                            filas.length === 0
                        ) {

                            evento.preventDefault();

                            alert(
                                'Debe agregar al menos un producto.'
                            );

                            return;

                        }


                        let errorStock =
                            false;


                        filas.forEach(
                            function (fila) {

                                const producto =
                                    fila.querySelector(
                                        '.producto'
                                    );


                                const cantidad =
                                    fila.querySelector(
                                        '.cantidad'
                                    );


                                if (
                                    !producto.value
                                ) {
                                    return;
                                }


                                const opcion =
                                    producto
                                        .selectedOptions[0];


                                const stock =
                                    Number.parseFloat(
                                        opcion.dataset.stock
                                    ) || 0;


                                const cantidadValor =
                                    Number.parseFloat(
                                        cantidad.value
                                    ) || 0;


                                if (
                                    cantidadValor >
                                    stock
                                ) {

                                    errorStock = true;

                                }

                            }
                        );


                        if (errorStock) {

                            evento.preventDefault();

                            alert(
                                'Una o más cantidades superan el stock disponible.'
                            );

                            return;

                        }


                        calcularTotalNormal();

                    }

                }
            );



        /*
        |--------------------------------------------------------------------------
        | Estado inicial
        |--------------------------------------------------------------------------
        */

        actualizarBotonesEliminar();

    }
);

</script>



<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
></script>


</body>

</html>