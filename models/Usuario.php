<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Usuario extends Conectar{
    
    public function login(){
        $conectar = parent::Conexion();
        parent::set_names();
        if(isset($_POST["enviar"])){
            $correo = $_POST["usu_correo"];
            $password = $_POST["usu_pass"];
            $rol = $_POST["rol_id"];  
            if(empty($correo) || empty($password)){
                header("Location: " . Conectar::ruta() . "index.php?m=2");
                exit();
            }else{
                $sql = "SELECT * FROM tm_usuario WHERE usu_correo = ? AND rol_id = ? AND est = 1";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $correo);
                $stmt->bindValue(2, $rol);
                $stmt->execute();
                $resultado = $stmt->fetch();


                if(is_array($resultado) and count($resultado) > 0 and password_verify($password, $resultado["usu_pass"])){
                    $_SESSION["usu_id"] = $resultado["usu_id"];
                    $_SESSION["usu_nom"] = $resultado["usu_nom"];
                    $_SESSION["usu_ape"] = $resultado["usu_ape"];
                    $_SESSION["rol_id"] = $resultado["rol_id"];

                    header ("Location: " . Conectar::ruta() . "view/Home/");
                    exit();
                }else{
                    header("Location: " . Conectar::ruta() . "index.php?m=1");
                    exit();
                }
            }
        }
    }

    public function insert_usuario($usu_nom,$usu_ape,$usu_correo,$usu_pass,$rol_id,$dp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            
            $hashed_pass = password_hash($usu_pass, PASSWORD_BCRYPT);

            $sql = "INSERT INTO tm_usuario (usu_id, usu_nom, usu_ape, usu_correo, usu_pass, rol_id, dp_id, fech_crea, fech_modi, fech_elim, est) VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NULL, NULL, '1')";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nom);
            $sql->bindValue(2, $usu_ape);
            $sql->bindValue(3, $usu_correo);
            $sql->bindValue(4, $hashed_pass);
            $sql->bindValue(5, $rol_id);
            $sql->bindValue(6, $dp_id);

            $sql->execute();

            return $resultado = $sql->fetchAll();
    }

    public function update_usuario($usu_id,$usu_nom,$usu_ape,$usu_correo,$usu_pass,$rol_id,$dp_id){
        $conectar = parent::Conexion();
            parent::set_names();

            $hashed_pass = password_hash($usu_pass, PASSWORD_BCRYPT);

            $sql = "UPDATE tm_usuario SET
                usu_nom = ?,
                usu_ape = ?,
                usu_correo = ?,
                usu_pass = ?,
                rol_id = ?,
                dp_id = ?
                WHERE usu_id = ?"; 
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nom);
            $sql->bindValue(2, $usu_ape);
            $sql->bindValue(3, $usu_correo);
            $sql->bindValue(4, $hashed_pass);
            $sql->bindValue(5, $rol_id);
            $sql->bindValue(6, $dp_id);
            $sql->bindValue(7, $usu_id);

            $sql->execute();

            return $resultado = $sql->fetchAll();
    }

    public function delete_usuario($usu_id){
        $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_usuario SET est = '0', fech_elim = NOW() WHERE usu_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
    } 

    public function get_usuario(){
        $conectar = parent::Conexion();
            parent::set_names();
            $sql = "call sp_l_usuario_01()";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
    }

    public function get_usuario_x_rol(){
        $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_usuario WHERE rol_id = '2' AND est = '1'";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
    }

    public function get_usuario_x_id($usu_id){
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "call sp_l_usuario_02(?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll(); 
    }

    public function get_usuario_total_id($usu_id){
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket where usu_id = ? and est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll(); 
    }

    public function get_usuario_totalabierto_id($usu_id){
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket where usu_id = ? and tick_estado = 'Abierto' and est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll(); 
    }

    public function get_usuario_totalcerrado_id($usu_id){
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket where usu_id = ? and tick_estado = 'Cerrado' and est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll(); 
    }

    public function get_total_categoria_usuario($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT tm_categoria.cat_nom as nom , COUNT(*) AS total
        FROM tm_ticket JOIN tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
        WHERE tm_ticket.est = '1'
        AND usu_id = ?
        GROUP BY tm_categoria.cat_nom
        ORDER BY total DESC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }
}

?>