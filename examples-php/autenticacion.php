<?php
function api_interoperabilidad_ant($url,$params,$method){
		
    //$postdata = http_build_query($params);
    $postdata = json_encode($params);
    
    $opts = array('http' =>
            array(
            'method' => $method,
            'header' => 'Content-type: application/json',
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

$url = "http://127.0.0.1:8080/componente-proxy/rest/pide/autenticacion";

$param = array(
    "userAccessApi" => "user_access_api",
);

$resp=api_interoperabilidad_ant($url, $param, 'POST');
if($resp!= NULL){
    if($resp['error']!=null){
        echo $resp['error'];
        return;
    }
}

var_dump($resp);

?>