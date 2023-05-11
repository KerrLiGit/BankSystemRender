<?php

/**
 * @OA\Schema(
 *     title="Controller_Main",
 *     description="Контроллер для взаимодействия с главной страницей",
 *     @OA\Xml(
 *         name="Controller_Operator"
 *     )
 * )
 */

class Controller_Main extends Controller {

	function __construct() {
		$this->model = new Model_Main();
		$this->view = new View();
	}

	/**
	 * @OA\Get(
	 *   path="/",
	 *   tags={"auth"},
	 *   summary="Главная страница",
	 *   operationId="auth",
	 *   description="Пустая, редирект на /auth",
	 *
	 *   @OA\Response(
	 *      response=200,
	 *      description="Success",
	 *      @OA\MediaType(
	 *           mediaType="html",
	 *      )
	 *   )
	 *)
	 *
	 * @return null
	 * @throws Exception
	 */
	function action_index() {
		header("Location: /auth");
	}

}