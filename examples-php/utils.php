
<?php
function generarNUMREGSTD() {
    $numero = rand(0, 99999);
    return sprintf("%05d", $numero);
}
function api_interoperabilidad_ant($url,$params,$method, $token = null){
		
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
?>