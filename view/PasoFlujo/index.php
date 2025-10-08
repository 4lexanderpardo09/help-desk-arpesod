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
                        <button type="button" class="modal-close" data-dismiss="modal" aria-label="Close">
                            <i class="font-icon-close-2"></i>
                        </button>
                        <h4 class="modal-title" id="modalGestionTransicionesLabel">Gestionar Transiciones para: <span id="nombre_paso_origen"></span></h4>
                    </div>
                    <div class="modal-body">
                        <h5>Transiciones Existentes</h5>
                        <table id="transiciones_data" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Paso Destino</th>
                                    <th>Clave Condición</th>
                                    <th>Nombre Condición</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Se llenará con JS -->
                            </tbody>
                        </table>
                        <hr>
                        <h5>Nueva Transición</h5>
                        <form method="post" id="transicion_form_modal">
                            <input type="hidden" id="transicion_id_modal" name="transicion_id">
                            <input type="hidden" id="paso_origen_id_modal" name="paso_origen_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="paso_destino_id_modal">Paso Destino</label>
                                        <select class="form-control" id="paso_destino_id_modal" name="paso_destino_id" data-placeholder="Seleccionar (o dejar en blanco si es fin de flujo)">
                                            <!-- Opciones se llenan por JS -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label" for="condicion_clave_modal">Clave Condición</label>
                                        <input type="text" class="form-control" id="condicion_clave_modal" name="condicion_clave" placeholder="Ej: APROBADO" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label" for="condicion_nombre_modal">Nombre Visible Condición</label>
                                        <input type="text" class="form-control" id="condicion_nombre_modal" name="condicion_nombre" placeholder="Ej: Aprobar y continuar" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Añadir Transición</button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-rounded btn-default" data-dismiss="modal">Cerrar</button>
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