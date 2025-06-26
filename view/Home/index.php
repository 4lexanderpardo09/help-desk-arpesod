<?php
require_once('../../config/conexion.php');
if (isset($_SESSION["usu_id"])) {
?>
    <!DOCTYPE html>
    <html>
    <?php require_once('../MainHead/head.php') ?>
    <title>Dashboard de Reportes</title>
    </head>

    <body class="with-side-menu">

        <?php require_once('../MainHeader/header.php') ?>
        <div class="mobile-menu-left-overlay"></div>
        <?php require_once('../MainNav/nav.php') ?>

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box purple">
                            <div>
                                <div class="number" id="lbltotal">0</div>
                                <div class="caption"><div>Total de Tickets</div></div>
                            </div>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box green">
                            <div>
                                <div class="number" id="lblabiertos">0</div>
                                <div class="caption"><div>Tickets Abiertos</div></div>
                            </div>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box red">
                            <div>
                                <div class="number" id="lblcerrados">0</div>
                                <div class="caption"><div>Tickets Cerrados</div></div>
                            </div>
                        </article>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <article class="statistic-box yellow">
                            <div>
                                <div class="number" id="lblpromedio">0</div>
                                <div class="caption"><div>Promedio Resolución (Horas)</div></div>
                            </div>
                        </article>
                    </div>
                </div>

                <section class="card">
                    <header class="card-header">
                        Tendencia de Tickets Creados por Mes
                    </header>
                    <div class="card-block">
                        <canvas id="line-chart-mes" style="height: 20rem;"></canvas>
                    </div>
                </section>

                <section class="card">
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
                </div>

            </div></div><?php require_once('../MainJs/js.php') ?>
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