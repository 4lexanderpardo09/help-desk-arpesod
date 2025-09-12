<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Dashboard de Reportes</title>
    <style>
        /* Estilo normal para el número en las cajas de estadísticas */
        .statistic-box .number {
            font-size: 36px;
            transition: font-size 0.3s ease;
        }

        /* NUEVA CLASE: Estilo para cuando el texto es largo */
        .statistic-box .number.texto-largo {
            font-size: 24px;
            line-height: 1.2;
            padding-top: 10px;
        }

        /* Estilos para tarjetas KPI pequeñas (fila adicional) */
        .kpi-small {
            padding: 12px;
            min-height: 90px;
        }

        .kpi-small .number {
            font-size: 22px;
        }

        /* Progress bar SLA */
        .progress {
            height: 10px;
            background: #e9ecef;
            border-radius: 6px;
            overflow: hidden;
            margin-top: 6px;
        }
        .progress-bar {
            height: 100%;
            background: #28a745;
            width: 0%;
            transition: width .6s ease;
        }

        /* Tablas compactas para KPI secundarios */
        .table-compact td, .table-compact th {
            padding: 6px 8px;
            vertical-align: middle;
        }

        /* === KPI secundarios: tarjeta completa blanca === */
        .kpi-small {
            background: #ffffff;               /* cuadro blanco */
            border-radius: 10px;               /* esquinas redondeadas */
            padding: 18px 14px;                /* espacio interior */
            min-height: 110px;
        }

        .kpi-small .number {
            font-size: 20px;
            color: #222;
            margin: 0 0 8px 0;
            display: block;
        }

        .kpi-small .caption div {
            font-size: 14px;
            color: #666;
        }


    </style>
    </head>

    <body class="with-side-menu">
        <input type="hidden" id="user_idx" value="<?php echo $_SESSION['usu_id']; ?>">
        <input type="hidden" id="rol_id_real" value="<?php echo $_SESSION['rol_id_real']; ?>">
        <input type="hidden" id="user_dp_id" value="<?php echo $_SESSION['dp_id']; ?>">
        <input type="hidden" id="is_jefe_depto" value="<?php echo $_SESSION['is_jefe'] ? '1' : '0'; ?>">

        <?php require_once('../MainHeader/header.php') ?>
        <div class="mobile-menu-left-overlay"></div>
        <?php require_once('../MainNav/nav.php') ?>

        <div class="page-content">
            <div class="container-fluid">

                <!-- FILTROS -->
                <div id="panel-filtros" class="box-typical box-typical-padding" style="display: none;">
                    <h5 class="m-t-lg with-border">Filtros del Dashboard</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="filtro_departamento">Departamento</label>
                            <select id="filtro_departamento" class="select2 form-control"></select>
                        </div>

                        <div class="col-md-4">
                            <label for="filtro_subcategoria">Subcategoría</label>
                            <select id="filtro_subcategoria" class="select2 form-control"></select>
                        </div>

                        <div class="col-md-2">
                            <label for="filtro_ticket_id">ID de Ticket</label>
                            <input type="text" id="filtro_ticket_id" class="form-control" placeholder="Ej: 379">
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button id="btn_buscar_ticket" class="btn btn-primary" type="button" title="Buscar Ticket">
                                <i class="fa fa-search"></i>
                            </button>
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button id="btn_limpiar_filtros" class="btn btn-secondary btn-block" title="Limpiar Filtros">
                                <i class="fa fa-eraser"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- KPIs principales -->
                <div class="row">
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box purple">
                            <div>
                                <div class="number" id="lbltotal">0</div>
                                <div class="caption">
                                    <div>Total de Tickets</div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box green">
                            <div>
                                <div class="number" id="lblabiertos">0</div>
                                <div class="caption">
                                    <div>Tickets Abiertos</div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box red">
                            <div>
                                <div class="number" id="lblcerrados">0</div>
                                <div class="caption">
                                    <div>Tickets Cerrados</div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box yellow">
                            <div>
                                <!-- lblpromedio puede recibir la clase texto-largo desde JS -->
                                <div class="number" id="lblpromedio">0</div>
                                <div class="caption">
                                    <div>Promedio Resolución (Horas)</div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>

               
                <!-- KPIs secundarios (fila nueva) - REEMPLAZAR ESTA SECCIÓN -->
                <div class="row" style="margin-top:10px;">
                    <div class="col-sm-6 col-md-3">
                        <article class="statistic-box kpi-small">
                            <div>
                                <div class="number" id="lblPrimeraRespuesta">N/A</div>
                                <div class="caption">
                                    <div>Promedio Primera Respuesta</div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <article class="statistic-box kpi-small">
                            <div>
                                <div class="number" id="lblSLACompliance">N/A</div>
                                <div class="caption">
                                    <div>SLA Compliance (<=48h)</div>
                                </div>
                                <div class="progress">
                                    <div id="bar-sla" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <article class="statistic-box kpi-small">
                            <div>
                                <div class="number" id="lblReopenRate">N/A</div>
                                <div class="caption">
                                    <div>Tasa Reapertura (estimada)</div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <article class="statistic-box kpi-small">
                            <div>
                                <div class="number" id="lblVacio">-</div>
                                <div class="caption">
                                    <div>Espacio para KPI futuro</div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
                <!-- FIN KPIs secundarios -->


                <!-- Gráficos principales -->
                <section class="card" style="margin-top:10px;">
                    <header class="card-header">
                        Tendencia de Tickets Creados por Mes
                    </header>
                    <div class="card-block">
                        <canvas id="line-chart-mes" style="height: 20rem;"></canvas>
                    </div>
                </section>

                <section id="cargaagente" class="card">
                    <header class="card-header">
                        Carga de Trabajo por Agente (Tickets Abiertos)
                    </header>
                    <div class="card-block">
                        <canvas id="bar-chart-agente" style="height: 20rem;"></canvas>
                    </div>
                </section>

                <div class="row">
                    <div class="col-lg-6">
                        <section class="card">
                            <header class="card-header">
                                Top 10 Categorías de Tickets
                            </header>
                            <div class="card-block">
                                <table id="tabla-categorias" class="table table-hover">
                                    <thead>
                                        <th>Categoría</th>
                                        <th>Subcategoría</th>
                                        <th>Cantidad</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-6">
                        <section class="card">
                            <header class="card-header">
                                Top 10 Usuarios Creadores de Tickets
                            </header>
                            <div class="card-block">
                                <table id="tabla-usuarios" class="table table-hover">
                                    <thead>
                                        <th>Usuario</th>
                                        <th>Departamento</th>
                                        <th>Tickets Creados</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <!-- Top categorías por tiempo (nuevo) -->
                    <div class="col-lg-12">
                        <section class="card">
                            <header class="card-header">
                                Top Categorías por Tiempo Promedio de Resolución
                            </header>
                            <div class="card-block">
                                <table id="tabla-top-categorias-tiempo" class="table table-hover table-compact">
                                    <thead>
                                        <th>Categoría</th>
                                        <th>Cantidad</th>
                                        <th>Horas Promedio</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-12">
                        <section class="card">
                            <header class="card-header">
                                Tickets Resueltos por Agente (Últimos 30 días)
                            </header>
                            <div class="card-block">
                                <table id="tabla-resueltos-agente" class="table table-hover">
                                    <thead>
                                        <th>Agente</th>
                                        <th>Tickets Resueltos</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Tiempo Promedio de Respuesta por Agente (Horas)</h5>
                            </div>
                            <div class="card-block">
                                <canvas id="bar-chart-tiempo-agente" height="250"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Tipos de Error Más Comunes</h5>
                            </div>
                            <div class="card-block">
                                <canvas id="pie-chart-errores-tipo" height="250"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Rendimiento y Cuellos de Botella por Paso</h5>
                            </div>
                            <div class="card-block">
                                <table id="tabla-rendimiento-paso" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre del Paso</th>
                                            <th>Horas Promedio</th>
                                            <th>A Tiempo</th>
                                            <th>Atrasado</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Aging backlog -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Aging Backlog (Tickets Abiertos por Antigüedad)</h5>
                            </div>
                            <div class="card-block">
                                <table id="tabla-aging" class="table table-hover table-compact">
                                    <thead>
                                        <th>Rango (días)</th>
                                        <th>Total</th>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Errores Atribuidos por Agente -->
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Errores Atribuidos por Agente</h5>
                            </div>
                            <div class="card-block">
                                <table id="tabla-errores-agente" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nombre del Agente</th>
                                            <th>Total Errores Atribuidos</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div> <!-- row -->

            </div> <!-- container-fluid -->
        </div> <!-- page-content -->

        <?php require_once('../MainJs/js.php') ?>
        <!-- Tu JS principal; asegúrate que aquí esté la versión del .js que contiene todas las funciones nuevas -->
        <script type="text/javascript" src="../Home/home.js"></script>
        <script type="text/javascript" src="../notificacion.js"></script>
    </body>

    </html>
<?php
} else {
    $conectar = new Conectar();
    header("Location: " . $conectar->ruta() . "index.php");
}
?>
