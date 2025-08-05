<?php
class Ticket extends Conectar
{
    public function insert_ticket($usu_id, $cat_id, $cats_id, $pd_id, $tick_titulo, $tick_descrip, $error_proceso, $usu_asig, $paso_actual_id = null, $how_asig)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_ticket (tick_id,usu_id,cat_id,cats_id,pd_id,tick_titulo,tick_descrip,tick_estado,error_proceso,fech_crea,usu_asig,paso_actual_id,how_asig,est) VALUES (NULL,?,?,?,?,?,?,'Abierto',?,NOW(),?,?,?, '1')";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->bindValue(2, $cat_id);
        $sql->bindValue(3, $cats_id);
        $sql->bindValue(4, $pd_id);
        $sql->bindValue(5, $tick_titulo);
        $sql->bindValue(6, $tick_descrip);
        $sql->bindValue(7, $error_proceso);
        $sql->bindValue(8, $usu_asig);
        $sql->bindValue(9, $paso_actual_id);
        $sql->bindValue(10, $how_asig);
        $sql->execute();

        $sql1 = "SELECT LAST_INSERT_ID() as tick_id";
        $sql1 = $conectar->prepare($sql1);
        $sql1->execute();   

        $resultado = $sql1->fetchAll(PDO::FETCH_ASSOC);
        $tick_id = $resultado[0]['tick_id'];

        $sql2 = "INSERT INTO th_ticket_asignacion (tick_id, usu_asig, how_asig, fech_asig, asig_comentario, est)
                VALUES (?, ?, NULL, NOW(), 'Ticket abierto', 1);";
        $sql2 = $conectar->prepare($sql2);
        $sql2->bindValue(1,$tick_id);
        $sql2->bindValue(2,$usu_asig);
        $sql2->execute();

        return $resultado;
    }

    public function update_asignacion_y_paso($tick_id, $usu_asig, $paso_actual_id,$quien_asigno_id) {
        $conectar = parent::Conexion();
        // Actualiza el usuario asignado y el ID del paso actual en el ticket
        $sql = "UPDATE tm_ticket 
                SET 
                    usu_asig = ?,
                    paso_actual_id = ?
                WHERE
                    tick_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->bindValue(2, $paso_actual_id);
        $sql->bindValue(3, $tick_id);
        $sql->execute();

        $sql2 = "INSERT INTO th_ticket_asignacion (tick_id, usu_asig, how_asig, fech_asig, asig_comentario, est)
                VALUES (?, ?, ?, NOW(), 'Reasignado por avance en el flujo', 1);";
        $sql2 = $conectar->prepare($sql2);
        $sql2->bindValue(1, $tick_id);                 // El ticket afectado
        $sql2->bindValue(2, $usu_asig);                // El NUEVO usuario asignado
        $sql2->bindValue(3, $quien_asigno_id);         // El usuario que HIZO la reasignación
        $sql2->execute();

        return $sql->fetchAll();
    }

    public function listar_ticket_x_usuario($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT 
                tm_ticket.tick_id,
                tm_ticket.usu_id,
                tm_ticket.cat_id,
                tm_ticket.tick_titulo,
                tm_ticket.tick_descrip,
                tm_ticket.tick_estado,
                tm_ticket.fech_crea,     
                tm_ticket.usu_asig,
                tm_usuario.usu_nom,
                tm_usuario.usu_ape,
                tm_categoria.cat_nom,
                tm_subcategoria.cats_nom,
                pd.pd_nom as prioridad_usuario,
                pdd.pd_nom as prioridad_defecto
                FROM 
                tm_ticket
                INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
                INNER join tm_subcategoria on tm_ticket.cats_id = tm_subcategoria.cats_id 
                INNER join tm_usuario on tm_ticket.usu_id = tm_usuario.usu_id
                INNER join td_prioridad as pd on tm_ticket.pd_id = pd.pd_id
                INNER join td_prioridad as pdd on tm_subcategoria.pd_id = pdd.pd_id
                WHERE 
                tm_ticket.est = 1
                AND tm_usuario.usu_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function listar_ticket_x_agente($usu_asig)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT 
                tm_ticket.tick_id,
                tm_ticket.usu_id,
                tm_ticket.cat_id,
                tm_ticket.tick_titulo,
                tm_ticket.tick_descrip,
                tm_ticket.tick_estado,
                tm_ticket.fech_crea,     
                tm_ticket.usu_asig,
                tm_usuario.usu_nom,
                tm_usuario.usu_ape,
                tm_categoria.cat_nom,
                tm_subcategoria.cats_nom,
                pd.pd_nom as prioridad_usuario,
                pdd.pd_nom as prioridad_defecto
                FROM 
                tm_ticket
                INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
                INNER JOIN tm_subcategoria on tm_ticket.cats_id = tm_subcategoria.cats_id
                INNER join tm_usuario on tm_ticket.usu_id = tm_usuario.usu_id
                INNER join td_prioridad as pd on tm_ticket.pd_id = pd.pd_id
                INNER join td_prioridad as pdd on tm_subcategoria.pd_id = pdd.pd_id

                WHERE 
                tm_ticket.est = 1
                AND tm_ticket.usu_asig=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function listar_ticket()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT 
                tm_ticket.tick_id,
                tm_ticket.usu_id,
                tm_ticket.cat_id,
                tm_ticket.tick_titulo,
                tm_ticket.tick_descrip,
                tm_ticket.tick_estado,
                tm_ticket.fech_crea,
                tm_ticket.usu_asig,
                tm_usuario.usu_nom,
                tm_usuario.usu_ape,
                tm_categoria.cat_nom,
                tm_subcategoria.cats_nom,
                pd.pd_nom as prioridad_usuario,
                pdd.pd_nom as prioridad_defecto
                FROM 
                tm_ticket
                INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
                INNER JOIN tm_subcategoria on tm_ticket.cats_id = tm_subcategoria.cats_id
                INNER join tm_usuario on tm_ticket.usu_id = tm_usuario.usu_id
                INNER join td_prioridad as pd on tm_ticket.pd_id = pd.pd_id
                INNER join td_prioridad as pdd on tm_subcategoria.pd_id = pdd.pd_id
                WHERE 
                tm_ticket.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll(pdo::FETCH_ASSOC);
    }


    public function listar_ticketdetalle_x_ticket($tick_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                td_ticketdetalle.tickd_id, 
                td_ticketdetalle.tickd_descrip,
                td_ticketdetalle.fech_crea,
                tm_usuario.usu_nom,
                tm_usuario.usu_ape,
                tm_usuario.rol_id,
                td_documento_detalle.det_nom 
            FROM td_ticketdetalle 
            INNER JOIN tm_usuario ON td_ticketdetalle.usu_id = tm_usuario.usu_id
            LEFT JOIN td_documento_detalle ON td_ticketdetalle.tickd_id = td_documento_detalle.tickd_id
            WHERE td_ticketdetalle.tick_id = ? AND td_ticketdetalle.est = 1
              ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function listar_ticket_x_id($tick_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.cat_id,
        tm_ticket.cats_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_ticket.usu_asig,
        tm_ticket.pd_id,
        tm_ticket.paso_actual_id, -- Se añade el ID del paso
        tm_usuario.usu_nom,
        tm_usuario.usu_ape,
        tm_usuario.usu_correo,
        tm_categoria.cat_nom,
        tm_subcategoria.cats_nom,
        td_prioridad.pd_nom,
        tm_departamento.dp_nom,
        td_empresa.emp_nom,
        paso.paso_nombre -- Se añade el NOMBRE del paso
        FROM
        tm_ticket
        LEFT JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
        LEFT JOIN td_empresa ON tm_ticket.cat_id = td_empresa.emp_id
        LEFT JOIN tm_usuario ON tm_ticket.usu_id = tm_usuario.usu_id
        LEFT JOIN tm_departamento ON tm_usuario.dp_id = tm_departamento.dp_id     
        LEFT JOIN td_prioridad ON tm_ticket.pd_id = td_prioridad.pd_id
        LEFT JOIN tm_subcategoria on tm_ticket.cats_id = tm_subcategoria.cats_id
        LEFT JOIN tm_flujo_paso AS paso ON tm_ticket.paso_actual_id = paso.paso_id


        WHERE
        tm_ticket.est = 1 AND tm_ticket.tick_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function listar_historial_completo($tick_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "
            (SELECT
                d.fech_crea AS fecha_evento,
                'comentario' AS tipo,
                u.usu_nom,
                u.usu_ape,
                u.rol_id,
                d.tickd_descrip AS descripcion,
                NULL AS nom_receptor,
                NULL AS ape_receptor,
                doc.det_nom,
                d.tickd_id,
                NULL AS estado_tiempo_paso -- Columna de relleno para que la unión funcione
            FROM td_ticketdetalle d
            INNER JOIN tm_usuario u ON d.usu_id = u.usu_id
            LEFT JOIN td_documento_detalle doc ON d.tickd_id = doc.tickd_id
            WHERE d.tick_id = ?)

            UNION ALL

            (SELECT
                a.fech_asig AS fecha_evento,
                'asignacion' AS tipo,
                IFNULL(u_origen.usu_nom, 'Sistema') AS usu_nom,
                IFNULL(u_origen.usu_ape, '(Acción Automática)') AS usu_ape,
                IFNULL(u_origen.rol_id, 0) AS rol_id,
                a.asig_comentario AS descripcion,
                u_nuevo.usu_nom AS nom_receptor,
                u_nuevo.usu_ape AS ape_receptor,
                NULL AS det_nom,
                NULL AS tickd_id,
                a.estado_tiempo_paso -- AÑADIDO: Seleccionamos el estado del paso
            FROM th_ticket_asignacion a
            LEFT JOIN tm_usuario u_origen ON a.how_asig = u_origen.usu_id
            INNER JOIN tm_usuario u_nuevo ON a.usu_asig = u_nuevo.usu_id
            WHERE a.tick_id = ?)

            UNION ALL

            (SELECT
                t.fech_cierre AS fecha_evento,
                'cierre' AS tipo,
                u_cierre.usu_nom,
                u_cierre.usu_ape,
                u_cierre.rol_id,
                'Ticket cerrado' AS descripcion,
                NULL AS nom_receptor,
                NULL AS ape_receptor,
                NULL AS det_nom,
                NULL AS tickd_id,
                NULL AS estado_tiempo_paso -- Columna de relleno
            FROM tm_ticket t
            LEFT JOIN tm_usuario u_cierre ON t.usu_asig = u_cierre.usu_id
            WHERE t.tick_id = ? AND t.fech_cierre IS NOT NULL)

            ORDER BY fecha_evento ASC
        ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $tick_id);
        $sql->bindValue(3, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listar_tickets_con_historial()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
                    t.tick_id,
                    t.tick_titulo,
                    t.tick_estado,
                    t.fech_crea,
                    cats.cats_nom,
                    u.usu_nom,
                    u.usu_ape
                FROM
                    tm_ticket t
                INNER JOIN tm_subcategoria cats ON t.cats_id = cats.cats_id
                LEFT JOIN tm_usuario u ON t.usu_asig = u.usu_id
                WHERE
                    t.tick_id IN (SELECT tick_id FROM th_ticket_asignacion)
                ORDER BY
                    t.tick_id DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_tickets_involucrados_por_usuario($usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
                    t.tick_id,
                    t.tick_titulo,
                    t.tick_estado,
                    t.fech_crea,
                    cats.cats_nom,
                    u.usu_nom,
                    u.usu_ape
                FROM
                    tm_ticket t
                INNER JOIN tm_subcategoria cats ON t.cats_id = cats.cats_id
                LEFT JOIN tm_usuario u ON t.usu_asig = u.usu_id
                WHERE
                    t.tick_id IN (SELECT DISTINCT tick_id FROM th_ticket_asignacion WHERE usu_asig = ?)
                    OR t.how_asig = ?
                ORDER BY
                    t.tick_id DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->bindValue(2, $usu_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listar_ticket_x_id_x_usuaarioasignado($tick_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.cat_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_usuario.usu_nom,
        tm_usuario.usu_ape,
        tm_usuario.usu_correo,
        tm_categoria.cat_nom
        FROM
        tm_ticket
        INNER JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
        INNER JOIN tm_usuario ON tm_ticket.usu_asig = tm_usuario.usu_id
        WHERE
        tm_ticket.est = 1 AND tm_ticket.tick_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function listar_ticket_x_id_x_quien_asigno($tick_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
        tm_ticket.tick_id,
        tm_ticket.usu_id,
        tm_ticket.cat_id,
        tm_ticket.tick_titulo,
        tm_ticket.tick_descrip,
        tm_ticket.tick_estado,
        tm_ticket.fech_crea,
        tm_usuario.usu_nom,
        tm_usuario.usu_ape,
        tm_usuario.usu_correo,
        tm_categoria.cat_nom
        FROM
        tm_ticket
        INNER JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
        INNER JOIN tm_usuario ON tm_ticket.how_asig = tm_usuario.usu_id
        WHERE
        tm_ticket.est = 1 AND tm_ticket.tick_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insert_ticket_detalle($tick_id, $usu_id, $tickd_descrip)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        
        $ticket = new Ticket();
        
        $datos  = $ticket->listar_ticket_x_id($tick_id);
            $usu_asig = $datos['usu_asig'];
            $usu_idx = $datos['usu_id'];

        
        if($_SESSION['rol_id']==1){

            $mensaje1 = "El usuario te ha respondido el ticket Nro " . $tick_id;

            
            $sql2 = "INSERT INTO tm_notificacion(not_id,usu_id,not_mensaje,tick_id,fech_not,est) VALUES(NULL,$usu_asig,?,?,NOW(),'2');";
            $sql2 = $conectar->prepare($sql2);
            $sql2->bindValue(1, $mensaje1);
            $sql2->bindValue(2, $tick_id);
            
            $sql2->execute();
            
        }else{

            $mensaje2 = "El agente de soporte te ha respondido el ticket Nro " . $tick_id;

            $sql3 = "INSERT INTO tm_notificacion(not_id,usu_id,not_mensaje,tick_id,fech_not,est) VALUES(NULL,?,?,?,NOW(),'2');";
            $sql3 = $conectar->prepare($sql3);
            $sql3->bindValue(1, $usu_idx);
            $sql3->bindValue(2, $mensaje2);
            $sql3->bindValue(3, $tick_id);

            $sql3->execute();
            
        }
        
        $sql = "INSERT INTO td_ticketdetalle (tickd_id, tick_id, usu_id, tickd_descrip, fech_crea, est) VALUES ( NULL, ?, ?, ?, NOW(), '1')  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $usu_id);
        $sql->bindValue(3, $tickd_descrip);
        $sql->execute();
    
        $sql1 = "SELECT LAST_INSERT_ID() as tickd_id";
        $sql1 = $conectar->prepare($sql1);
        $sql1->execute();  
        
        return $sql1->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function update_ticket($tick_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_ticket SET tick_estado = 'Cerrado', fech_cierre = NOW() WHERE tm_ticket.tick_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }
    public function reabrir_ticket($tick_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_ticket SET tick_estado = 'Abierto' WHERE tm_ticket.tick_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    } 

    public function update_ticket_asignacion($tick_id,$usu_asig,$how_asig)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_ticket SET usu_asig = ?, how_asig = ? WHERE tm_ticket.tick_id = ? ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->bindValue(2, $how_asig);
        $sql->bindValue(3, $tick_id);

        $sql->execute();

        // Crea el mensaje completo en una variable de PHP
        $mensaje = "Se le ha asignado el ticket # " . $tick_id;

        $sql1 = "INSERT INTO tm_notificacion(not_id,usu_id,not_mensaje,tick_id,fech_not,est) VALUES(NULL,?,?,?,NOW(),2);";
        $sql1 = $conectar->prepare($sql1);
        $sql1->bindValue(1, $usu_asig);
        $sql1->bindValue(2, $mensaje);
        $sql1->bindValue(3, $tick_id);

        $sql1->execute();

        $sql2 = "INSERT INTO th_ticket_asignacion (tick_id, usu_asig, how_asig, fech_asig, asig_comentario, est)
                VALUES (?, ?, ?, NOW(), 'Ticket trasladado',1)";
        $sql2 = $conectar->prepare($sql2);        
        $sql2->bindValue(1, $tick_id);
        $sql2->bindValue(2, $usu_asig);
        $sql2->bindValue(3, $how_asig);
       
        $sql2->execute();

        return $resultado = $sql->fetchAll();

    }

    public function insert_ticket_detalle_cerrar($tick_id, $usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO td_ticketdetalle (tickd_id, tick_id, usu_id, tickd_descrip, fech_crea, est) VALUES ( NULL, ?, ?, 'Ticket cerrado', NOW(), '1')  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function insert_ticket_detalle_reabrir($tick_id, $usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO td_ticketdetalle (tickd_id, tick_id, usu_id, tickd_descrip, fech_crea, est) VALUES ( NULL, ?, ?, 'Ticket Re-abierto', NOW(), '1')  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ticket_total()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE est = '1'";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ticket_totalabierto_id()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE tick_estado = 'Abierto' and est = '1'";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ticket_totalcerrado_id()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket WHERE tick_estado = 'Cerrado' and est = '1'";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_total_categoria()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT tm_categoria.cat_nom as nom , COUNT(*) AS total
        FROM tm_ticket JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
        WHERE tm_ticket.est = '1'
        GROUP BY tm_categoria.cat_nom
        ORDER BY total DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_calendar_x_asig($usu_asig)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                tm_ticket.tick_id as id,
                tm_ticket.tick_titulo as title,
                tm_ticket.fech_crea as start,
                tm_ticket.tick_estado as estado,
                tm_ticket.tick_descrip as descripcion,
                td_prioridad.pd_nom as prioridad,
                CONCAT(tm_usuario.usu_nom, ' ', tm_usuario.usu_ape) as nombre,
                CASE 
                    WHEN tm_ticket.tick_estado = 'Abierto' THEN 'green'   
                    WHEN tm_ticket.tick_estado = 'Cerrado' THEN 'red'  
                    ELSE 'white' 
                END as color
                FROM 
                tm_ticket
                INNER JOIN tm_usuario ON tm_ticket.usu_id = tm_usuario.usu_id
                INNER JOIN td_prioridad ON tm_ticket.pd_id = td_prioridad.pd_id
                WHERE usu_asig = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_calendar_x_usu($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                tm_ticket.tick_id as id,
                tm_ticket.tick_titulo as title,
                tm_ticket.fech_crea as start,
                tm_ticket.tick_estado as estado,
                tm_ticket.tick_descrip as descripcion,
                CONCAT(usu_asignado.usu_nom, ' ', usu_asignado.usu_ape) as usuasignado,
                td_prioridad.pd_nom as prioridad,
                CASE 
                    WHEN tm_ticket.tick_estado = 'Abierto' THEN 'green'   
                    WHEN tm_ticket.tick_estado = 'Cerrado' THEN 'red'  
                    ELSE 'white' 
                END as color
                FROM 
                tm_ticket
                INNER JOIN tm_usuario as usu_creador ON tm_ticket.usu_id = usu_creador.usu_id
                INNER JOIN td_prioridad ON tm_ticket.pd_id = td_prioridad.pd_id
                LEFT JOIN tm_usuario as usu_asignado ON tm_ticket.usu_asig = usu_asignado.usu_id


                WHERE tm_ticket.usu_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_ticket_region($tick_id){
        $conectar = parent::conexion();
        parent::set_names();
        // Esta consulta busca el ticket, luego al usuario creador, y finalmente la regional de ese usuario.
        $sql = "SELECT u.reg_id
                FROM tm_ticket t
                INNER JOIN tm_usuario u ON t.usu_id = u.usu_id
                WHERE t.tick_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['reg_id'] : null;
    }

    public function get_fecha_ultima_asignacion($tick_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT fech_asig 
                FROM th_ticket_asignacion 
                WHERE tick_id = ? 
                ORDER BY fech_asig DESC 
                LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['fech_asig'] : null;
    }

    public function get_ultima_asignacion($tick_id) {
        $conectar = parent::conexion();
        parent::set_names();
        // Añadimos t.paso_actual_id para saber en qué paso estaba
        $sql = "SELECT a.*, t.paso_actual_id
                FROM th_ticket_asignacion a
                INNER JOIN tm_ticket t ON a.tick_id = t.tick_id
                WHERE a.tick_id = ? 
                ORDER BY a.fech_asig DESC 
                LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function update_estado_tiempo_paso($th_id, $estado) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE th_ticket_asignacion SET estado_tiempo_paso = ? WHERE th_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $th_id);
        $sql->execute();
    }

    public function get_penultima_asignacion($tick_id) {
        $conectar = parent::conexion();
        parent::set_names();
        // LIMIT 1 OFFSET 1 significa "sáltate el primer resultado y dame el siguiente"
        $sql = "SELECT u.usu_nom, u.usu_ape
                FROM th_ticket_asignacion a
                INNER JOIN tm_usuario u ON a.usu_asig = u.usu_id
                WHERE a.tick_id = ? 
                ORDER BY a.fech_asig DESC 
                LIMIT 1 OFFSET 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function update_error_proceso($tick_id, $error_code) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_ticket SET error_proceso = ? WHERE tick_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $error_code);
        $sql->bindValue(2, $tick_id);
        $sql->execute();
    }

    public function update_error_code_paso($th_id, $error_code_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE th_ticket_asignacion SET error_code_id = ? WHERE th_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $error_code_id);
        $sql->bindValue(2, $th_id);
        $sql->execute();
    }
        
}
