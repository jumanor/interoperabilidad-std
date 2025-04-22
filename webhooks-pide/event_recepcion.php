<?php
// Configurar cabeceras para permitir solicitudes CORS (si pruebas desde otro dominio)
header("Access-Control-Allow-Origin: *");
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

function generarCodigoVnumRegStd() {
    $numero = rand(0, 99999);
    return sprintf("%05d", $numero);
}
function crearDocumentoSGD($bpdfdoc,$vnumdoc,$vasu){
   
   $datosBinarios = base64_decode($bpdfdoc);
   file_put_contents("documentoRecepcionado.pdf", $datosBinarios);

   $vnumregstd=generarCodigoVnumRegStd(); 
   logger("event_recepcion.log",$vnumregstd." ".$vnumdoc." ".$vasu);
    
   return $vnumregstd;     
}

// Verificar que la solicitud sea POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $data = $_POST; // Para formularios enviados como application/x-www-form-urlencoded
    
    //varios campos use var_dump($data)
    $vnumdoc=$data["vnumdoc"];
    $vasu=$data["vasu"];
    $bpdfdoc=$data["bpdfdoc"];
    
    try{

        $vnumregstd=crearDocumentoSGD($bpdfdoc,$vnumdoc,$vasu);
            
        $response = [
            'estado' => "0000",
            'data' => ["vnumregstd"=>$vnumregstd],
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
    http_response_code(405);
    header('Content-Type: text/plain');
    echo 'Método no permitido. Use POST.';
    
}
?>
