<?php
require_once('../config/conexion.php');
require_once('../models/Notificacion.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$notificacion = new Notificacion();

switch ($_GET["op"]) {

    case "mostrar":
        $datos = $notificacion->get_notificacion_x_usu($_POST['usu_id']);
        if(is_array($datos) and count($datos) >0){
            foreach ($datos as $row) {
                $output['not_id'] = $row['not_id'];
                $output['usu_id'] = $row['usu_id']; 
                $output['not_mensaje'] = $row['not_mensaje']. ' ' .$row['tick_id'];
                $output['tick_id'] = $row['tick_id'];
            }
            echo json_encode($output);
        }

        

        break;

        case "notificacionespendientes":
            date_default_timezone_set('America/Bogota');
            $datos = $notificacion->get_notificacion_x_usu_todas($_POST['usu_id']);
            $conectar = new Conectar();
            if(is_array($datos) and count($datos) >0){
                foreach ($datos as $row) {

                    $fech_not = new DateTime($row['fech_not']);

                    $ahora = new DateTime('now', new DateTimeZone('America/Bogota'));

                    $intervalo = $fech_not->diff($ahora);

                            // Generar texto legible:
                    if ($intervalo->y > 0) {
                        $tiempo = $intervalo->y . ' año(s)';
                    } elseif ($intervalo->m > 0) {
                        $tiempo = $intervalo->m . ' mes(es)';
                    } elseif ($intervalo->d > 0) {
                        $tiempo = $intervalo->d . ' día(s)';
                    } elseif ($intervalo->h > 0) {
                        $tiempo = $intervalo->h . ' hora(s)';
                    } elseif ($intervalo->i > 0) {
                        $tiempo = $intervalo->i . ' minuto(s)';
                    } else {
                        $tiempo = 'hace unos segundos';
                    }

                    ?>
                    <div class="dropdown-menu-notif-item">
                        <div class="photo">
                            <img src="" alt="">
                        </div>
                        <?php
                        if ($row['est'] != 0) {   
                        ?>
                        <a onclick="verNotificacion(<?php echo $row['not_id'] ?>)" href="<?php echo $conectar->ruta() ?>view/DetalleTicket/?ID=<?php echo $row['tick_id'] ?>">Nueva notificacion </a>
                        <div><?php echo  $row['not_mensaje']?></div>
                        <div class="color-blue-grey-lighter"><?php echo $tiempo ?></div>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
            break;    


    case "actualizar":
        $notificacion->update_notificacion_estado( $_POST["not_id"]);
        break;   
        
    case "leido";
        $notificacion->update_notificacion_estado_leido( $_POST["not_id"]);
        break;

    case "contar";
    $datos = $notificacion->contar_notificaciones_x_usu( $_POST["usu_id"]);
        foreach ($datos as $row) {
            $output['totalnotificaciones'] = $row['totalnotificaciones'];
        }
        echo json_encode($output);
        break;
}
?>