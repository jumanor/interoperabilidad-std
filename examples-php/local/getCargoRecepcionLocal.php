<?php
require_once '../utils.php';

    $vnumregstd = $argv[1];

    $paramAuth=array("userAccessApi"=>"user_access_api");
    $urlAuth= "http://127.0.0.1:8080/componente-proxy/rest/pide/autenticacion";
    $resp=api_interoperabilidad_ant($urlAuth, $paramAuth, 'POST');
    if($resp==null){
        throw new Exception("Error en la autenticacion");
    }    
    if($resp['error']!=null){
        throw new Exception($resp['error']);
    } 

    $token = $resp['data'];
    $url = "http://127.0.0.1:8080/componente-proxy/rest/local/cargo/recepcionado/$vnumregstd/$token";


    $resp=api_interoperabilidad_ant($url, [], 'GET');
    if($resp==null){
        throw new Exception("Error en rest recepcion");
    }
    if($resp['error']!=null){
        throw new Exception($resp['error']);
    }

    $cargoPdf = base64_decode($resp["data"]["bcarstd"]);
    file_put_contents("cargo_recepcion_local.pdf", $cargoPdf);

    $resp["data"]["bcarstd"]="pdf";

    var_dump($resp);
?>