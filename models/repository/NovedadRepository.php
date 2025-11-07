<?php

namespace models\repository;

use PDO;

class NovedadRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function crearNovedad(int $tick_id, int $paso_id_pausado, int $usu_asig_novedad, int $usu_crea_novedad, string $descripcion_novedad): int
    {
        $sql = "INSERT INTO th_ticket_novedad (tick_id, paso_id_pausado, usu_asig_novedad, usu_crea_novedad, descripcion_novedad, fecha_inicio, estado_novedad) VALUES (?, ?, ?, ?, ?, NOW(), 'Abierta')";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tick_id, $paso_id_pausado, $usu_asig_novedad, $usu_crea_novedad, $descripcion_novedad]);
        return (int)$this->pdo->lastInsertId();
    }

    public function resolverNovedad(int $novedad_id): bool
    {
        $sql = "UPDATE th_ticket_novedad SET fecha_fin = NOW(), estado_novedad = 'Resuelta' WHERE novedad_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$novedad_id]);
    }

    public function getNovedadAbiertaPorTicket(int $tick_id)
    {
        $sql = "SELECT * FROM th_ticket_novedad WHERE tick_id = ? AND estado_novedad = 'Abierta' ORDER BY fecha_inicio DESC LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$tick_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getNovedadesAbiertasPorUsuario(int $usu_id)
    {
        $sql = "SELECT 
                    n.novedad_id,
                    n.tick_id,
                    n.descripcion_novedad,
                    n.fecha_inicio,
                    CONCAT(u.usu_nom, ' ', u.usu_ape) as usu_crea_novedad
                FROM 
                    th_ticket_novedad n
                JOIN 
                    tm_usuario u ON n.usu_crea_novedad = u.usu_id
                WHERE 
                    n.usu_asig_novedad = ? AND n.estado_novedad = 'Abierta'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$usu_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
