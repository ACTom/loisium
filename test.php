<?php

require "vendor/autoload.php";

use loisium\Request, loisium\Response;

$request = new Request();
$response = $request->post('http://localhost:8000/', ['user' => 'admin', 'pass' => 'admin']);
exit($response->getBody());
var_dump($response);
var_dump($request);
