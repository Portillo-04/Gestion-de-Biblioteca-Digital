<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../models/Reporte.php";
require_once __DIR__ . "/../vendor/autoload.php"; // Dompdf

use Dompdf\Dompdf;

$database = new Database();
$db = $database->getConnection();
$reporteObj = new Reporte($db);

$accion = $_GET['accion'] ?? '';

if($accion === 'prestamos_activos'){
    $data = $reporteObj->prestamosActivos();
    generarPDF($data, "Reporte de Préstamos Activos");
}
elseif($accion === 'libros_mas_prestados'){
    $data = $reporteObj->librosMasPrestados();
    generarPDF($data, "Libros más Prestados");
}
elseif($accion === 'usuarios_activos'){
    $data = $reporteObj->usuariosMasActivos();
    generarPDF($data, "Usuarios más Activos");
}
else {
    header("Location: ../views/Reporte/reportes.php");
    exit();
}

function generarPDF($rows, $titulo){
    $fecha = date("d/m/Y H:i");
    $usuario = $_SESSION['usuario']; 

    // Plantilla HTML, fecha y usuario
    $html = "
    <html>
    <head>
        <style>
            body { font-family: DejaVu Sans, sans-serif; }
            .header { text-align: center; margin-bottom: 20px; }
            .header img { width: 80px; float: left; }
            .header h2 { margin: 0; }
            .header p { font-size: 12px; color: #555; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #000; padding: 6px; font-size: 12px; }
            th { background-color: #007bff; color: #fff; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .no-data { text-align: center; font-style: italic; margin-top: 20px; }
            .footer { margin-top: 30px; font-size: 11px; text-align: right; color: #555; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Biblioteca Digital</h2>
            <p>$titulo<br>Generado el $fecha</p>
        </div>";

    if(empty($rows)){
        $html .= "<p class='no-data'>No hay registros en el reporte</p>";
    } else {
        $html .= "<table>
                    <tr>";
        foreach(array_keys($rows[0]) as $col){
            $html .= "<th>".htmlspecialchars($col)."</th>";
        }
        $html .= "</tr>";

        foreach($rows as $row){
            $html .= "<tr>";
            foreach($row as $col){
                $html .= "<td>".htmlspecialchars($col)."</td>";
            }
            $html .= "</tr>";
        }

        $html .= "</table>";
    }

    
    $html .= "<div class='footer'>Reporte generado por: $usuario</div>";

    $html .= "</body></html>";

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("reporte.pdf", ["Attachment" => true]);
    exit();
}
?>
