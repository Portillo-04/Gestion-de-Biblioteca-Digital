<?php
class Categoria {
    private $conn;
    private $table_name = "categorias"; // nombre exacto de la tabla

    public function __construct($db) {
        $this->conn = $db;
    }

    //  Guardar o actualizar categoría
    public function guardarCategoria($data) {
        try {
            if (!empty($data['id_categoria'])) {
                // UPDATE
                $query = "UPDATE " . $this->table_name . " 
                          SET nombre_categoria = :nombre_categoria, descripcion = :descripcion, estado = :estado 
                          WHERE id_categoria = :id_categoria";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(":id_categoria", $data['id_categoria']);
            } else {
                // INSERT 
                if (empty($data['estado'])) {
                    $data['estado'] = "ACTIVO";
                }
                $query = "INSERT INTO " . $this->table_name . " (nombre_categoria, descripcion, estado) 
                          VALUES (:nombre_categoria, :descripcion, :estado)";
                $stmt = $this->conn->prepare($query);
            }

            $stmt->bindParam(":nombre_categoria", $data['nombre_categoria']);
            $stmt->bindParam(":descripcion", $data['descripcion']);
            $stmt->bindParam(":estado", $data['estado']);

            return $stmt->execute();
        } catch (PDOException $e) {
            return "Error al guardar categoría: " . $e->getMessage();
        }
    }

    //  Listar categorías con búsqueda 
    public function listarCategorias($busqueda = '') {
        $query = "SELECT id_categoria, nombre_categoria, descripcion, estado 
                  FROM " . $this->table_name . " WHERE 1=1";

        if (!empty($busqueda)) {
            $query .= " AND (nombre_categoria LIKE :nombre OR estado = :estado)";
        }

        $stmt = $this->conn->prepare($query);

        if (!empty($busqueda)) {
            $like = "%$busqueda%";
            $stmt->bindParam(":nombre", $like);
            $stmt->bindParam(":estado", $busqueda); // comparación exacta
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar una categoría por ID
    public function buscarCategoria($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_categoria = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Inactivar categoría 
    public function inactivarCategoria($id) {
        $query = "UPDATE " . $this->table_name . " SET estado = 'INACTIVO' WHERE id_categoria = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
