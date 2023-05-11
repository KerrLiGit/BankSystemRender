<?php

/**
 * @OA\Schema(
 *     title="Controller_Auth",
 *     description="Контроллер для взаимодействия со страницей входа",
 *     @OA\Xml(
 *         name="Controller_Operator"
 *     )
 * )
 */

class Controller_Auth extends Controller {

	function __construct() {
		$this->model = new Model_Auth();
		$this->view = new View();
	}

	/**
	 * @OA\Get(
	 *   path="/auth",
	 *   tags={"auth"},
	 *   summary="Страница авторизации",
	 *   operationId="auth",
	 *   description="Страница, позволяющая оператору выполнить авторизацию или выйти из аккаунта.",
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
		Session::safe_session_start();
		$data = $this->model->get_data(null, null, $_SESSION['message']);
		unset($_SESSION['message']);
		$this->view->generate('', 'view_auth.php', $data);
	}

	/**
	 * @OA\Post(
	 *   path="/auth/signin",
	 *   tags={"auth"},
	 *   summary="Вход",
	 *   operationId="auth_signin",
	 *   description="Функционал в виде формы, позволяющий сотруднику выполнить авторизацию.",
	 *
	 *   @OA\Parameter(
	 *      name="login",
	 *      in="query",
	 *      required=true,
	 *      @OA\Schema(
	 *           type="string"
	 *      )
	 *   ),
	 *   @OA\Parameter(
	 *      name="pass",
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
	 *      description="Wrong login or password",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   )
	 *)
	 *
	 * @return null
	 * @throws Exception
	 */
	function action_signin() {
		Session::safe_session_start();
		try {
			$this->model->signin($_POST['login'], $_POST['pass']);
		}
		catch (LogicException $exception) {
			$_SESSION['message']['auth'] = 'Неверный пароль: вход не выполнен';
			header('Location: /auth');
		}
	}

	/**
	 * @OA\Post(
	 *   path="/auth/signout",
	 *   tags={"auth"},
	 *   summary="Выход",
	 *   operationId="auth_signout",
	 *   description="Функционал в виде ссылки в панели навигации, позволяющий сотруднику выйти из аккаунта.",
	 *
	 *   @OA\Response(
	 *      response=200,
	 *      description="Success",
	 *      @OA\MediaType(
	 *           mediaType="application/json",
	 *      )
	 *   )
	 *)
	 *
	 * @return null
	 * @throws Exception
	 */
	function action_signout() {
		$this->model->signout();
	}

}