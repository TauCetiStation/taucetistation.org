<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/servers/json', function ($request, $response) {
	$data = $this->get('settings')['servers'];
	return $response->withJson($data)->withHeader('Access-Control-Allow-Origin', '*');
});
