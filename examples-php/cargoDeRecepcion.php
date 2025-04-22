<?php
require_once 'utils.php';

$vnumregstd = $argv[1];

$bcarstd=base64_encode(file_get_contents("cargo_test.pdf"));

// Datos a enviar en formato JSON (simulando la estructura de JSCargo)
$param = array(
    "vnumregstd" => $vnumregstd,        // Número único en la tabla
    "vuniorgstd" => "UNIDAD001",    // Unidad organizativa
    "ccoduniorgstd" => "COD001",    // Código de unidad organizativa
    "vusuregstd" => "usuario1",     // Usuario que registra
    "bcarstd" => $bcarstd,               // Bandera de cargo (ejemplo)
    //"vobs" => "Observación ejemplo",// Observación
);

function wsCargoRecepcion($param){
    
    $paramAuth=array("userAccessApi"=>"user_access_api");
    $urlAuth= "http://127.0.0.1:8080/componente-proxy/rest/pide/autenticacion";
    $resp=api_interoperabilidad_ant($urlAuth, $paramAuth, 'POST');
    if($resp==null){
        throw new Exception("Error en la autenticacion");
    }    
    if($resp['estado']!="0000"){
        throw new Exception($resp['error']);
    }
    
    $token = $resp['data'];
    $url = "http://127.0.0.1:8080/componente-proxy/rest/pide/cargo/recepcionado";
    $resp=api_interoperabilidad_ant($url, $param, 'POST',$token);
    if($resp==null){
        throw new Exception("Error en rest cargo");
    }
    if($resp['estado']!="0000"){
        throw new Exception($resp['error']);
    }

    return $resp['data'];

}
function persistirEnSGD($param){

}

//$pdo->beginTransaction();
try{
    
    persistirEnSGD($param);
    $respuesta=wsCargoRecepcion($param);
    
    //$pdo->commit();
    echo $respuesta;
    echo "\n";

}catch(Exception $ex){
    
    //$pdo->rollBack();    
    echo $ex->getMessage();
    echo "\n";
}

?>