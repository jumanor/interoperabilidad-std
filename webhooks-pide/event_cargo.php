<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

function logger(string $archivoRuta, string $lineaAEscribir)
{
    // Abre el archivo en modo de "append" (agregar al final).
    // El flag 'a' crea el archivo si no existe.
    $archivoManejador = fopen($archivoRuta, 'a');

    if ($archivoManejador) {
        $fechaHora = date('Y-m-d H:i:s');
        // Agrega un salto de línea al final de la línea a escribir (opcional, pero común).
        $lineaConSalto = '[' . $fechaHora . '] '. $lineaAEscribir . PHP_EOL;

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

function cambiarEstadoToRecibidoSGD($vnumregstd,$cflgest){
    
    if($cflgest=="R"){
        logger("event_cargo.log",$vnumregstd." ".$cflgest);

    }
    if($cflgest=="O"){//documento observado
        logger("event_cargo.log",$vnumregstd." ".$cflgest);

    }    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $data = $_POST; 
    //varios campos use var_dump($data)
    $vnumregstd=$data["vnumregstd"];
    $cflgest=$data["cflgest"];

    try{

        cambiarEstadoToRecibidoSGD($vnumregstd,$cflgest);
            
        $response = [
            'estado' => "0000",
            'data' => "",
            'error' => null
        ];
        
        http_response_code(200); // OK
        echo json_encode($response);

    }catch(Exception $ex){
        
        $response = [
            'estado' => "-1",
            'data' => null,
            'error' => $ex->getMessage()
        ];
        
        http_response_code(200); // OK
        echo json_encode($response);     
        
    }

   
} else {
    // Si no es POST, devolver error
    http_response_code(405); // Método no permitido
    header('Content-Type: text/plain');
    echo 'Método no permitido. Use POST.';
}
?>
