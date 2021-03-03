<?php
//echo 
$url="http://SRV02:8000/SC/hs/site/get_product";

/*$headers=get_headers($url);
if ($headersss[0] != "HTTP/1.0 200 OK")
{echo "<pre>" ; exit (print_r($headers));} else echo "<pre>" ;
//echo "<pre>" ; exit (print_r($headers));/**/
$params = array(
    $_POST['name'] => $_POST['value'] 
   // "name" => "value"
);
$result = file_get_contents($url, false, stream_context_create(array(
    'http' => array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($params)
    )
)));
echo "<pre>".date("r")."\n" ;
if($result) print_r(json_decode($result));
print_r($_REQUEST);
echo "</pre>";
?>