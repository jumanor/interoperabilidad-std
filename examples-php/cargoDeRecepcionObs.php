<?php
function api_interoperabilidad_ant($url,$params,$method,$token = null){
		
    //$postdata = http_build_query($params);
    $postdata = json_encode($params);
    
    // Inicializar las cabeceras como un array
    $headers = ['Content-type: application/json'];

    // Agregar el token a la cabecera si se proporciona
    if ($token !== null) {
        $headers[] = "Authorization: Bearer {$token}";
    }

    $opts = array('http' =>
            array(
            'method' => $method,
            'header' => implode("\r\n", $headers),
            //'header' => 'Content-type: application/json',
            //'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postdata
            )
        );

    $context = stream_context_create($opts);
    
    @$response = file_get_contents($url, false, $context);

    if($response === FALSE){

        //echo " FALLO CONEXION ";
        return NULL;
    }
    
    $obj=json_decode($response,true);	
    return $obj;
    
}/////////////////////////////////////////////////////////////////////////////// 
function api_interoperabilidad($url, $params, $method) {
    // Convertir los parámetros a JSON
    $postdata = json_encode($params);

    // Inicializar cURL
    $ch = curl_init($url);

    // Configurar opciones de cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Devolver la respuesta como string
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method)); // Método HTTP (POST, GET, etc.)
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json' // Encabezado para enviar JSON
    ]);

    // Si el método no es GET, agregar los datos en el cuerpo
    if (strtoupper($method) !== 'GET') {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    }

    // Ejecutar la solicitud
    $response = curl_exec($ch);

    // Verificar si hubo errores
    if ($response === false) {
        //echo " FALLO CONEXION ";
        curl_close($ch);
        return null;
    }

    // Cerrar la sesión cURL
    curl_close($ch);

    // Decodificar la respuesta JSON a un arreglo asociativo
    $obj = json_decode($response, true);
    return $obj;
}
$vnumregstd = $argv[1];

$bcarstd=base64_encode(file_get_contents("cargo_test.pdf"));



// Datos a enviar en formato JSON (simulando la estructura de JSCargo)
$param = array(
    "vnumregstd" => $vnumregstd,        // Número único en la tabla
    "vuniorgstd" => "UNIDAD001",    // Unidad organizativa
    "ccoduniorgstd" => "COD001",    // Código de unidad organizativa
    "vusuregstd" => "usuario1",     // Usuario que registra
    "bcarstd" => $bcarstd,               // Bandera de cargo (ejemplo)
    "vobs" => "Documento Observado",// Observación
);

$paramAuth=array("userAccessApi"=>"user_access_api");
$urlAuth= "http://127.0.0.1:8080/componente-proxy/rest/pide/autenticacion";
$resp=api_interoperabilidad_ant($urlAuth, $paramAuth, 'POST');
if($resp!= NULL){
    if($resp['error']!=null){
        echo $resp['error'];
        return;
    }
    $token = $resp['data'];
    $url = "http://127.0.0.1:8080/componente-proxy/rest/pide/cargo/observado";
    $resp=api_interoperabilidad_ant($url, $param, 'POST',$token);
    if($resp!= NULL){
        if($resp['error']!=null){
            echo $resp['error'];
            return;
        }
    }
    var_dump($resp);
}

?>