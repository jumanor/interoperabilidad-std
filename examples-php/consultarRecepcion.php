<?php


function api_interoperabilidad_ant($url,$params,$method, $token = null){
		
    $postdata="";
    if($params != null){
        $postdata = json_encode($params);
    }
    
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
    
}


$vnumregstd = $argv[1];
$vrucentrec= $argv[2] ?? '00000000000';

$paramAuth=array("userAccessApi"=>"user_access_api");
$urlAuth= "http://127.0.0.1:8080/componente-proxy/rest/pide/autenticacion";
$resp=api_interoperabilidad_ant($urlAuth, $paramAuth, 'POST');
if($resp!= NULL){
    if($resp['error']!=null){
        echo $resp['error'];
        return;
    }
    $token = $resp['data'];
    $url = "http://127.0.0.1:8080/componente-proxy/rest/pide/consultar/recepcion/".$vrucentrec."/".$vnumregstd."/".$token;
    $resp=api_interoperabilidad_ant($url, null, 'GET',$token);
    if($resp!= NULL && $resp['data'] != null){
        if($resp['data']['cflgest']=="R"){
            $pdf = $resp['data']['bcarstd'];
            $pdfData = base64_decode($pdf,true);
            
            if ($pdfData === false) {
                echo "Error: La cadena Base64 contiene caracteres inválidos.";
            } else {
                // Guardar el archivo
                if (file_put_contents("consulta_cargo.pdf", $pdfData) !== false) {
                    echo "PDF file has been successfully decoded and saved.";
            
                } else {
                    echo "Failed to save the PDF file.";
                }
            }

        }
    }
    $resp["data"]['bcarstd']="";
    var_dump($resp);
}

?>
