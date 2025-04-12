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

function generarNUMREGSTD() {
    // Generar número aleatorio entre 0 y 99999
    $numero = rand(0, 99999);
    // Formatear el número a 5 dígitos rellenando con ceros a la izquierda
    return sprintf("%05d", $numero);
}

$vnumregstdref = $argv[1];

$bpdfdoc=base64_encode(file_get_contents("documento_test.pdf"));

$despacho = array();
$despacho['vnumregstd'] = generarNUMREGSTD();
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
$anexo01['vnomdoc']="anexo 01 subsanado";

$anexo02=array();
$anexo02['vnomdoc']="anexo 02 subsanado";

$despacho['lstanexos']=array();
$despacho['lstanexos'][0]=$anexo01;
$despacho['lstanexos'][1]=$anexo02;
//solo si hay anexos
$despacho['vurldocanx']="http://urldercargadeanexos.com";

$despacho['vnumregstdref'] = $vnumregstdref;


$paramAuth=array("userAccessApi"=>"user_access_api");
$urlAuth= "http://127.0.0.1:8080/componente-proxy/rest/pide/autenticacion";
$resp=api_interoperabilidad_ant($urlAuth, $paramAuth, 'POST');
if($resp!= NULL){
    if($resp['error']!=null){
        echo $resp['error'];
        return;
    }
    $token = $resp['data'];
    $url = "http://127.0.0.1:8080/componente-proxy/rest/pide/despacho/subsanado";
    $resp=api_interoperabilidad_ant($url, $despacho, 'POST',$token);
    if($resp!= NULL){
        if($resp['error']!=null){
            echo $resp['error'];
            return;
        }
    }

    var_dump($resp);
}

?>