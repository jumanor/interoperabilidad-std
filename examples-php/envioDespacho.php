<?php
require_once 'utils.php';

$bpdfdoc=base64_encode(file_get_contents("documento_test.pdf"));
//file_put_contents("test.pdf", $bpdfdoc);

$despacho = array();
//$despacho['vnumregstd'] = generarNUMREGSTD();
$despacho['bpdfdoc'] = $bpdfdoc;
$despacho['vusureg'] = "emachaca";
$despacho['vnomentrec'] = "GOBIERNO REGIONAL DE TACNA";
$despacho['vrucentrec'] = "20519752515";
$despacho['ccodtipdoc'] = "01"; // OFICIO
$despacho['vnumdoc'] = "1000 OTI-TEST-2020";
$despacho['vuniorgrem'] = "OFICINA TECNOLOGIA DE INFORMACION"; // UNIDAD ORGANICA REMITENTE
$despacho['vcoduniorgrem'] = "OTI";
$despacho['ctipdociderem'] = "1"; // DNI
$despacho['vnumdociderem'] = "40633367";
$despacho['vuniorgdst'] = "OFICINA DE SISTEMAS"; // UNIDAD ORGANICA DE DESTINO
$despacho['vnomdst'] = "ELMER MACHACA"; // NOMBRE DEL DESTINATARIO
$despacho['vnomcardst'] = "JEFE INMEDIATO"; // NOMBRE DEL CARGO DEL DESTINATARIO
$despacho['vasu'] = "no borrar test prueba";
$despacho['vnomdoc'] = "documento.pdf";
$despacho['snumfol'] = 1;

$anexo01=array();
$anexo01['vnomdoc']="anexo 01";

$anexo02=array();
$anexo02['vnomdoc']="anexo 02";

$despacho['lstanexos']=array();
$despacho['lstanexos'][0]=$anexo01;
$despacho['lstanexos'][1]=$anexo02;

//solo si hay anexos
$despacho['vurldocanx']="http://urldercargadeanexos.com";

/////////////////
function wsEnvioDespacho($despacho){
    
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
    $url = "http://127.0.0.1:8080/componente-proxy/rest/pide/despacho/enviado";
    $resp=api_interoperabilidad_ant($url, $despacho, 'POST',$token);
    if($resp==null){
        throw new Exception("Error en rest despacho");
    }
    if($resp['estado']!="0000"){
        throw new Exception($resp['error']);
    }

    return $resp['data'];
}

function persistirEnSGD($despacho){
    return generarNUMREGSTD();
}

//$pdo->beginTransaction();
try{
    
    $vnumregstd=persistirEnSGD($despacho);
    $despacho['vnumregstd'] = $vnumregstd;
    $respuesta=wsEnvioDespacho($despacho);
    
    //$pdo->commit();
    echo $respuesta;
    echo "\n";

}catch(Exception $ex){
    
    //$pdo->rollBack();    
    echo $ex->getMessage();
    echo "\n";
}

?>