<?php

$publicHash = '29c30265b5ebe7f47b44b6791ec12d3924a1b641d0fc7aed10702767627f7d73';
$privateHash = '3cdf2dfb7a68fd11b892e2a89304bee215083ae8ab2a054e9ca688785ba81150';

$REQUEST_METHOD= 'post';
$REQUEST_URI = '/api/v1/contacts';

$data = array("name" => "Dinh Thanh Nam","phone" => "0985589635","notes"=> "Engineer at TMA Solutions","user_id"=> "11");

$data = json_encode($data);
echo $data;

$content = strtolower($REQUEST_METHOD . $REQUEST_URI . $data);
// echo $content;

$hash = hash_hmac('sha256', $content, $privateHash);


echo $hash;
?>