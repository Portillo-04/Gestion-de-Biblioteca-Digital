<?php
class Estudiante {
    private $conn;
    private $table_name = "estudiantes";

    public function __construct($db){
        $this->conn = $db;
    }

    //  Listar todos los estudiantes
    public function listarEstudiantes(){
        $query = "SELECT id_estudiante, carnet, nombre_completo, carrera, telefono, correo, estado 
                  FROM " . $this->table_name . " 
                  ORDER BY nombre_completo ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  Buscar estudiante por ID
    public function buscarEstudiante($id){
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_estudiante = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //  Guardar nuevo estudiante
    public function guardarEstudiante($data){
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                      (carnet, nombre_completo, carrera, telefono, correo, estado) 
                      VALUES (:carnet, :nombre_completo, :carrera, :telefono, :correo, :estado)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":carnet", $data['carnet']);
            $stmt->bindParam(":nombre_completo", $data['nombre_completo']);
            $stmt->bindParam(":carrera", $data['carrera']);
            $stmt->bindParam(":telefono", $data['telefono']);
            $stmt->bindParam(":correo", $data['correo']);
            $stmt->bindParam(":estado", $data['estado']);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return "El carnet ya existe, por favor ingrese uno diferente.";
            }
            return "Error al guardar estudiante: " . $e->getMessage();
        }
    }

    //  Actualizar estudiante existente
    public function actualizarEstudiante($data){
        try {
            $query = "UPDATE " . $this->table_name . " 
                      SET carnet=:carnet, nombre_completo=:nombre_completo, carrera=:carrera, 
                          telefono=:telefono, correo=:correo, estado=:estado 
                      WHERE id_estudiante=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $data['id_estudiante']);
            $stmt->bindParam(":carnet", $data['carnet']);
            $stmt->bindParam(":nombre_completo", $data['nombre_completo']);
            $stmt->bindParam(":carrera", $data['carrera']);
            $stmt->bindParam(":telefono", $data['telefono']);
            $stmt->bindParam(":correo", $data['correo']);
            $stmt->bindParam(":estado", $data['estado']);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return "El carnet ya existe, por favor ingrese uno diferente.";
            }
            return "Error al actualizar estudiante: " . $e->getMessage();
        }
    }

    //  Inactivar estudiante 
    public function inactivarEstudiante($id){
        $query = "UPDATE " . $this->table_name . " SET estado = 'INACTIVO' WHERE id_estudiante = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    //  Buscar estudiantes por criterio 
    public function buscarEstudiantes($criterio){
        $query = "SELECT e.id_estudiante, e.carnet, e.nombre_completo, e.carrera, e.telefono, e.correo, e.estado
                  FROM " . $this->table_name . " e
                  WHERE e.carnet LIKE :criterio
                     OR e.nombre_completo LIKE :criterio
                     OR e.carrera LIKE :criterio
                     OR e.estado = :estadoExacto
                  ORDER BY e.nombre_completo ASC";

        $stmt = $this->conn->prepare($query);

        // Para nombre, carnet y carrera usamos LIKE
        $like = "%".$criterio."%";
        $stmt->bindParam(":criterio", $like);

        // Para estado usamos coincidencia exacta
        $estadoExacto = strtoupper(trim($criterio));
        if(in_array($estadoExacto, ["ACTIVO","INACTIVO"])){
            $stmt->bindParam(":estadoExacto", $estadoExacto);
        } else {
            // Si no es estado válido, ponemos un valor imposible
            $stmt->bindValue(":estadoExacto", "__NO_MATCH__");
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
