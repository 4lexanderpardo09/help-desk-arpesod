<?php
class Reporte extends Conectar
{

    /**
     * Obtiene el conteo de tickets por estado, con múltiples filtros opcionales.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_tickets_por_estado($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                CASE
                    WHEN t.tick_estado = 'Abierto' THEN 'Tickets Abiertos'
                    WHEN t.tick_estado = 'Cerrado' THEN 'Tickets Cerrados'
                    ELSE t.tick_estado
                END AS estado,
                COUNT(*) AS total
            FROM 
                tm_ticket t";

        $params = [];
        $sql_where = " WHERE t.est = 1";

        // Se construye la consulta dinámicamente
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            // Si no se busca un ticket específico, se aplican los otros filtros
            if (!is_null($cats_id)) {
                $sql_where .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql .= " INNER JOIN tm_usuario u ON t.usu_id = u.usu_id";
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND (t.usu_asig = ? OR t.usu_id = ? OR t.how_asig = ?)";
                $params = array_merge($params, [$usu_id, $usu_id, $usu_id]);
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY t.tick_estado";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la cantidad de tickets abiertos asignados a cada agente, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_carga_por_agente($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                CONCAT(usuario.usu_nom, ' ', usuario.usu_ape) AS agente_de_soporte,
                COUNT(ticket.tick_id) AS tickets_abiertos
            FROM 
                tm_ticket AS ticket
            INNER JOIN 
                tm_usuario AS usuario ON ticket.usu_asig = usuario.usu_id";

        $params = [];
        $sql_where = " WHERE ticket.tick_estado = 'Abierto' AND ticket.est = 1";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND ticket.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND ticket.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql_where .= " AND usuario.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND ticket.usu_asig = ?";
                $params[] = $usu_id;
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY agente_de_soporte ORDER BY tickets_abiertos DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el conteo de tickets por categoría, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_tickets_por_categoria($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                cat.cat_nom AS categoria,
                sub.cats_nom AS subcategoria,
                COUNT(ticket.tick_id) AS cantidad_de_tickets
            FROM 
                tm_ticket AS ticket
            INNER JOIN 
                tm_categoria AS cat ON ticket.cat_id = cat.cat_id
            INNER JOIN 
                tm_subcategoria AS sub ON ticket.cats_id = sub.cats_id";

        $params = [];
        $sql_where = " WHERE ticket.est = 1";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND ticket.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND ticket.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql .= " INNER JOIN tm_usuario AS u ON ticket.usu_id = u.usu_id";
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND (ticket.usu_id = ? OR ticket.usu_asig = ?)";
                $params[] = $usu_id;
                $params[] = $usu_id;
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY categoria, subcategoria ORDER BY cantidad_de_tickets DESC LIMIT 10";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calcula el tiempo promedio de resolución en horas, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return float|null El promedio de horas o null si no hay datos.
     */
    public function get_tiempo_promedio_resolucion($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // La consulta ahora solo trae las fechas necesarias
        $sql = "SELECT
                ticket.fech_crea,
                ticket.fech_cierre
            FROM 
                tm_ticket AS ticket";

        $params = [];
        $sql_where = " WHERE ticket.tick_estado = 'Cerrado' AND ticket.fech_cierre IS NOT NULL";

        // Lógica de filtros dinámicos (sin cambios)
        if (!is_null($tick_id)) {
            $sql_where .= " AND ticket.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND ticket.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql .= " INNER JOIN tm_usuario AS u ON ticket.usu_id = u.usu_id";
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND ticket.usu_asig = ?";
                $params[] = $usu_id;
            }
        }

        $sql .= $sql_where;

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($resultados)) {
            return null;
        }

        // Cálculo del promedio en PHP usando el DateHelper para horas hábiles
        $total_horas_habiles = 0;
        foreach ($resultados as $row) {
            $total_horas_habiles += DateHelper::calcular_horas_habiles($row['fech_crea'], $row['fech_cierre']);
        }

        $promedio = $total_horas_habiles / count($resultados);

        return round($promedio, 2);
    }

    /**
     * Obtiene el conteo de tickets creados por mes, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_tickets_creados_por_mes($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                DATE_FORMAT(t.fech_crea, '%Y-%m') AS mes,
                COUNT(t.tick_id) AS total_tickets
            FROM 
                tm_ticket t";

        $params = [];
        $sql_where = " WHERE t.est = 1";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql .= " INNER JOIN tm_usuario u ON t.usu_id = u.usu_id";
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND t.usu_id = ?";
                $params[] = $usu_id;
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY mes ORDER BY mes ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene el top 10 de usuarios que más tickets han creado, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_usuarios_top_creadores($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                CONCAT(u.usu_nom, ' ', u.usu_ape) AS nombre_usuario,
                d.dp_nom as departamento,
                COUNT(t.tick_id) AS tickets_creados
            FROM 
                tm_ticket AS t
            INNER JOIN 
                tm_usuario AS u ON t.usu_id = u.usu_id
            LEFT JOIN 
                tm_departamento AS d on u.dp_id = d.dp_id";

        $params = [];
        $sql_where = " WHERE t.est = 1";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND t.usu_id = ?";
                $params[] = $usu_id;
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY nombre_usuario, departamento ORDER BY tickets_creados DESC LIMIT 10";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * KPI 1: Tiempo Promedio de Respuesta por Agente en horas, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_tiempo_promedio_por_agente($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                u.usu_nom,
                u.usu_ape,
                AVG(TIMESTAMPDIFF(HOUR, T1.fech_asig, T1.fecha_fin_paso)) AS horas_promedio_respuesta
            FROM (
                SELECT
                    a.tick_id,
                    a.usu_asig,
                    a.fech_asig,
                    (SELECT b.fech_asig 
                    FROM th_ticket_asignacion b 
                    WHERE b.tick_id = a.tick_id AND b.fech_asig > a.fech_asig 
                    ORDER BY b.fech_asig ASC LIMIT 1) AS fecha_fin_paso
                FROM
                    th_ticket_asignacion a
            ) AS T1
            INNER JOIN tm_usuario u ON T1.usu_asig = u.usu_id
            -- AÑADIDO: Unimos con tm_ticket para poder filtrar por sus propiedades
            INNER JOIN tm_ticket t ON T1.tick_id = t.tick_id";

        $params = [];
        $sql_where = " WHERE T1.fecha_fin_paso IS NOT NULL";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND T1.usu_asig = ?";
                $params[] = $usu_id;
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY u.usu_nom, u.usu_ape ORDER BY horas_promedio_respuesta ASC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * KPI 2: Rendimiento y Cuellos de Botella por Paso del Flujo, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_rendimiento_por_paso($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                fp.paso_nombre,
                AVG(TIMESTAMPDIFF(HOUR, T1.fech_asig, T1.fecha_fin_paso)) AS horas_promedio_paso,
                SUM(CASE WHEN T1.estado_tiempo_paso = 'A Tiempo' THEN 1 ELSE 0 END) AS a_tiempo,
                SUM(CASE WHEN T1.estado_tiempo_paso = 'Atrasado' THEN 1 ELSE 0 END) AS atrasado
            FROM (
                SELECT
                    a.tick_id,
                    a.paso_id,
                    a.usu_asig,
                    a.fech_asig,
                    a.estado_tiempo_paso,
                    (SELECT b.fech_asig 
                    FROM th_ticket_asignacion b 
                    WHERE b.tick_id = a.tick_id AND b.fech_asig > a.fech_asig 
                    ORDER BY b.fech_asig ASC LIMIT 1) AS fecha_fin_paso
                FROM
                    th_ticket_asignacion a
            ) AS T1
            INNER JOIN tm_flujo_paso fp ON T1.paso_id = fp.paso_id
            -- AÑADIDO: Unimos con tm_ticket para poder filtrar por sus propiedades
            INNER JOIN tm_ticket t ON T1.tick_id = t.tick_id";

        $params = [];
        $sql_where = " WHERE T1.fecha_fin_paso IS NOT NULL AND T1.paso_id IS NOT NULL";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql .= " INNER JOIN tm_usuario u ON T1.usu_asig = u.usu_id";
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND T1.usu_asig = ?";
                $params[] = $usu_id;
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY fp.paso_nombre ORDER BY horas_promedio_paso DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * KPI 3: Conteo de Errores Registrados por Tipo, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_conteo_errores_por_tipo($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT 
                fa.answer_nom AS tipo_de_error,
                COUNT(t.tick_id) AS total_tickets
            FROM 
                tm_ticket t
            INNER JOIN 
                tm_fast_answer fa ON t.error_proceso = fa.answer_id";

        $params = [];
        $sql_where = " WHERE t.error_proceso IS NOT NULL AND t.error_proceso > 0";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql .= " INNER JOIN tm_usuario u ON t.usu_asig = u.usu_id";
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND (t.usu_asig = ? OR t.usu_id = ? OR t.how_asig = ?)";
                $params = array_merge($params, [$usu_id, $usu_id, $usu_id]);
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY fa.answer_nom ORDER BY total_tickets DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * KPI 4: Errores Atribuidos por Persona, con filtros avanzados.
     *
     * @param int|null $usu_id    ID del agente para filtrar.
     * @param int|null $dp_id     ID del departamento para filtrar.
     * @param int|null $cats_id   ID de la subcategoría para filtrar.
     * @param int|null $tick_id   ID de un ticket específico para filtrar.
     * @return array
     */
    public function get_errores_atribuidos_por_agente($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Base de la consulta
        $sql = "SELECT
                u.usu_nom,
                u.usu_ape,
                COUNT(a.th_id) AS total_errores_atribuidos
            FROM
                th_ticket_asignacion a
            INNER JOIN
                tm_usuario u ON a.usu_asig = u.usu_id
            -- AÑADIDO: Unimos con tm_ticket para poder filtrar por sus propiedades
            INNER JOIN 
                tm_ticket t ON a.tick_id = t.tick_id";

        $params = [];
        $sql_where = " WHERE a.error_code_id IS NOT NULL";

        // Lógica para añadir filtros dinámicos
        if (!is_null($tick_id)) {
            // El filtro por ticket específico tiene la máxima prioridad
            $sql_where .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql_where .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }

            if (!is_null($dp_id)) {
                $sql_where .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql_where .= " AND a.usu_asig = ?";
                $params[] = $usu_id;
            }
        }

        // Unimos todo y finalizamos la consulta
        $sql .= $sql_where . " GROUP BY u.usu_nom, u.usu_ape ORDER BY total_errores_atribuidos DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene una lista de todos los departamentos activos para usar en los filtros del dashboard.
     * @return array
     */
    public function get_departamentos_para_filtro()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT dp_id, dp_nom FROM tm_departamento WHERE est = 1 ORDER BY dp_nom ASC";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_subcategorias_para_filtro()
    {
        $conectar = parent::Conexion();
        $sql = "SELECT cats_id, cats_nom FROM tm_subcategoria WHERE est = 1 ORDER BY cats_nom ASC";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_tickets_resueltos_por_agente($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "SELECT
                CONCAT(u.usu_nom, ' ', u.usu_ape) AS agente,
                COUNT(t.tick_id) AS total_cerrados
            FROM
                tm_ticket t
            INNER JOIN
                tm_usuario u ON t.usu_asig = u.usu_id
            WHERE
                t.tick_estado = 'Cerrado'
                AND t.fech_cierre >= DATE_SUB(NOW(), INTERVAL 30 DAY)";

        $params = [];

        if (!is_null($tick_id)) {
            $sql .= " AND t.tick_id = ?";
            $params[] = $tick_id;
        } else {
            if (!is_null($cats_id)) {
                $sql .= " AND t.cats_id = ?";
                $params[] = $cats_id;
            }
            if (!is_null($dp_id)) {
                $sql .= " AND u.dp_id = ?";
                $params[] = $dp_id;
            } elseif (!is_null($usu_id)) {
                $sql .= " AND t.usu_asig = ?";
                $params[] = $usu_id;
            }
        }

        $sql .= " GROUP BY agente ORDER BY total_cerrados DESC";

        $stmt = $conectar->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
