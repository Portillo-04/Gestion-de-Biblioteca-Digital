<?php
class Libro {
    private $conn;
    private $table_name = "libros";

    public function __construct($db){
        $this->conn = $db;
    }

    // Listar todos los libros
    public function listarLibros(){
        $query = "SELECT l.id_libro, l.codigo_libro, l.titulo, l.stock, l.estado,
                         e.id_editorial, e.nombre_editorial,
                         c.id_categoria, c.nombre_categoria,
                         (SELECT COUNT(*) 
                          FROM detalle_prestamo dp 
                          WHERE dp.id_libro = l.id_libro AND dp.estado_libro = 'PRESTADO') AS prestados
                  FROM " . $this->table_name . " l
                  LEFT JOIN editoriales e ON l.editorial = e.id_editorial
                  LEFT JOIN categorias c ON l.id_categoria = c.id_categoria
                  ORDER BY l.titulo ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar libro por ID
    public function buscarLibro($id_libro){
        $query = "SELECT l.id_libro, l.codigo_libro, l.titulo, l.stock, l.estado,
                         e.id_editorial, e.nombre_editorial,
                         c.id_categoria, c.nombre_categoria,
                         (SELECT COUNT(*) 
                          FROM detalle_prestamo dp 
                          WHERE dp.id_libro = l.id_libro AND dp.estado_libro = 'PRESTADO') AS prestados
                  FROM " . $this->table_name . " l
                  LEFT JOIN editoriales e ON l.editorial = e.id_editorial
                  LEFT JOIN categorias c ON l.id_categoria = c.id_categoria
                  WHERE l.id_libro = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_libro, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verificar si existe un código
    public function existeCodigo($codigo, $id_libro = null){
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE codigo_libro = :codigo";
        if($id_libro){
            $query .= " AND id_libro != :id_libro";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":codigo", $codigo);
        if($id_libro){
            $stmt->bindParam(":id_libro", $id_libro, PDO::PARAM_INT);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }

    // Agregar libro con categoría
    public function agregarLibro($codigo, $titulo, $editorial, $stock, $estado, $categoria){
        if($this->existeCodigo($codigo)){
            throw new Exception("Ese código ya existe, usa otro diferente.");
        }

        $query = "INSERT INTO " . $this->table_name . " 
                  (codigo_libro, titulo, editorial, stock, estado, id_categoria)
                  VALUES (:codigo, :titulo, :editorial, :stock, :estado, :categoria)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":codigo", $codigo);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":editorial", $editorial);
        $stmt->bindParam(":stock", $stock, PDO::PARAM_INT);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":categoria", $categoria, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Editar libro con categoría
    public function editarLibro($id_libro, $codigo, $titulo, $editorial, $stock, $estado, $categoria){
        if($this->existeCodigo($codigo, $id_libro)){
            throw new Exception("Ese código ya existe, usa otro diferente.");
        }

        $query = "UPDATE " . $this->table_name . " 
                  SET codigo_libro = :codigo, titulo = :titulo, editorial = :editorial, 
                      stock = :stock, estado = :estado, id_categoria = :categoria
                  WHERE id_libro = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":codigo", $codigo);
        $stmt->bindParam(":titulo", $titulo);
        $stmt->bindParam(":editorial", $editorial);
        $stmt->bindParam(":stock", $stock, PDO::PARAM_INT);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":categoria", $categoria, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id_libro, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Inactivar libro
    public function inactivarLibro($id_libro){
        return $this->cambiarEstado($id_libro, 'INACTIVO');
    }

    // Cambiar estado
    public function cambiarEstado($id_libro, $estado){
        $query = "UPDATE " . $this->table_name . " SET estado = :estado WHERE id_libro = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":estado", $estado);
        $stmt->bindParam(":id", $id_libro, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Marcar prestado
    public function marcarPrestado($id_libro){
        $query = "UPDATE " . $this->table_name . " 
                  SET stock = stock - 1, estado = CASE WHEN stock - 1 <= 0 THEN 'PRESTADO' ELSE estado END
                  WHERE id_libro = :id AND stock > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_libro, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Marcar disponible
    public function marcarDisponible($id_libro){
        $query = "UPDATE " . $this->table_name . " 
                  SET stock = stock + 1, estado = 'DISPONIBLE'
                  WHERE id_libro = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_libro, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Obtener autores
    public function obtenerAutoresLibro($id_libro){
        $query = "SELECT a.id_autor, a.nombre_autor, a.apellido
                  FROM autores a
                  INNER JOIN libro_autor la ON a.id_autor = la.id_autor
                  WHERE la.id_libro = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id_libro, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Búsqueda general
    public function buscarGeneral($termino){
        $query = "SELECT l.id_libro, l.codigo_libro, l.titulo, l.stock, l.estado,
                         e.nombre_editorial, c.nombre_categoria,
                         (SELECT COUNT(*) FROM detalle_prestamo dp 
                          WHERE dp.id_libro = l.id_libro AND dp.estado_libro = 'PRESTADO') AS prestados
                  FROM " . $this->table_name . " l
                  LEFT JOIN editoriales e ON l.editorial = e.id_editorial
                  LEFT JOIN categorias c ON l.id_categoria = c.id_categoria
                  WHERE l.codigo_libro LIKE :term
                     OR l.titulo LIKE :term
                     OR l.estado LIKE :term
                     OR c.nombre_categoria LIKE :term";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(":term", "%$termino%");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>