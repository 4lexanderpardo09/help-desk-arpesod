<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Detalle ticket</title>
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
                                <h3 id="lblticketid"></h3>
                                <span id="lbltickestado"></span>
                                <span id="lblestado_tiempo"></span> 
                                <span class="label label-primary" id="lblnomusuario"></span>
                                <span class="label label-default" id="lblfechacrea"></span>
                                <span id="lblprioridad"></span>
                                <ol class="breadcrumb breadcrumb-simple">
                                    <li><a href="..\Home\">Home</a></li>
                                    <li><a href="..\ConsultarTicket\">Consultar ticket</a></li>
                                    <li class="active">Detalle ticket</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </header>

                <div id="panel_linea_tiempo" class="box-typical box-typical-padding" style="display:none;">
                    <h5 class="m-t-lg with-border">Progreso del Flujo de Trabajo</h5>
                    <div class="mermaid">
                        
                    </div>
                </div>

                <div id="panel_guia_paso" class="alert alert-info" role="alert" style="display: none;">
                      <div class="card-body">
                        <h4 class="alert-heading" id="guia_paso_nombre"></h4>
                        <p>
                        <strong>Descripción de la Tarea:</strong> Tienes <strong id="guia_paso_tiempo"></strong> día(s) hábiles para completar este paso.
                        </p>
                        <hr>
                        <p class="mb-0">
                        A continuación, en el editor de texto, encontrarás una plantilla o guía con las instrucciones para esta tarea.
                        </p>
                        <!-- Aquí se insertará el adjunto -->
                    </div>
                </div>

                <div class="box-typical box-typical-padding">
                    <div class="row">

                        <div class="col-lg-12">
                            <fieldset class="form-group semibold">
                                <label class="form-label" for="tick_titulo">Titulo</label>
                                <input type="text" class="form-control" id="tick_titulo" name="tick_titulo" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-3">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="cat_id">Categoria</label>
                                <input class="form-control" id="cat_id" name="cat_id" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-3">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="cats_id">Subcategoria</label>
                                <input class="form-control" id="cats_id" name="cats_id" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-3">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="emp_id">Empresa</label>
                                <input class="form-control" id="emp_id" name="emp_id" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-3">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="dp_id">Departamento</label>
                                <input class="form-control" id="dp_id" name="dp_id" readonly>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="tick_titulo">Documentos adicionales</label>
                                <table id="documentos_data" class="table table-bondered table-striped table-vcenter js-datatable-full">
                                    <thead>
                                        <tr>
                                            <th style="width: 90%;">Nombre</th>
                                            <th class="text-center" style="width: 10%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Los datos se llenaran mediante AJAX -->
                                    </tbody>
                                </table>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset class="form-group">
                                <label class="form-label semibold" for="tick_descripusu">Descripcion</label>
                                <div class="summernote-theme-1">
                                    <textarea id="tickd_descripusu" name="tickd_descripusu" class="summernote" name="name" readonly></textarea>
                                </div>
                            </fieldset>
                        </div>
                    </div><!--.row-->

                </div>

                <section class="activity-line" id="lbldetalle">

                </section>


                <div id="boxdetalleticket" class="box-typical box-typical-padding">
                    <p>
                        Ingrese su duda o consulta
                    </p>


                    <div class="row">
                        <form method="post" id="detalle_form">
                            <div class="col-lg-12">
                                
                                    <input type="hidden" id="selected_siguiente_paso_id" name="selected_siguiente_paso_id">
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <fieldset class="form-group">
                                                <label class="form-label semibold" for="cat_id">Documento adicional</label>
                                                <input type="file" name="fileElem" id="fileElem" class="form-control" multiple>
                                            </fieldset>
                                        </div>
                                    </div>
                                
                                <div class="box-typical box-typical-padding" id="panel_respuestas_rapidas">
                                    <h5 class="m-t-lg with-border">Registrar Evento</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="fast_answer_id">Tipo de Evento (Respuesta Rápida)</label>
                                                <select class="select2" id="fast_answer_id" name="fast_answer_id" data-placeholder="Seleccione un tipo de evento...">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="error_descrip">Descripción Detallada del Evento</label>
                                                <textarea class="form-control" id="error_descrip" name="error_descrip" rows="4" placeholder="Añada aquí cualquier detalle relevante sobre el evento que está registrando..."></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <button type="button" id="btn_registrar_evento" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Registrar Evento en Historial
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <fieldset class="form-group semibold">
                                    <label class="form-label" for="tickd_descrip">Descripcion</label>
                                    <div class="summernote-theme-1">
                                        <textarea id="tickd_descrip" name="tickd_descrip" class="summernote" name="name"></textarea>
                                    </div>
                                </fieldset>

                                <div id="panel_checkbox_flujo" class="form-group" style="display: none;">
                                    <div class="checkbox-toggle">
                                        <input type="checkbox" id="checkbox_avanzar_flujo" name="avanzar_flujo">
                                        <label for="checkbox_avanzar_flujo" class="form-label semibold">
                                            Completar este paso y avanzar al siguiente flujo
                                        </label>
                                    </div>
                                </div>
                                <div id="panel_seleccion_usuario" class="box-typical box-typical-padding" style="display: none;">
                                    <h5 class="m-t-lg with-border">Seleccionar Usuario para Asignación</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-label" for="usuario_seleccionado">Asignar a:</label>
                                                <select class="select2" id="usuario_seleccionado" name="usuario_seleccionado" data-placeholder="Buscar por correo electrónico...">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </form>
                        <div style="display: inline-flex;">
                            <div class="col-lg-12">
                                <button type="button" id="btnenviar" class="btn btn-inline">Enviar</button>
                                <button type="button" id="btncrearnovedad" class="btn btn-inline btn-warning">Crear Novedad</button>
                                <button type="button" id="btnresolvernovedad" class="btn btn-inline btn-success" style="display: none;">Resolver Novedad</button>
                            </div>
                            <div class="col-lg-12">
                                <button type="button" id="btncerrarticket" class="btn btn-danger">Cerrar ticket</button>
                            </div>
                        </div>
                         <div class="box-typical box-typical-padding" id="panel_aprobacion" style="display: none;">
                            <h5 class="m-t-lg with-border">Acción Requerida: Aprobar o Rechazar</h5>
                            <p>
                                Este paso del flujo requiere su aprobación para continuar. Puede aprobar para avanzar al siguiente paso o rechazar para devolverlo al paso anterior.
                            </p>
                            <button type="button" id="btn_aprobar_paso" class="btn btn-rounded btn-success">
                                <i class="fa fa-check"></i> Aprobar
                            </button>
                            <button type="button" id="btn_rechazar_paso" class="btn btn-rounded btn-danger">
                                <i class="fa fa-times"></i> Rechazar
                            </button>
                        </div>
                    </div><!--.row-->
                    
                </div>
                

            </div>
        </div>
        <!-- Modal para seleccionar el siguiente paso -->
<div class="modal fade" id="modal_seleccionar_paso" tabindex="-1" role="dialog" aria-labelledby="modal_seleccionar_paso_label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_seleccionar_paso_label">Seleccionar Siguiente Paso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Hay múltiples opciones para el siguiente paso. Por favor, selecciona uno:</p>
                <div class="form-group">
                    <label for="select_siguiente_paso">Paso:</label>
                    <select class="form-control" id="select_siguiente_paso" name="select_siguiente_paso">
                        <!-- Options will be populated by JavaScript -->
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_confirmar_paso_seleccionado">Seleccionar</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para la nota de cierre -->
<div class="modal fade" id="modal_nota_cierre" tabindex="-1" role="dialog" aria-labelledby="modal_nota_cierre_label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_nota_cierre_label">Añadir Nota de Cierre</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Por favor, escribe una nota para el cierre de este ticket. Esta nota será visible para el usuario creador y otros usuarios relevantes.</p>
                <div class="summernote-theme-1">
                    <textarea id="nota_cierre_summernote" name="nota_cierre_summernote" class="summernote"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label semibold" for="cierre_files">Documentos adjuntos</label>
                    <input type="file" name="cierre_files[]" id="cierre_files" class="form-control" multiple>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_confirmar_cierre">Confirmar Cierre</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal para crear novedad -->
<div class="modal fade" id="modal_crear_novedad" tabindex="-1" role="dialog" aria-labelledby="modal_crear_novedad_label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_crear_novedad_label">Crear Novedad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="novedad_form">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="descripcion_novedad">Descripción de la Novedad</label>
                        <textarea class="form-control" id="descripcion_novedad" name="descripcion_novedad" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="usu_asig_novedad">Asignar a</label>
                        <select class="select2" id="usu_asig_novedad" name="usu_asig_novedad" data-placeholder="Seleccione un usuario" required>
                            <!-- Opciones de usuario se cargarán dinámicamente -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Novedad</button>
                </div>
            </form>
        </div>
    </div>
</div>
        <?php require_once('../MainJs/js.php') ?>
        <script type="text/javascript" src="../DetalleTicket/detalleticket.js"></script>
        <script type="text/javascript" src="../notificacion.js"></script>


    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>