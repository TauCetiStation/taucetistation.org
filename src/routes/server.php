<?php

use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/server/{name}[/{format:json}]', function ($request, $response, $args) {
	$settings = $this->get('settings')['servers'];

	//check if server in configs
	if(!array_key_exists($args['name'], $settings)) {
		$notFoundHandler = $this->get('notFoundHandler');
		return $notFoundHandler($request, $response);
	}

	$server = $settings[$args['name']];

	$byond = new byond;
	$data = $byond->getServerStatus($server['address'], $server['port']);

	if(isset($args['format']) && $args['format']==='json') {
		return $response->withJson($data)->withHeader('Access-Control-Allow-Origin', '*');
	} else {
		$args['data'] = $data;
		return $this->view->render($response, 'server.html', $args);
	}
});

$app->get('/server/{name}/link', function ($request, $response, $args) {
	$settings = $this->get('settings')['servers'];

	//check if server in configs
	if(!array_key_exists($args['name'], $settings)) {
		$notFoundHandler = $this->get('notFoundHandler');
		return $notFoundHandler($request, $response);
	}

	$data['name'] = $settings[$args['name']]['name'];
	$data['address'] = $settings[$args['name']]['address'];
	$data['port'] = $settings[$args['name']]['port'];

	return $this->view->render($response, 'server_link.html', $data);
});
