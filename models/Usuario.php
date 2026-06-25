<?php
// Evita redefinir la clase si ya existe
if (!class_exists('Usuario')) {
    class Usuario {
        private $conn;
        private $table_name = "usuarios";

        public function __construct($db){
            $this->conn = $db;
        }

        //  Login (rol 1 y 2)
        public function iniciarSesion($usuario, $password){
            $query = "SELECT u.* 
                      FROM " . $this->table_name . " u
                      WHERE u.usuario = :usuario 
                      AND u.estado = 'ACTIVO' 
                      AND u.id_rol IN (1,2)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario", $usuario);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row && password_verify($password, $row['password'])){
                return $row;
            }
            return false;
        }

        // Listar todos los usuarios 
        public function listarUsuarios(){
            $query = "SELECT u.id_usuario, u.usuario, u.correo, u.estado, u.fecha_creacion, u.id_rol
                      FROM " . $this->table_name . " u
                      WHERE u.id_rol IN (1,2)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

// Buscar usuarios con lupa
        public function buscarUsuarios($criterio){
            if(is_numeric($criterio)){
        // Buscar por rol
        $query = "SELECT u.id_usuario, u.usuario, u.correo, u.estado, u.fecha_creacion, u.id_rol
                  FROM usuarios u
                  WHERE u.id_rol = :rol AND u.id_rol IN (1,2)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rol", $criterio, PDO::PARAM_INT);

        } else {
        // Buscar por estado
        if(strtoupper($criterio) === "ACTIVO" || strtoupper($criterio) === "INACTIVO"){
            $query = "SELECT u.id_usuario, u.usuario, u.correo, u.estado, u.fecha_creacion, u.id_rol
                      FROM usuarios u
                      WHERE u.estado = :estado AND u.id_rol IN (1,2)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":estado", $criterio);

        // Buscar por fecha 
        } else if(preg_match('/^\d{2}[-\/]\d{2}[-\/]\d{4}$/', $criterio)){
            // Intentar ambos formatos
            $fechaObj = DateTime::createFromFormat('d/m/Y', $criterio);
            if(!$fechaObj){
                $fechaObj = DateTime::createFromFormat('d-m-Y', $criterio);
            }
            $fecha = $fechaObj ? $fechaObj->format('Y-m-d') : null;

            $query = "SELECT u.id_usuario, u.usuario, u.correo, u.estado, u.fecha_creacion, u.id_rol
                      FROM usuarios u
                      WHERE DATE(u.fecha_creacion) = :fecha AND u.id_rol IN (1,2)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":fecha", $fecha);

        // Buscar por nombre de usuario
        } else {
            $query = "SELECT u.id_usuario, u.usuario, u.correo, u.estado, u.fecha_creacion, u.id_rol
                      FROM usuarios u
                      WHERE u.usuario LIKE :criterio AND u.id_rol IN (1,2)";
            $stmt = $this->conn->prepare($query);
            $criterioTexto = "%".$criterio."%";
            $stmt->bindParam(":criterio", $criterioTexto);
        }
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

        // Guardar usuario 
        public function guardarUsuario($data){
            try {
                if(isset($data['id_usuario']) && $data['id_usuario'] != ""){
                    // Validar duplicados excluyendo el mismo usuario
                    $check = $this->conn->prepare("SELECT COUNT(*) FROM usuarios 
                                                   WHERE (usuario=:usuario OR correo=:correo) 
                                                   AND id_usuario != :id");
                    $check->bindParam(":usuario", $data['usuario']);
                    $check->bindParam(":correo", $data['correo']);
                    $check->bindParam(":id", $data['id_usuario']);
                    $check->execute();
                    if($check->fetchColumn() > 0){
                        return "El usuario o correo ya existe, por favor ingrese uno diferente.";
                    }

                    // UPDATE
                    $query = "UPDATE usuarios 
                              SET usuario=:usuario, correo=:correo, password=:password, estado=:estado, id_rol=:id_rol 
                              WHERE id_usuario=:id";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(":id", $data['id_usuario']);
                } else {
                    // Validar duplicados para INSERT
                    $check = $this->conn->prepare("SELECT COUNT(*) FROM usuarios 
                                                   WHERE usuario=:usuario OR correo=:correo");
                    $check->bindParam(":usuario", $data['usuario']);
                    $check->bindParam(":correo", $data['correo']);
                    $check->execute();
                    if($check->fetchColumn() > 0){
                        return "El usuario o correo ya existe, por favor ingrese uno diferente.";
                    }

                    // INSERT
                    $query = "INSERT INTO usuarios (usuario, correo, password, estado, id_rol, fecha_creacion) 
                              VALUES (:usuario, :correo, :password, :estado, :id_rol, NOW())";
                    $stmt = $this->conn->prepare($query);
                }

                $stmt->bindParam(":usuario", $data['usuario']);
                $stmt->bindParam(":correo", $data['correo']);
                $stmt->bindParam(":password", $data['password']);
                $stmt->bindParam(":estado", $data['estado']);
                $stmt->bindParam(":id_rol", $data['id_rol'], PDO::PARAM_INT);

                return $stmt->execute();
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        // Inactivar usuario
        public function eliminarUsuario($id){
            $query = "UPDATE usuarios SET estado='INACTIVO' WHERE id_usuario=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            return $stmt->execute();
        }

        //  Buscar usuario por ID
        public function buscarUsuario($id){
            $query = "SELECT * FROM usuarios WHERE id_usuario=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        //  Buscar usuario por nombre de usuario (para login)
        public function buscarPorUsuario($usuario){
            $query = "SELECT * FROM usuarios WHERE usuario=:usuario LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":usuario", $usuario, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
