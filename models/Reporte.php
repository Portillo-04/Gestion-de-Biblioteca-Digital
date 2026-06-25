<?php
class Reporte {
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    // Reporte de préstamos activos
    public function prestamosActivos(){
        $query = "SELECT dp.id_detalle, l.titulo AS libro, e.nombre_completo AS estudiante, u.usuario,
                         dp.fecha_prestamo, dp.fecha_entrega, dp.estado_libro
                  FROM detalle_prestamo dp
                  INNER JOIN libros l ON dp.id_libro = l.id_libro
                  INNER JOIN prestamos p ON dp.id_prestamo = p.id_prestamo
                  INNER JOIN estudiantes e ON p.id_estudiante = e.id_estudiante
                  INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                  WHERE dp.estado_libro = 'PRESTADO'
                  ORDER BY dp.fecha_prestamo DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Libros más prestados
    public function librosMasPrestados(){
        $query = "SELECT l.titulo AS libro, COUNT(dp.id_detalle) AS total_prestamos
                  FROM detalle_prestamo dp
                  INNER JOIN libros l ON dp.id_libro = l.id_libro
                  GROUP BY l.titulo
                  ORDER BY total_prestamos DESC
                  LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Usuarios más activos
    public function usuariosMasActivos(){
        $query = "SELECT u.usuario, COUNT(dp.id_detalle) AS total_prestamos
                  FROM detalle_prestamo dp
                  INNER JOIN prestamos p ON dp.id_prestamo = p.id_prestamo
                  INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
                  GROUP BY u.usuario
                  ORDER BY total_prestamos DESC
                  LIMIT 10";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
