<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Usuario extends Conectar
{

    public function login()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        if (isset($_POST["enviar"])) {
            $correo = $_POST["usu_correo"];
            $password = $_POST["usu_pass"];
            $rol_solicitado = (int)$_POST["rol_id"];

            if (empty($correo) || empty($password)) {
                header("Location: " . Conectar::ruta() . "index.php?m=2");
                exit();
            } else {
                $sql = "SELECT * FROM tm_usuario WHERE usu_correo = ? AND est = 1";
                $stmt = $conectar->prepare($sql);
                $stmt->bindValue(1, $correo);
                $stmt->execute();
                $resultado = $stmt->fetch();

                if (is_array($resultado) and count($resultado) > 0 and password_verify($password, $resultado["usu_pass"])) {
                    $rol_real_del_usuario = $resultado["rol_id"];
                    $rol_de_administrador = 3;

                    $acceso_permitido = ($rol_real_del_usuario == $rol_de_administrador) || ($rol_real_del_usuario == $rol_solicitado);

                    if ($acceso_permitido) {
                        require_once(dirname(__FILE__) . '/../models/Organigrama.php');
                        $organigrama = new Organigrama();

                        // Verificar si el usuario es jefe usando el organigrama
                        $es_jefe = $organigrama->es_jefe($resultado['car_id']);
                        $_SESSION["is_jefe"] = $es_jefe;

                        // Guardar datos en la sesión
                        $_SESSION["usu_id"] = $resultado["usu_id"];
                        $_SESSION["usu_nom"] = $resultado["usu_nom"];
                        $_SESSION["usu_ape"] = $resultado["usu_ape"];
                        $_SESSION["rol_id"] = $rol_solicitado;
                        $_SESSION["rol_id_real"] = $rol_real_del_usuario;
                        $_SESSION["dp_id"] = $resultado["dp_id"]; // Se mantiene el depto al que pertenece
                        $_SESSION["car_id"] = $resultado["car_id"];
                        $_SESSION["es_nacional"] = $resultado["es_nacional"];

                        header("Location: " . Conectar::ruta() . "view/Home/");
                        exit();
                    } else {
                        header("Location: " . Conectar::ruta() . "index.php?m=1");
                        exit();
                    }
                } else {
                    header("Location: " . Conectar::ruta() . "index.php?m=1");
                    exit();
                }
            }
        }
    }
    public function insert_usuario($usu_nom, $usu_ape, $usu_correo, $usu_pass, $rol_id, $dp_id, $es_nacional, $reg_id, $car_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $hashed_pass = password_hash($usu_pass, PASSWORD_BCRYPT);

        $sql = "INSERT INTO tm_usuario (usu_id, usu_nom, usu_ape, usu_correo, usu_pass, rol_id, reg_id, car_id, dp_id, es_nacional, fech_crea, fech_modi, fech_elim, est) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NULL, NULL, '1')";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_nom);
        $sql->bindValue(2, $usu_ape);
        $sql->bindValue(3, $usu_correo);
        $sql->bindValue(4, $hashed_pass);
        $sql->bindValue(5, $rol_id);
        $sql->bindValue(6, $reg_id);
        $sql->bindValue(7, $car_id);

        // dp_id puede ser NULL
        if (empty($dp_id)) {
            $sql->bindValue(8, null, PDO::PARAM_NULL);
        } else {
            $sql->bindValue(8, $dp_id, PDO::PARAM_INT);
        }

        $sql->bindValue(9, $es_nacional);

        $sql->execute();

        return $conectar->lastInsertId();
    }

    public function update_usuario($usu_id, $usu_nom, $usu_ape, $usu_correo, $usu_pass, $rol_id, $dp_id, $es_nacional, $reg_id, $car_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        if (!empty($_POST['usu_pass'])) {
            $hashed_pass = password_hash($usu_pass, PASSWORD_BCRYPT);

            $sql = "UPDATE tm_usuario SET
                    usu_nom = ?,
                    usu_ape = ?,
                    usu_correo = ?,
                    usu_pass = ?,
                    rol_id = ?,
                    reg_id = ?,
                    car_id = ?,
                    dp_id = ?,
                    es_nacional = ?
                    WHERE usu_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nom);
            $sql->bindValue(2, $usu_ape);
            $sql->bindValue(3, $usu_correo);
            $sql->bindValue(4, $hashed_pass);
            $sql->bindValue(5, $rol_id);
            $sql->bindValue(6, $reg_id);
            $sql->bindValue(7, $car_id);

            // dp_id puede ser NULL
            if (empty($dp_id)) {
                $sql->bindValue(8, null, PDO::PARAM_NULL);
            } else {
                $sql->bindValue(8, $dp_id, PDO::PARAM_INT);
            }

            $sql->bindValue(9, $es_nacional);

            $sql->bindValue(10, $usu_id);

            $sql->execute();
        } else {
            $sql = "UPDATE tm_usuario SET
                    usu_nom = ?,
                    usu_ape = ?,
                    usu_correo = ?,
                    rol_id = ?,
                    reg_id = ?,
                    car_id = ?,
                    dp_id = ?,
                    es_nacional = ?
                    WHERE usu_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_nom);
            $sql->bindValue(2, $usu_ape);
            $sql->bindValue(3, $usu_correo);
            $sql->bindValue(4, $rol_id);
            $sql->bindValue(5, $reg_id);
            $sql->bindValue(6, $car_id);
            // dp_id puede ser NULL
            if (empty($dp_id)) {
                $sql->bindValue(7, null, PDO::PARAM_NULL);
            } else {
                $sql->bindValue(7, $dp_id, PDO::PARAM_INT);
            }
            $sql->bindValue(8, $es_nacional);

            $sql->bindValue(9, $usu_id);

            $sql->execute();
        }



        return $usu_id;
    }

    public function delete_usuario($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_usuario SET est = '0', fech_elim = NOW() WHERE usu_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_por_correo($usu_correo)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_usuario WHERE usu_correo = ? AND est = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $usu_correo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get_usuario()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "call sp_l_usuario_01()";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_usuarios_por_cargo($car_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT u.usu_id, u.usu_nom, u.usu_ape, r.reg_nom
                FROM tm_usuario u
                LEFT JOIN tm_regional r ON u.reg_id = r.reg_id
                WHERE u.car_id = ? AND u.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_usuario_x_rol()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_usuario WHERE rol_id = '2' AND est = '1'";
        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_x_departamento($dp_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        if (is_null($dp_id)) {
            $sql = "SELECT * FROM tm_usuario WHERE dp_id IS NULL AND est = '1'";
            $sql = $conectar->prepare($sql);
        } else {
            $sql = "SELECT * FROM tm_usuario WHERE dp_id = ? AND est = '1'";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $dp_id, PDO::PARAM_INT);
            $sql->execute();
        }

        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_x_id($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "call sp_l_usuario_02(?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_usuario_detalle_x_id($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_usuario WHERE usu_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_usuario_total_id($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket where usu_id = ? and est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_totalabierto_id($usu_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) AS TOTAL FROM tm_ticket where usu_asig = ? and tick_estado = 'Abierto' and est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_totalcerrado_id($usu_id)
    {
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
        $sql->bindValue(1, $usu_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_usuario_por_cargo_y_regional($car_id, $reg_id)
    {
        $conectar = parent::Conexion();
        $sql = "SELECT * FROM tm_usuario WHERE car_id = ? AND reg_id = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->bindValue(2, $reg_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_usuario_por_cargo($car_id)
    {
        $conectar = parent::Conexion();
        $sql = "SELECT * FROM tm_usuario WHERE car_id = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
    public function get_usuario_nacional_por_cargo($cargo_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_usuario 
            WHERE car_id = ? AND es_nacional = 1 AND est = 1 
            LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cargo_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_usuario_por_cargo_y_departamento($car_id, $dp_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_usuario WHERE car_id = ? AND dp_id = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->bindValue(2, $dp_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_usuarios_por_ids($user_ids)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $inQuery = implode(',', array_fill(0, count($user_ids), '?'));
        $sql = "SELECT usu_id, usu_nom, usu_ape, usu_correo FROM tm_usuario WHERE usu_id IN ($inQuery) AND est = 1";
        $stmt = $conectar->prepare($sql);
        foreach ($user_ids as $k => $id) {
            $stmt->bindValue(($k + 1), $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_usuarios_por_cargo_y_regional_all($car_id, $reg_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_usuario WHERE car_id = ? AND reg_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->bindValue(2, $reg_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_usuarios_por_cargo_regional_o_nacional($car_id, $reg_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        // Selecciona usuarios del cargo que sean de la regional O que sean nacionales
        $sql = "SELECT * FROM tm_usuario 
                WHERE car_id = ? 
                AND (reg_id = ? OR es_nacional = 1) 
                AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->bindValue(2, $reg_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
