<?php
class Prestamo {
    private $conn;
    private $table_name = "detalle_prestamo";

    public function __construct($db){
        $this->conn = $db;
    }

    //  Listar préstamos
    public function listarPrestamos(){
        $query = "SELECT p.id_detalle, p.id_prestamo, p.fecha_prestamo, p.fecha_entrega, p.estado_libro,
                         e.id_estudiante, e.carnet, e.nombre_completo,
                         u.id_usuario, u.usuario,
                         l.titulo AS libro
                  FROM " . $this->table_name . " p
                  INNER JOIN prestamos pr ON p.id_prestamo = pr.id_prestamo
                  INNER JOIN estudiantes e ON pr.id_estudiante = e.id_estudiante
                  INNER JOIN usuarios u ON pr.id_usuario = u.id_usuario
                  INNER JOIN libros l ON p.id_libro = l.id_libro
                  ORDER BY p.fecha_prestamo DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  Registrar préstamo 
    public function registrarPrestamo($id_prestamo, $id_libro, $fecha_entrega){
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_prestamo, id_libro, fecha_prestamo, fecha_entrega, estado_libro)
                  VALUES (:id_prestamo, :id_libro, NOW(), :fecha_entrega, 'PRESTADO')";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_prestamo", $id_prestamo);
        $stmt->bindParam(":id_libro", $id_libro);
        $stmt->bindParam(":fecha_entrega", $fecha_entrega);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    //  Registrar devolución (usamos la fecha_entrega como fecha real de devolución)
    public function registrarDevolucion($idDetalle){
        $query = "UPDATE " . $this->table_name . " 
                  SET fecha_entrega = NOW(), estado_libro = 'DEVUELTO'
                  WHERE id_detalle = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $idDetalle, PDO::PARAM_INT);
        return $stmt->execute();
    }

    //  Buscar préstamo por detalle
    public function buscarPrestamo($idDetalle){
        $query = "SELECT p.id_detalle, p.id_prestamo, p.fecha_prestamo, p.fecha_entrega, p.estado_libro,
                         e.id_estudiante, e.carnet, e.nombre_completo,
                         u.id_usuario, u.usuario,
                         l.titulo AS libro
                  FROM " . $this->table_name . " p
                  INNER JOIN prestamos pr ON p.id_prestamo = pr.id_prestamo
                  INNER JOIN estudiantes e ON pr.id_estudiante = e.id_estudiante
                  INNER JOIN usuarios u ON pr.id_usuario = u.id_usuario
                  INNER JOIN libros l ON p.id_libro = l.id_libro
                  WHERE p.id_detalle = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $idDetalle, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Verificar si estudiante tiene préstamo pendiente
    public function tienePrestamoPendiente($id_estudiante){
        $query = "SELECT COUNT(*) AS pendientes
                  FROM " . $this->table_name . " p
                  INNER JOIN prestamos pr ON p.id_prestamo = pr.id_prestamo
                  WHERE pr.id_estudiante = :id_estudiante
                    AND p.estado_libro = 'PRESTADO'
                    AND p.fecha_entrega IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_estudiante", $id_estudiante, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['pendientes'] > 0;
    }
}
?>
