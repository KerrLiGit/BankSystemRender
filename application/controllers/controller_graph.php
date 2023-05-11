<?php

/**
 * @OA\Schema(
 *     title="Controller_Auth",
 *     description="Контроллер для просмотра состояния кредита",
 *     @OA\Xml(
 *         name="Controller_Operator"
 *     )
 * )
 */

class Controller_Graph extends Controller {

	public function __construct() {
		$this->model = new Model_Graph();
		$this->view = new View();
	}

	/**
	 * @OA\Get(
	 *   path="/graph",
	 *   tags={"graph"},
	 *   summary="Страница оператора для просмотра состояния кредита клиента",
	 *   operationId="graph",
	 *   description="Страница оператора, на которой присутствует информация по кредиту клиента.",
	 *
	 *   @OA\Parameter(
	 *      name="token",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="client_uuid",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="credit_id",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *      description="Success",
	 *      @OA\MediaType(
	 *           mediaType="html",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Unauthorized (Неверный токен или роль сотрудника, или отсутствует текущий клиент)",
	 *      @OA\MediaType(
	 *           mediaType="html",
	 *      )
	 *   ),
	 *)
	 *
	 * @return null
	 * @throws Exception
	 */
	public function action_index() {
		Session::safe_session_start();
		Session::auth($_SESSION['token']);
		Session::check_client($_SESSION['client_uuid']);
		try {
			$data = $this->model->get_data($_SESSION['token'], $_SESSION['client_uuid'], $_POST['credit_id']);
			$this->view->generate('view_graph.php', 'view_header.php', $data);
		}
		catch (Exception $exception) {
			$_SESSION['message']['graph_credit'] = 'Информация по кредиту не найдена: ' . $exception->getMessage();
			header('Location: /client#graph_credit');
		}
	}
}