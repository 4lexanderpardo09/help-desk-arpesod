<?php
class DocumentoCierre extends Conectar {
    public function insert_documento_cierre($tick_id, $doc_nom) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO td_documento_cierre (tick_id, doc_nom, fech_crea, est) VALUES (?, ?, NOW(), 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $doc_nom);
        $sql->execute();
        return $conectar->lastInsertId();
    }

    public function get_documentos_cierre_x_ticket($tick_id) {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM td_documento_cierre WHERE tick_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>