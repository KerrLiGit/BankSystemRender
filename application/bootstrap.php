<?php

require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/route.php';
require_once 'core/session.php';
try {
	Route::start(); // запускаем маршрутизатор
}
catch (Exception $e) {
	require_once 'controllers/controller_error.php';
	require_once 'models/model_error.php';
	$controller = new Controller_Error();
	$controller->action_index($e->getMessage());
}