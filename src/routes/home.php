<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', function ($request, $response, $args) {
	$args['messages'] = $this->get('settings')['messages'];
	$args['servers'] = $this->get('settings')['servers'];

	return $this->view->render($response, 'index.html', $args);
});