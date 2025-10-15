<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Gestion de pasos</title>
    </head>

    <body class="with-side-menu">

        <?php require_once('../MainHeader/header.php') ?>

        <div class="mobile-menu-left-overlay"></div>

        <?php require_once('../MainNav/nav.php') ?>

        <!-- contenido -->
        <div class="page-content">
            <div class="container-fluid">
                <header class="section-header">
                    <div class="tbl">
                        <div class="tbl-row">
                            <div class="tbl-cell">
                                <h3>Gestion de pasos</h3>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="..\Home\">Home</a></li>
                                    <li><a href="#">Gestion</a></li>
                                    <li><a href="..\GestionFlujo\">Gestion de flujo</a></li>
                                    <li class="active">Gestion de pasos</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>
                <div class="box-typical box-typical-padding">
                <button type="button" id="btn_cargue_masivo" class="btn btn-inline btn-success" data-toggle="modal" data-target="#modalCargueMasivo">
                    <i class="fa fa-upload"></i> Cargue Masivo
                </button>
                <button type="button" id="btnnuevopaso" class="btn btn-inline btn-primary">Nuevo paso</button>
                    <table id="paso_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
                        <thead>
                            <tr role="row">
                                <th style="width: 20%;">Paso</th>
                                <th style="width: 20%;">Nombre</th>
                                <th style="width: 20%;">Usuario asigando</th>  
                                <th style="width: 10%;">Seleccion manual</th>
                                <th style="width: 10%;">Es tarea nacional</th>
                                <th style="width: 10%;">Es Aprobación</th>
                                <th class="text-center" style="width: 5%;">Transiciones</th>
                                <th class="text-center" style="width: 2%;">Editar</th>
                                <th class="text-center" style="width: 2%;">Eliminar</th>
                            </tr>
                        </thead>    
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Modal para Gestionar Transiciones -->
        <div class="modal fade" id="modalGestionTransiciones" tabindex="-1" role="dialog" aria-labelledby="modalGestionTransicionesLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="modalGestionTransicionesLabel">
                            <i class="fa fa-sitemap"></i> Gestionar Transiciones
                        </h5>
                    </div>

                    <div class="modal-body">
                        <div class="alert alert-light border" role="alert">
                            Configurando transiciones para el paso: <strong id="nombre_paso_origen"></strong>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <strong>Añadir Nueva Transición</strong>
                            </div>
                            <div class="card-body">
                                <form id="transicion_form" method="post">
                                    <input type="hidden" id="paso_origen_id_modal" name="paso_origen_id_modal">

                                    <div class="form-row mb-3" id="areaNuevaRuta" style="display: none;">
                                        <div class="col-md-7">
                                            <label for="nueva_ruta_nombre">Nombre de la Nueva Ruta</label>
                                            <div class="input-group">
                                                <input type="text" id="nueva_ruta_nombre" class="form-control" placeholder="Ej: Proceso de Aprobación Gerente">
                                                <div class="input-group-append">
                                                    <button class="btn btn-success" type="button" id="btnGuardarRuta"><i class="fa fa-check"></i> Guardar</button>
                                                    <button class="btn btn-secondary" type="button" id="btnCancelarNuevaRuta" title="Cancelar"><i class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Crea una nueva ruta si no existe en la lista de abajo.</small>
                                        </div>
                                    </div>

                                    <div class="form-row align-items-end">
                                        <div class="form-group col-lg-5 col-md-12">
                                            <label for="ruta_id_modal">1. Seleccione la Ruta de Destino</label>
                                            <div class="input-group">
                                                <select id="ruta_id_modal" name="ruta_id_modal" class="form-control selectpicker" data-live-search="true" title="Ninguna ruta seleccionada" required></select>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-primary" type="button" id="btnNuevaRuta" title="Crear Nueva Ruta"><i class="fa fa-plus"></i></button>
                                                    <button class="btn btn-outline-info" type="button" id="btnGestionarPasos" title="Gestionar Pasos de la Ruta"><i class="fa fa-cogs"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-4 col-md-6">
                                            <label for="condicion_nombre_modal">2. Nombre esta Decisión</label>
                                            <input type="text" id="condicion_nombre_modal" name="condicion_nombre_modal" class="form-control" placeholder="Ej: Aprobar Solicitud" required>
                                        </div>
                                        <div class="form-group col-lg-3 col-md-6">
                                            <label for="condicion_clave_modal">Clave (Opcional)</label>
                                            <input type="text" id="condicion_clave_modal" name="condicion_clave_modal" class="form-control" placeholder="Ej: APROBADO">
                                        </div>
                                    </div>

                                    <div class="text-right mt-3">
                                        <button type="submit" name="action" value="add" class="btn btn-primary"><i class="fa fa-plus-circle"></i> Añadir Transición</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>Transiciones Existentes</h5>
                            <div class="table-responsive">
                                <table id="transiciones_data" class="table table-bordered table-striped table-hover mt-2">
                                    <thead>
                                        <tr>
                                            <th>Ruta Destino</th>
                                            <th>Nombre de la Decisión</th>
                                            <th>Clave</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalGestionPasosRuta" tabindex="-1" role="dialog" aria-labelledby="modalGestionPasosRutaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalGestionPasosRutaLabel">Gestionar Pasos de la Ruta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="gestion_ruta_id" name="gestion_ruta_id">
                        
                        <div class="alert alert-info" role="alert">
                            Estás añadiendo pasos a la ruta: <strong id="gestion_ruta_nombre"></strong>
                        </div>

                        <form id="rutapaso_form" class="row">
                            <div class="col-md-6">
                                <label for="paso_id_para_ruta">Paso a añadir</label>
                                <select id="paso_id_para_ruta" name="paso_id_para_ruta" class="form-control" required></select>
                            </div>
                            <div class="col-md-3">
                                <label for="orden_del_paso">Orden</label>
                                <input type="number" id="orden_del_paso" name="orden_del_paso" class="form-control" required min="1">
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">Añadir Paso</button>
                            </div>
                        </form>

                        <hr>

                        <h5>Pasos Asignados</h5>
                        <table id="rutapasos_data" class="table table-bordered table-striped mt-2">
                            <thead>
                                <tr>
                                    <th style="width: 15%;">Orden</th>
                                    <th>Nombre del Paso</th>
                                    <th style="width: 10%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <?php require_once('../PasoFlujo/modalnuevopaso.php') ?>
        <?php require_once('../MainJs/js.php') ?>

        <script type="text/javascript" src="../PasoFlujo/pasoflujo.js"></script>
        <script type="text/javascript" src="../notificacion.js"></script>


    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>