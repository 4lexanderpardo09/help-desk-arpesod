<?php
  require_once("../../config/conexion.php"); 
  if(isset($_SESSION["usu_id"])){
?>
<!DOCTYPE html>
<html>
    <?php require_once("../MainHead/head.php");?>
	<title>HelpDesk | Mensajeria</title>
</head>
<body class="with-side-menu">

    <?php require_once("../MainHeader/header.php");?>

    <div class="mobile-menu-left-overlay"></div>
    
    <?php require_once("../MainNav/nav.php");?>

	<!-- Contenido -->
	<div class="page-content">
		<div class="container-fluid">
            <input type="hidden" id="usu_id" value="<?php echo $_SESSION['usu_id']; ?>">
			<header class="section-header">
				<div class="tbl">
					<div class="tbl-row">
						<div class="tbl-cell">
							<h3>Mensajeria</h3>
							<ol class="breadcrumb breadcrumb-simple">
								<li><a href="#">Home</a></li>
								<li class="active">Mensajeria</li>
							</ol>
						</div>
					</div>
				</div>
			</header>

			<div class="row">
				<div class="col-md-4">
					<div class="box-typical box-typical-padding">
						<button type="button" id="btnnuevo" class="btn btn-inline btn-primary">Nueva Conversacion</button>
						<table id="conversacion_data" class="table table-bordered table-striped table-vcenter js-dataTable-full">
							<thead>
								<tr>
									<th style="width: 80%;">Conversaciones</th>
									<th class="text-center" style="width: 5%;"></th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-8">
					<div id="mensajes_view" style="display:none;">
						<div id="mensajes_header" class="box-typical-header">
                            <h3 class="text-center" id="nombre_conversacion"></h3>
                        </div>
						<div id="mensajes_body"></div>
						<div id="mensajes_footer">
                            <div id="typing_indicator" style="display:none;"><em>Escribiendo...</em></div>
							<div class="form-group">
								<textarea id="mensaje_texto" class="form-control" rows="3"></textarea>
							</div>
							<button id="enviar_mensaje" class="btn btn-primary">Enviar</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!-- Contenido -->

	<?php require_once("modalnuevomensaje.php");?>

	<?php require_once("../MainJs/js.php");?>
	
	<script type="text/javascript" src="mensajeria.js"></script>

</body>
</html>
<?php
  } else {
    header("Location:".Conectar::ruta()."index.php");
  }
?>