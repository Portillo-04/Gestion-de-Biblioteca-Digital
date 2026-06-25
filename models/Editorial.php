<?php
class Editorial {
    private $conn;
    private $table_name = "editoriales";

    public function __construct($db){
        $this->conn = $db;
    }

    //  Listar todas las editoriales 
    public function listarEditoriales(){
        $query = "SELECT id_editorial, nombre_editorial, estado 
                  FROM " . $this->table_name . " 
                  ORDER BY nombre_editorial ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  Guardar editorial 
    public function guardarEditorial($data){
        try {
            if(isset($data['id_editorial']) && $data['id_editorial'] != ""){
                // UPDATE
                $query = "UPDATE " . $this->table_name . " 
                          SET nombre_editorial=:nombre_editorial, estado=:estado 
                          WHERE id_editorial=:id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id", $data['id_editorial']);
            } else {
                // INSERT 
                if (empty($data['estado'])) {
                    $data['estado'] = "ACTIVO";
                }
                $query = "INSERT INTO " . $this->table_name . " (nombre_editorial, estado)
                          VALUES (:nombre_editorial, :estado)";
                $stmt = $this->conn->prepare($query);
            }

            $stmt->bindParam(":nombre_editorial", $data['nombre_editorial']);
            $stmt->bindParam(":estado", $data['estado']);
            $stmt->execute();

            if(!isset($data['id_editorial']) || $data['id_editorial'] == ""){
                return $this->conn->lastInsertId();
            } else {
                return $data['id_editorial'];
            }
        } catch (PDOException $e) {
            return "Error al guardar editorial: " . $e->getMessage();
        }
    }

    //  Inactivar editorial 
    public function eliminarEditorial($id){
        $query = "UPDATE " . $this->table_name . " SET estado='INACTIVO' WHERE id_editorial=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    //  Buscar editorial por ID
    public function buscarEditorial($id){
        $query = "SELECT id_editorial, nombre_editorial, estado 
                  FROM " . $this->table_name . " 
                  WHERE id_editorial=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //  Buscar editoriales por nombre o estado
    public function buscarEditoriales($criterio){
        $query = "SELECT id_editorial, nombre_editorial, estado 
                  FROM " . $this->table_name . " 
                  WHERE nombre_editorial LIKE :criterio 
                     OR estado = :estadoExacto
                  ORDER BY nombre_editorial ASC";

        $stmt = $this->conn->prepare($query);

        // Para nombre usamos LIKE
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
