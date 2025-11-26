<?php
class DocumentoFlujo extends Conectar
{
    public function insert_documento_flujo($tick_id, $flujo_id, $paso_id, $usu_id, $doc_nom)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_documento_flujo (tick_id, flujo_id, paso_id, usu_id, doc_nom, fech_crea, est) VALUES (?, ?, ?, ?, ?, NOW(), 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $flujo_id);
        $sql->bindValue(3, $paso_id);
        $sql->bindValue(4, $usu_id);
        $sql->bindValue(5, $doc_nom);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_ultimo_documento_flujo($tick_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        // Get the latest document for this ticket
        $sql = "SELECT * FROM tm_documento_flujo WHERE tick_id = ? AND est = 1 ORDER BY doc_flujo_id DESC LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }
}
