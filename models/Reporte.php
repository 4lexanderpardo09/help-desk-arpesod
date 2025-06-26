<?php
class Reporte extends Conectar {

    // 1. Resumen General: Tickets Abiertos vs. Cerrados (para el gráfico de pastel)
    public function get_tickets_por_estado() {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    CASE
                        WHEN tick_estado = 'Abierto' THEN 'Tickets Abiertos'
                        WHEN tick_estado = 'Cerrado' THEN 'Tickets Cerrados'
                        ELSE tick_estado
                    END AS estado,
                    COUNT(*) AS total
                FROM tm_ticket
                WHERE est = 1
                GROUP BY tick_estado";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Carga de Trabajo por Agente (para el gráfico de barras)
    public function get_carga_por_agente() {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    CONCAT(usuario.usu_nom, ' ', usuario.usu_ape) AS agente_de_soporte,
                    COUNT(ticket.tick_id) AS tickets_abiertos
                FROM tm_ticket AS ticket
                INNER JOIN tm_usuario AS usuario ON ticket.usu_asig = usuario.usu_id
                WHERE ticket.tick_estado = 'Abierto' AND ticket.est = 1
                GROUP BY agente_de_soporte
                ORDER BY tickets_abiertos DESC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Tickets por Categoría (para la tabla de datos)
    public function get_tickets_por_categoria() {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    cat.cat_nom AS categoria,
                    sub.cats_nom AS subcategoria,
                    COUNT(ticket.tick_id) AS cantidad_de_tickets
                FROM tm_ticket AS ticket
                INNER JOIN tm_categoria AS cat ON ticket.cat_id = cat.cat_id
                INNER JOIN tm_subcategoria AS sub ON ticket.cats_id = sub.cats_id
                WHERE ticket.est = 1
                GROUP BY categoria, subcategoria
                ORDER BY cantidad_de_tickets DESC
                LIMIT 10";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Tiempo Promedio de Resolución (para la tarjeta KPI)
    public function get_tiempo_promedio_resolucion() {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    ROUND(AVG(TIMESTAMPDIFF(HOUR, ticket.fech_crea, ticket.fech_cierre)), 2) AS horas_promedio_resolucion
                FROM tm_ticket AS ticket
                WHERE ticket.tick_estado = 'Cerrado' AND ticket.fech_cierre IS NOT NULL";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado['horas_promedio_resolucion'];
    }

    // 5. Tickets Creados por Mes (para el gráfico de líneas)
    public function get_tickets_creados_por_mes() {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    DATE_FORMAT(fech_crea, '%Y-%m') AS mes,
                    COUNT(tick_id) AS total_tickets
                FROM tm_ticket
                WHERE est = 1
                GROUP BY mes
                ORDER BY mes ASC";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Usuarios que Crean Más Tickets (para la tabla de datos)
    public function get_usuarios_top_creadores() {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    CONCAT(u.usu_nom, ' ', u.usu_ape) AS nombre_usuario,
                    d.dp_nom as departamento,
                    COUNT(t.tick_id) AS tickets_creados
                FROM tm_ticket AS t
                INNER JOIN tm_usuario AS u ON t.usu_id = u.usu_id
                INNER JOIN tm_departamento AS d on u.dp_id = d.dp_id
                GROUP BY nombre_usuario, departamento
                ORDER BY tickets_creados DESC
                LIMIT 10";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }


    public function get_tickets_por_estado_x_agente($usu_asig) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    CASE
                        WHEN tick_estado = 'Abierto' THEN 'Tickets Abiertos'
                        WHEN tick_estado = 'Cerrado' THEN 'Tickets Cerrados'
                        ELSE tick_estado
                    END AS estado,
                    COUNT(*) AS total
                FROM tm_ticket
                WHERE est = 1 AND 
                usu_asig = ?
                GROUP BY tick_estado";        
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Tickets por Categoría (para la tabla de datos)
    public function get_tickets_por_categoria_x_agente($usu_asig) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    cat.cat_nom AS categoria,
                    sub.cats_nom AS subcategoria,
                    COUNT(ticket.tick_id) AS cantidad_de_tickets
                FROM tm_ticket AS ticket
                INNER JOIN tm_categoria AS cat ON ticket.cat_id = cat.cat_id
                INNER JOIN tm_subcategoria AS sub ON ticket.cats_id = sub.cats_id
                WHERE ticket.est = 1 AND
                ticket.usu_asig = ?
                GROUP BY categoria, subcategoria
                ORDER BY cantidad_de_tickets DESC
                LIMIT 10";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Tiempo Promedio de Resolución (para la tarjeta KPI)
    public function get_tiempo_promedio_resolucion_x_agente($usu_asig) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    ROUND(AVG(TIMESTAMPDIFF(HOUR, ticket.fech_crea, ticket.fech_cierre)), 2) AS horas_promedio_resolucion
                FROM tm_ticket AS ticket
                WHERE ticket.tick_estado = 'Cerrado' AND ticket.fech_cierre IS NOT NULL AND ticket.usu_asig = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado['horas_promedio_resolucion'];
    }

    // 5. Tickets Creados por Mes (para el gráfico de líneas)
    public function get_tickets_creados_por_mes_x_agente($usu_asig) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    DATE_FORMAT(fech_crea, '%Y-%m') AS mes,
                    COUNT(tick_id) AS total_tickets
                FROM tm_ticket
                WHERE est = 1 AND
                usu_asig = ?
                GROUP BY mes
                ORDER BY mes ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6. Usuarios que Crean Más Tickets (para la tabla de datos)
    public function get_usuarios_top_creadores_x_agente($usu_asig) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT
                    CONCAT(u.usu_nom, ' ', u.usu_ape) AS nombre_usuario,
                    d.dp_nom as departamento,
                    COUNT(t.tick_id) AS tickets_creados
                FROM tm_ticket AS t
                INNER JOIN tm_usuario AS u ON t.usu_id = u.usu_id
                INNER JOIN tm_departamento AS d on u.dp_id = d.dp_id
                WHERE t.est = 1 AND t.usu_asig = ?
                GROUP BY nombre_usuario, departamento
                ORDER BY tickets_creados DESC
                LIMIT 10";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_asig);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>