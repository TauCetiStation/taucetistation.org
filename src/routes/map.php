<?php

$app->get('/map', function ($request, $response) {
	return $this->view->render($response, 'map.html');
});
