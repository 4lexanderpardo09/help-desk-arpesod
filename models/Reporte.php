<?php
class Reporte extends Conectar
{
    /**
     * (Tus métodos originales) ...
     * A continuación pego exactamente todos tus métodos originales (sin cambios),
     * y después agrego los nuevos métodos (índices, tablas resumen, KPIs nuevos, etc).
     *
     * --- INICIO: métodos originales (copiados tal cual) ---
     */

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

    /**
     * --- FIN: métodos originales ---
     *
     * --- INICIO: métodos nuevos / mejoras sugeridas ---
     */

    /**
     * Aplica los índices recomendados para acelerar las consultas.
     * IMPORTANTE: ejecutar primero en staging. Retorna array con resultados (true/false por sentencia).
     */
    public function apply_recommended_indexes()
    {
        $con = parent::Conexion();
        parent::set_names();

        $statements = [
            "ALTER TABLE tm_ticket ADD INDEX IF NOT EXISTS idx_ticket_est_estado (est, tick_estado);",
            "ALTER TABLE tm_ticket ADD INDEX IF NOT EXISTS idx_ticket_cats_est (cats_id, est);",
            "ALTER TABLE tm_ticket ADD INDEX IF NOT EXISTS idx_ticket_usu_asig (usu_asig);",
            "ALTER TABLE tm_ticket ADD INDEX IF NOT EXISTS idx_ticket_usu_id (usu_id);",
            "ALTER TABLE tm_ticket ADD INDEX IF NOT EXISTS idx_ticket_fech_crea (fech_crea);",
            "ALTER TABLE tm_usuario ADD INDEX IF NOT EXISTS idx_usuario_dp (dp_id);",
            "ALTER TABLE th_ticket_asignacion ADD INDEX IF NOT EXISTS idx_th_tickid_fech (tick_id, fech_asig);",
            "ALTER TABLE th_ticket_asignacion ADD INDEX IF NOT EXISTS idx_th_usu_fech (usu_asig, fech_asig);"
        ];

        $results = [];
        try {
            // Algunos motores (MySQL 8+) aceptan IF NOT EXISTS para índices; si el tuyo no, la sentencia fallará y se captura.
            $con->beginTransaction();
            foreach ($statements as $sql) {
                try {
                    $con->exec($sql);
                    $results[] = ['sql' => $sql, 'ok' => true, 'msg' => 'ok'];
                } catch (PDOException $e) {
                    // Intentar sin IF NOT EXISTS (compatibilidad) si falla por sintaxis
                    if (stripos($e->getMessage(), 'syntax') !== false || stripos($e->getMessage(), 'ER_DUP_KEYNAME') !== false || stripos($e->getMessage(), 'Duplicate') !== false) {
                        // marcar como no aplicable/usado
                        $results[] = ['sql' => $sql, 'ok' => false, 'msg' => $e->getMessage()];
                    } else {
                        $results[] = ['sql' => $sql, 'ok' => false, 'msg' => $e->getMessage()];
                    }
                }
            }
            $con->commit();
        } catch (Exception $ex) {
            $con->rollBack();
            $results[] = ['sql' => 'transaction', 'ok' => false, 'msg' => $ex->getMessage()];
        }

        return $results;
    }

    /**
     * Crea la tabla resumen (materialized-like) para KPIs: kpi_ticket_resumen.
     * Solo crea la tabla si no existe.
     */
    public function create_kpi_summary_table()
    {
        $con = parent::Conexion();
        parent::set_names();

        $sql = "
        CREATE TABLE IF NOT EXISTS kpi_ticket_resumen (
            tick_id INT PRIMARY KEY,
            resolucion_horas DECIMAL(10,2) NULL,
            primera_respuesta_horas DECIMAL(10,2) NULL,
            fech_cierre DATETIME NULL,
            prioridad_id INT NULL,
            usu_id INT NULL,
            usu_asig INT NULL,
            cat_id INT NULL,
            cats_id INT NULL,
            reopened_flag TINYINT(1) DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB;
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute();
        return ['ok' => true, 'msg' => 'kpi_ticket_resumen creada o ya existe'];
    }

    /**
     * Pobla (o actualiza) la tabla resumen con cálculos básicos:
     * - resolucion_horas = TIMESTAMPDIFF(HOUR, fech_crea, fech_cierre)
     * - primera_respuesta_horas = TIMESTAMPDIFF(HOUR, fech_crea, primera asignacion)
     *
     * Usa INSERT ... ON DUPLICATE KEY UPDATE para idempotencia.
     */
    public function populate_kpi_summary_once()
    {
        $con = parent::Conexion();
        parent::set_names();

        // Query que obtiene la primera asignación por ticket (subconsulta agregada)
        $sql = "
        INSERT INTO kpi_ticket_resumen (tick_id, resolucion_horas, primera_respuesta_horas, fech_cierre, prioridad_id, usu_id, usu_asig, cat_id, cats_id)
        SELECT 
            t.tick_id,
            CASE WHEN t.fech_cierre IS NOT NULL THEN TIMESTAMPDIFF(HOUR, t.fech_crea, t.fech_cierre) ELSE NULL END AS resolucion_horas,
            CASE WHEN a.fech_asig IS NOT NULL THEN TIMESTAMPDIFF(HOUR, t.fech_crea, a.fech_asig) ELSE NULL END AS primera_respuesta_horas,
            t.fech_cierre,
            COALESCE(t.prioridad_id, NULL),
            t.usu_id,
            t.usu_asig,
            t.cat_id,
            t.cats_id
        FROM tm_ticket t
        LEFT JOIN (
            SELECT tick_id, MIN(fech_asig) AS fech_asig
            FROM th_ticket_asignacion
            GROUP BY tick_id
        ) a ON a.tick_id = t.tick_id
        WHERE t.est = 1
        ON DUPLICATE KEY UPDATE
            resolucion_horas = VALUES(resolucion_horas),
            primera_respuesta_horas = VALUES(primera_respuesta_horas),
            fech_cierre = VALUES(fech_cierre),
            prioridad_id = VALUES(prioridad_id),
            usu_id = VALUES(usu_id),
            usu_asig = VALUES(usu_asig),
            cat_id = VALUES(cat_id),
            cats_id = VALUES(cats_id),
            updated_at = CURRENT_TIMESTAMP;
        ";

        $stmt = $con->prepare($sql);
        $stmt->execute();
        return ['ok' => true, 'msg' => 'población inicial completada'];
    }

    /**
     * Crea un trigger que actualiza la tabla kpi_ticket_resumen cuando un ticket pasa a 'Cerrado'.
     * NOTA: algunos hosts / versiones de MySQL requieren ejecutar el SQL de creación de trigger
     * directamente desde un cliente; si PDO falla, ejecuta el 'createTriggerSql' manualmente en tu cliente.
     */
    public function create_trigger_update_summary_on_close()
    {
        $con = parent::Conexion();
        parent::set_names();

        // Intentaremos crear el trigger (si ya existe, lo eliminamos y recreamos)
        $dropSql = "DROP TRIGGER IF EXISTS trg_update_kpi_on_ticket_close;";
        $createSql = "
        CREATE TRIGGER trg_update_kpi_on_ticket_close
        AFTER UPDATE ON tm_ticket
        FOR EACH ROW
        BEGIN
            -- Si el ticket se cierra ahora
            IF NEW.tick_estado = 'Cerrado' AND (OLD.tick_estado IS NULL OR OLD.tick_estado <> 'Cerrado') THEN
                DECLARE first_assign DATETIME;
                SELECT MIN(fech_asig) INTO first_assign FROM th_ticket_asignacion WHERE tick_id = NEW.tick_id;

                INSERT INTO kpi_ticket_resumen (tick_id, resolucion_horas, primera_respuesta_horas, fech_cierre, prioridad_id, usu_id, usu_asig, cat_id, cats_id)
                VALUES (
                    NEW.tick_id,
                    CASE WHEN NEW.fech_cierre IS NOT NULL THEN TIMESTAMPDIFF(HOUR, NEW.fech_crea, NEW.fech_cierre) ELSE NULL END,
                    CASE WHEN first_assign IS NOT NULL THEN TIMESTAMPDIFF(HOUR, NEW.fech_crea, first_assign) ELSE NULL END,
                    NEW.fech_cierre,
                    NEW.prioridad_id,
                    NEW.usu_id,
                    NEW.usu_asig,
                    NEW.cat_id,
                    NEW.cats_id
                )
                ON DUPLICATE KEY UPDATE
                    resolucion_horas = VALUES(resolucion_horas),
                    primera_respuesta_horas = VALUES(primera_respuesta_horas),
                    fech_cierre = VALUES(fech_cierre),
                    prioridad_id = VALUES(prioridad_id),
                    usu_id = VALUES(usu_id),
                    usu_asig = VALUES(usu_asig),
                    cat_id = VALUES(cat_id),
                    cats_id = VALUES(cats_id),
                    updated_at = CURRENT_TIMESTAMP;
            END IF;
        END;
        ";

        try {
            // DROP trigger
            $con->exec($dropSql);
            // CREATE trigger
            $con->exec($createSql);
            return ['ok' => true, 'msg' => 'trigger creado'];
        } catch (PDOException $e) {
            // Si falla por delimitadores o permisos, devuelve el SQL para que lo ejecutes manualmente
            return ['ok' => false, 'msg' => $e->getMessage(), 'create_sql' => $createSql, 'drop_sql' => $dropSql];
        }
    }

    /**
     * Versión optimizada del cálculo de tiempo promedio por agente (evita subqueries correlacionadas por fila).
     * Usa un JOIN para obtener la siguiente asignación por ticket y calcula la media.
     */
    public function get_tiempo_promedio_por_agente_optimizado($usu_id = null, $dp_id = null, $cats_id = null, $tick_id = null)
    {
        $con = parent::Conexion();
        parent::set_names();

        // Derived table: para cada asignación 'a' buscamos la MIN(b.fech_asig) > a.fech_asig y lo agrupamos.
        $sql = "
        SELECT
            u.usu_nom,
            u.usu_ape,
            AVG(TIMESTAMPDIFF(HOUR, a.fech_asig, a.fecha_fin_paso)) AS horas_promedio_respuesta
        FROM (
            SELECT
                a.th_id,
                a.tick_id,
                a.usu_asig,
                a.fech_asig,
                MIN(b.fech_asig) AS fecha_fin_paso
            FROM th_ticket_asignacion a
            LEFT JOIN th_ticket_asignacion b
              ON b.tick_id = a.tick_id AND b.fech_asig > a.fech_asig
            GROUP BY a.th_id, a.tick_id, a.usu_asig, a.fech_asig
        ) a
        INNER JOIN tm_usuario u ON a.usu_asig = u.usu_id
        INNER JOIN tm_ticket t ON a.tick_id = t.tick_id
        WHERE a.fecha_fin_paso IS NOT NULL
        ";

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
                $sql .= " AND a.usu_asig = ?";
                $params[] = $usu_id;
            }
        }

        $sql .= " GROUP BY u.usu_nom, u.usu_ape ORDER BY horas_promedio_respuesta ASC";

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tiempo promedio hasta la primera respuesta (hrs).
     */
    public function get_tiempo_primera_respuesta($usu_id = null, $dp_id = null, $cats_id = null)
    {
        $con = parent::Conexion();
        parent::set_names();

        $sql = "
        SELECT AVG(TIMESTAMPDIFF(HOUR, t.fech_crea, a.fech_asig)) AS horas_promedio_primera_respuesta
        FROM tm_ticket t
        JOIN (
            SELECT tick_id, MIN(fech_asig) AS fech_asig
            FROM th_ticket_asignacion
            GROUP BY tick_id
        ) a ON t.tick_id = a.tick_id
        WHERE t.est = 1
        ";

        $params = [];
        if (!is_null($cats_id)) {
            $sql .= " AND t.cats_id = ?";
            $params[] = $cats_id;
        }
        if (!is_null($dp_id)) {
            $sql .= " AND EXISTS(SELECT 1 FROM tm_usuario u WHERE u.usu_id = t.usu_id AND u.dp_id = ?)";
            $params[] = $dp_id;
        }
        if (!is_null($usu_id)) {
            $sql .= " AND (t.usu_id = ? OR t.usu_asig = ?)";
            $params[] = $usu_id;
            $params[] = $usu_id;
        }

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? round((float)$row['horas_promedio_primera_respuesta'], 2) : null;
    }

    /**
     * SLA compliance: porcentaje de tickets resueltos en <= $hours horas.
     */
    public function get_sla_compliance($hours = 48, $usu_id = null, $dp_id = null, $cats_id = null)
    {
        $con = parent::Conexion();
        parent::set_names();

        $sql = "
        SELECT
            SUM(CASE WHEN TIMESTAMPDIFF(HOUR, t.fech_crea, t.fech_cierre) <= ? THEN 1 ELSE 0 END) AS cumplidos,
            COUNT(*) AS total
        FROM tm_ticket t
        WHERE t.tick_estado = 'Cerrado' AND t.fech_cierre IS NOT NULL AND t.est = 1
        ";

        $params = [$hours];
        if (!is_null($cats_id)) {
            $sql .= " AND t.cats_id = ?";
            $params[] = $cats_id;
        }
        if (!is_null($dp_id)) {
            $sql .= " AND EXISTS (SELECT 1 FROM tm_usuario u WHERE u.usu_id = t.usu_id AND u.dp_id = ?)";
            $params[] = $dp_id;
        }
        if (!is_null($usu_id)) {
            $sql .= " AND (t.usu_asig = ? OR t.usu_id = ?)";
            $params[] = $usu_id;
            $params[] = $usu_id;
        }

        $stmt = $con->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || $row['total'] == 0) {
            return ['cumplidos' => 0, 'total' => 0, 'pct' => null];
        }

        $pct = round(($row['cumplidos'] / $row['total']) * 100, 2);
        return ['cumplidos' => (int)$row['cumplidos'], 'total' => (int)$row['total'], 'pct' => $pct];
    }

    /**
     * Aging backlog: tickets abiertos por rango de antigüedad (días).
     */
    public function get_aging_backlog($buckets = null)
    {
        // buckets = array of day thresholds: e.g. [3,7,15] => 0-3,4-7,8-15,>15
        $con = parent::Conexion();
        parent::set_names();

        if ($buckets === null) {
            $buckets = [3, 7, 15];
        }

        // Construir CASE dinámico
        $caseSql = "CASE ";
        $prev = 0;
        foreach ($buckets as $b) {
            $caseSql .= "WHEN DATEDIFF(NOW(), t.fech_crea) BETWEEN " . ($prev) . " AND " . $b . " THEN '" . ($prev) . "-" . $b . "d' ";
            $prev = $b + 1;
        }
        $caseSql .= "ELSE '" . $prev . "+d' END as rango";

        $sql = "SELECT $caseSql, COUNT(*) AS total
                FROM tm_ticket t
                WHERE t.tick_estado = 'Abierto' AND t.est = 1
                GROUP BY rango
                ORDER BY rango ASC";

        $stmt = $con->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reopen rate estimado: tickets que tuvieron más de un cierre/asignación (estimación).
     * NOTA: esto es una estimación; para precisión se necesita un flag 'reopened' o historial de estados.
     */
    public function get_reopen_rate_estimate()
    {
        $con = parent::Conexion();
        parent::set_names();

        // Contamos tickets que tienen >1 eventos en th_ticket_asignacion (como proxy de re-aperturas)
        $sql_total = "SELECT COUNT(*) as total FROM tm_ticket WHERE est = 1";
        $sql_reopened = "
            SELECT COUNT(DISTINCT tick_id) as reopened
            FROM th_ticket_asignacion
            GROUP BY tick_id
            HAVING COUNT(*) > 1
        ";

        // Ejecutar total
        $stmt = $con->prepare($sql_total);
        $stmt->execute();
        $total = (int)$stmt->fetchColumn();

        // Ejecutar reopened (esta consulta devuelve N filas; necesitamos sumarlas)
        $stmt2 = $con->prepare("
            SELECT COUNT(*) as reopened
            FROM (
                SELECT tick_id FROM th_ticket_asignacion GROUP BY tick_id HAVING COUNT(*) > 1
            ) x
        ");
        $stmt2->execute();
        $reopened = (int)$stmt2->fetchColumn();

        $pct = $total > 0 ? round(($reopened / $total) * 100, 2) : null;
        return ['reopened' => $reopened, 'total' => $total, 'pct' => $pct];
    }

    /**
     * Top categorías con mayor tiempo promedio de resolución.
     */
    public function get_top_categorias_tiempo_resolucion($limit = 10)
    {
        $con = parent::Conexion();
        parent::set_names();

        $sql = "
        SELECT c.cat_nom, COUNT(t.tick_id) AS cant, AVG(TIMESTAMPDIFF(HOUR, t.fech_crea, t.fech_cierre)) AS hrs_promedio
        FROM tm_ticket t
        JOIN tm_categoria c ON t.cat_id = c.cat_id
        WHERE t.tick_estado = 'Cerrado' AND t.fech_cierre IS NOT NULL AND t.est = 1
        GROUP BY c.cat_nom
        ORDER BY hrs_promedio DESC
        LIMIT ?
        ";

        $stmt = $con->prepare($sql);
        $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Genera script SQL (string) con los ALTER TABLE que recomendamos — útil si prefieres aplicarlos manualmente.
     */
    public function get_recommended_indexes_sql_script()
    {
        $script = "-- Índices recomendados (prueba en staging primero)\n";
        $script .= "ALTER TABLE tm_ticket ADD INDEX idx_ticket_est_estado (est, tick_estado);\n";
        $script .= "ALTER TABLE tm_ticket ADD INDEX idx_ticket_cats_est (cats_id, est);\n";
        $script .= "ALTER TABLE tm_ticket ADD INDEX idx_ticket_usu_asig (usu_asig);\n";
        $script .= "ALTER TABLE tm_ticket ADD INDEX idx_ticket_usu_id (usu_id);\n";
        $script .= "ALTER TABLE tm_ticket ADD INDEX idx_ticket_fech_crea (fech_crea);\n";
        $script .= "ALTER TABLE tm_usuario ADD INDEX idx_usuario_dp (dp_id);\n";
        $script .= "ALTER TABLE th_ticket_asignacion ADD INDEX idx_th_tickid_fech (tick_id, fech_asig);\n";
        $script .= "ALTER TABLE th_ticket_asignacion ADD INDEX idx_th_usu_fech (usu_asig, fech_asig);\n";
        return $script;
    }

    /**
     * Método de utilidad: recalcula (parcial o total) la tabla resumen desde cero.
     * Usa TRUNCATE + populate_kpi_summary_once
     */
    public function rebuild_kpi_summary_full()
    {
        $con = parent::Conexion();
        parent::set_names();

        try {
            $con->beginTransaction();
            $con->exec("TRUNCATE TABLE kpi_ticket_resumen;");
            $this->populate_kpi_summary_once();
            $con->commit();
            return ['ok' => true, 'msg' => 'kpi_ticket_resumen reconstruida'];
        } catch (PDOException $e) {
            $con->rollBack();
            return ['ok' => false, 'msg' => $e->getMessage()];
        }
    }

    /**
     * --- FIN: métodos nuevos ---
     */
}
