<?php


/**
 * @OA\Schema(
 *     title="Controller_Operator",
 *     description="Контроллер для взаимодействия со страницей оператора",
 *     @OA\Xml(
 *         name="Controller_Operator"
 *     )
 * )
 */

class Controller_Operator extends Controller {

	function __construct() {
		$this->model = new Model_Operator();
		$this->view = new View();
	}

	/**
	 * @OA\Get(
	 *   path="/operator",
	 *   tags={"operator"},
	 *   summary="Страница оператора",
	 *   operationId="operator",
	 *   description="Страница оператора, на которой присутствует функцилнал, предшествцющий работе с клиентом.",
	 *
	 *   @OA\Parameter(
	 *      name="token",
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
	 *      description="Unauthorized (Неверный токен или роль сотрудника)",
	 *      @OA\MediaType(
	 *           mediaType="html",
	 *      )
	 *   ),
	 *)
	 *
	 * @return null
	 * @throws Exception
	 */
	function action_index() {
		Session::safe_session_start();
		$user = Session::auth($_SESSION['token']);
		if ($user['role'] == 'admin' || $user['role'] == 'operator') {
			$data = $this->model->get_data($_SESSION['token'], null, $_SESSION['message']);
			unset($_SESSION['message']);
			$this->view->generate('view_operator.php', 'view_header.php', $data);
		}
		else {
			throw new LogicException(403);
		}
	}

	/**
	 * @OA\Post(
	 *   path="/operator/find_by_passport",
	 *   tags={"operator"},
	 *   summary="Клиент по паспорту",
	 *   operationId="operator_find_by_passport",
	 *   description="Функционал в виде формы, позволяющий найти uuid клиента по паспорту для дальнейшей работы с ним.",
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
	 *      name="passport",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *          type="string"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *      description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Unauthorized (Неверный токен или роль сотрудника)",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *)
	 *
	 * @return null
	 * @throws Exception
	 */
	function action_find_by_passport() {
		Session::safe_session_start();
		$user = Session::auth($_SESSION['token']);
		if ($user['role'] == 'admin' || $user['role'] == 'operator') {
			try {
				$_SESSION['client_uuid'] = $this->model->find_by_passport($_POST['passport']);
				header('Location: /client');
			} catch (Exception $exception) {
				$_SESSION['message']['client'] = $exception->getMessage();
				header('Location: /operator');
			}
		}
		else {
			throw new LogicException(403);
		}
	}

	/**
	 * @OA\Post(
	 *   path="/operator/create_client",
	 *   tags={"operator"},
	 *   summary="Создание клиента",
	 *   operationId="operator_create_client",
	 *   description="Функционал в виде формы, позволяющий создать клиента по паспорту для дальнейшей работы с ним.",
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
	 *      name="client_parameters",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *          type="string"
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=200,
	 *      description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *   @OA\Response(
	 *      response=403,
	 *      description="Unauthorized (Неверный токен или роль сотрудника)",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   ),
	 *)
	 *
	 * @return null
	 * @throws Exception
	 */
	function action_create_client() {
		Session::safe_session_start();
		$user = Session::auth($_SESSION['token']);
		if ($user['role'] == 'admin' || $user['role'] == 'operator') {
			try {
				$this->model->create_client($_POST);
				$_SESSION['message']['create_client'] = 'Клиент успешно создан';
			} catch (Exception $exception) {
				$_SESSION['message']['create_client'] = 'Неправильный формат данных в полях:' . $exception->getMessage();
			}
			header('Location: /operator');
		}
		else {
			throw new LogicException(403);
		}
	}

}