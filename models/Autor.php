<?php
class Autor {
    private $conn;
    private $table_name = "autores";

    public function __construct($db){
        $this->conn = $db;
    }

    // Listar autores 
    public function listarAutores(){
    $query = "SELECT id_autor, nombre_autor, apellido, nacionalidad, fecha_nacimiento, biografia, estado 
              FROM " . $this->table_name . " 
              ORDER BY nombre_autor ASC";
    $stmt = $this->conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar autores con múltiples filtros
    public function buscarAutores($criterio){
        $criterioTexto = "%".$criterio."%";

        $query = "SELECT id_autor, nombre_autor, apellido, nacionalidad, fecha_nacimiento, biografia, estado 
                  FROM " . $this->table_name . " 
                  WHERE nombre_autor LIKE :criterio 
                     OR apellido LIKE :criterio
                     OR nacionalidad LIKE :criterio
                     OR estado = :estado
                     OR DATE(fecha_nacimiento) = :fecha";

        $stmt = $this->conn->prepare($query);

        // Vincula criterio general
        $stmt->bindParam(":criterio", $criterioTexto);

        // Estado
        if(strtoupper($criterio) === "ACTIVO" || strtoupper($criterio) === "INACTIVO"){
            $stmt->bindParam(":estado", $criterio);
        } else {
            $stmt->bindValue(":estado", null, PDO::PARAM_NULL);
        }

        // Fecha formato DD-MM-YYYY
        $fechaValida = null;
        if(preg_match("/^\d{2}-\d{2}-\d{4}$/", $criterio)){
            $partes = explode("-", $criterio); // [DD, MM, YYYY]
            $fechaValida = $partes[2]."-".$partes[1]."-".$partes[0]; // YYYY-MM-DD
        }

        if($fechaValida){
            $stmt->bindParam(":fecha", $fechaValida);
        } else {
            $stmt->bindValue(":fecha", null, PDO::PARAM_NULL);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Guardar autor 
    public function guardarAutor($data){
        try {
            if(isset($data['id_autor']) && $data['id_autor'] != ""){
                // UPDATE
                $query = "UPDATE autores 
                          SET nombre_autor=:nombre_autor, apellido=:apellido, nacionalidad=:nacionalidad, 
                              fecha_nacimiento=:fecha_nacimiento, biografia=:biografia, estado=:estado 
                          WHERE id_autor=:id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id", $data['id_autor']);
            } else {
                // INSERT
                $query = "INSERT INTO autores (nombre_autor, apellido, nacionalidad, fecha_nacimiento, biografia, estado) 
                          VALUES (:nombre_autor, :apellido, :nacionalidad, :fecha_nacimiento, :biografia, :estado)";
                $stmt = $this->conn->prepare($query);
            }

            $stmt->bindParam(":nombre_autor", $data['nombre_autor']);
            $stmt->bindParam(":apellido", $data['apellido']);
            $stmt->bindParam(":nacionalidad", $data['nacionalidad']);
            $stmt->bindParam(":fecha_nacimiento", $data['fecha_nacimiento']);
            $stmt->bindParam(":biografia", $data['biografia']);
            $stmt->bindParam(":estado", $data['estado']);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            return "Error al guardar autor: " . $e->getMessage();
        }
    }

    // Inactivar autor
    public function eliminarAutor($id){
        $query = "UPDATE autores SET estado='INACTIVO' WHERE id_autor=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Buscar autor por ID
    public function buscarAutor($id){
        $query = "SELECT id_autor, nombre_autor, apellido, nacionalidad, fecha_nacimiento, biografia, estado 
                  FROM " . $this->table_name . " 
                  WHERE id_autor=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
