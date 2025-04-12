<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function escribirAlFinalDelArchivo(string $archivoRuta, string $lineaAEscribir)
{
    // Abre el archivo en modo de "append" (agregar al final).
    // El flag 'a' crea el archivo si no existe.
    $archivoManejador = fopen($archivoRuta, 'a');

    if ($archivoManejador) {
        // Agrega un salto de línea al final de la línea a escribir (opcional, pero común).
        $lineaConSalto = $lineaAEscribir . PHP_EOL;

        // Escribe la línea en el archivo.
        if (fwrite($archivoManejador, $lineaConSalto) !== false) {
            // Cierra el archivo.
            fclose($archivoManejador);
            return true; // Escritura exitosa.
        } else {
            // Error al escribir en el archivo.
            fclose($archivoManejador);
            return false;
        }
    } else {
        // No se pudo abrir el archivo.
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados en el cuerpo de la solicitud
    $data = $_POST; 
    $vnumregstd=$data["vnumregstd"];
    $cflgest=$data["cflgest"];

    if($cflgest=="R"){
        escribirAlFinalDelArchivo("event_cargo.log",$vnumregstd." ".$cflgest);

    }
    if($cflgest=="O"){//documento observado
        escribirAlFinalDelArchivo("event_cargo.log",$vnumregstd." ".$cflgest);

    }

    // Construir respuesta con los datos recibidos
    $response = [
        'estado' => "0000",
        'data' => "",
        'error' => ""
    ];

   
    // Devolver respuesta en formato JSON
    http_response_code(200); // OK
    echo json_encode($response);
} else {
    // Si no es POST, devolver error
    http_response_code(405); // Método no permitido
    header('Content-Type: text/plain');
    echo 'Método no permitido. Use POST.';
}
?>
