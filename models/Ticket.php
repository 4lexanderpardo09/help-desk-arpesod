<?php
class Ticket extends Conectar
{
    public function insert_ticket($usu_id, $cat_id, $cats_id, $pd_id, $tick_titulo, $tick_descrip, $usu_asig)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_ticket (tick_id,usu_id,cat_id,cats_id,pd_id,tick_titulo,tick_descrip,tick_estado,fech_crea,usu_asig,est) VALUES (NULL,?,?,?,?,?,?,'Abierto',NOW(),?,'1' )  ";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->bindValue(2, $cat_id);
        $sql->bindValue(3, $cats_id);
        $sql->bindValue(4, $pd_id);
        $sql->bindValue(5, $tick_titulo);
        $sql->bindValue(6, $tick_descrip);
        $sql->bindValue(7, $usu_asig);

        $sql->execute();

        $sql1 = "SELECT LAST_INSERT_ID() as tick_id";
        $sql1 = $conectar->prepare($sql1);
        $sql1->execute();   

        return $resultado = $sql1->fetchAll();
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
                td_prioridad.pd_nom
                FROM 
                tm_ticket
                INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
                INNER join tm_usuario on tm_ticket.usu_id = tm_usuario.usu_id
                INNER join td_prioridad on tm_ticket.pd_id = td_prioridad.pd_id
                WHERE 
                tm_ticket.est = 1
                AND tm_usuario.usu_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
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
                td_prioridad.pd_nom
                FROM 
                tm_ticket
                INNER join tm_categoria on tm_ticket.cat_id = tm_categoria.cat_id
                INNER join tm_usuario on tm_ticket.usu_id = tm_usuario.usu_id
                INNER join td_prioridad on tm_ticket.pd_id = td_prioridad.pd_id
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
        tm_usuario.usu_nom,
        tm_usuario.usu_ape,
        tm_usuario.usu_correo,
        tm_categoria.cat_nom,
        tm_subcategoria.cats_nom,
        td_prioridad.pd_nom
        FROM
        tm_ticket
        INNER JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
        INNER JOIN tm_usuario ON tm_ticket.usu_id = tm_usuario.usu_id
        INNER JOIN td_prioridad ON tm_ticket.pd_id = td_prioridad.pd_id
        INNER JOIN tm_subcategoria on tm_ticket.cats_id = tm_subcategoria.cats_id


        WHERE
        tm_ticket.est = 1 AND tm_ticket.tick_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
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
        foreach($datos as $row){
            $usu_asig = $row['usu_asig'];
            $usu_idx = $row['usu_id'];
        }
        
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
    
}
