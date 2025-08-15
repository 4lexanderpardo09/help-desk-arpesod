<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Detalle ticket</title>
    <style>
        .m-t-lg {
            margin-top: 0px;
        }
    /* Contenedor principal de la línea de tiempo horizontal */
    .timeline-wrapper {
        width: 100%;
        overflow-x: auto; /* Permite scroll horizontal si no cabe */
        padding: 20px 0;
    }

    /* La lista que contiene los pasos */
    .timeline {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0;
        min-width: 600px;
    }

    /* Cada paso individual del timeline */
    .timeline li {
        flex: 1;
        position: relative;
        text-align: center;
        padding-top: 40px;
    }

    /* La línea horizontal que conecta los pasos */
    .timeline li:before {
        content: '';
        position: absolute;
        top: 18px;
        left: -50%;
        width: 100%;
        height: 4px;
        background-color: #e5e5e5;
        /* CORRECCIÓN: Usamos un z-index positivo */
        z-index: 1; 
    }

    /* Ocultamos la línea del primer elemento */
    .timeline li:first-child:before {
        display: none;
    }

    /* El círculo de cada paso */
    .timeline li:after {
        content: '';
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background-color: white; /* Importante para tapar la línea que pasa por detrás */
        border: 4px solid #e5e5e5;
        transition: all 0.2s ease-in-out;
        z-index: 2;
    }

    /* Estilos para el nombre del paso */
    .step-name {
        font-size: 14px;
        color: #777;
    }

    /* --- ESTILOS POR ESTADO --- */

    /* Paso completado */
    .timeline li.timeline-step-completed:before {
        background-color: #5cb85c; /* Verde */
    }
    .timeline li.timeline-step-completed:after {
        border-color: #5cb85c;
        background-color: #5cb85c;
    }
    .timeline li.timeline-step-completed .step-name {
        color: #333;
    }
    
    /* Paso activo (actual) */
    .timeline li.timeline-step-active:after {
        border-color: #337ab7; /* Azul */
        transform: translateX(-50%) scale(1.2);
    }
    .timeline li.timeline-step-active .step-name {
        font-weight: bold;
        color: #337ab7;
    }

    /* Paso pendiente */
    .timeline li.timeline-step-pending:after {
        border-color: #e5e5e5; /* Gris */
    }
    .timeline li.timeline-step-pending .step-name {
        color: #aaa;
    }
</style>
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
                    <div class="timeline-wrapper">
                        <ul id="timeline_flujo" class="timeline">
                        </ul>
                    </div>
                </div>

                <div id="panel_guia_paso" class="alert alert-info" role="alert" style="display: none;">
                    <h4 class="alert-heading" id="guia_paso_nombre"></h4>
                    <p>
                        <strong>Descripción de la Tarea:</strong> Tienes <strong id="guia_paso_tiempo"></strong> día(s) hábiles para completar este paso.
                    </p>
                    <hr>
                    <p class="mb-0">
                        A continuación, en el editor de texto, encontrarás una plantilla o guía con las instrucciones para esta tarea.
                    </p>
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

                <div class="box-typical box-typical-padding" id="panel_aprobacion_jefe" style="display: none;">
                    <h5 class="m-t-lg with-border">Acción Requerida: Aprobación de Flujo</h5>
                    <p>
                        Este ticket requiere tu aprobación para poder iniciar su flujo de trabajo y ser asignado al siguiente responsable.
                    </p>
                    <button type="button" id="btn_aprobar_flujo" class="btn btn-rounded btn-success">
                        <i class="fa fa-check-square"></i> Aprobar y Continuar Flujo
                    </button>
                </div>

                <div id="boxdetalleticket" class="box-typical box-typical-padding">
                    <p>
                        Ingrese su duda o consulta
                    </p>


                    <div class="row">
                        <div class="col-lg-12">
                            <form method="post" id="detalle_form">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <fieldset class="form-group">
                                            <label class="form-label semibold" for="cat_id">Documento adicional</label>
                                            <input type="file" name="fileElem" id="fileElem" class="form-control" multiple>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
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
                                <div id="panel_siguiente_asignado" class="form-group" style="display: none; margin-top: 15px; border-top: 1px solid #e5e5e5; padding-top: 15px;">
                                    <label class="form-label semibold" for="nuevo_asignado_id">Seleccionar Siguiente Agente:</label>
                                    <select id="nuevo_asignado_id" name="nuevo_asignado_id" class="form-control" data-placeholder="Seleccione un usuario..." style="width: 100%;"></select>
                                </div>
                            </div> 
                        </div>

                        <div style="display: inline-flex;">
                            <div class="col-lg-12">
                                <button type="button" id="btnenviar" class="btn btn-inline">Enviar</button>
                            </div>
                            <div class="col-lg-12">
                                <button type="button" id="btncerrarticket" class="btn btn-danger">Cerrar ticket</button>
                            </div>
                        </div>
                    </div><!--.row-->
                    
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