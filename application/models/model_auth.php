<?php

class Model_Auth extends Model {

	public function get_data($token, $client_uuid, $message)
	{
		$data = array(
			'code' => 200,
			'message' => $message
		);
		return $data;
	}

	/**
	 * @throws exception
	 */
	public function signin($login, $pass) {
		$data = array(
			'code' => 200
		);
		Session::get_sql_connection();
		$token = md5($login . ':' . md5($pass));
		$data['user'] = Session::auth($token);
		$_SESSION['token'] = $token;
		if ($data['user']['role'] == 'operator' || $data['user']['role'] == 'admin') {
			header('Location: /operator');
		}
		else if ($data['user']['role'] == 'accountant') {
			header('Location: /accountant');
		}
		return $data;
	}

	public function signout() {
		Session::safe_session_start();
		if (array_key_exists('token', $_SESSION)) {
			unset($_SESSION['token']);
		}
		header('Location: /auth');
	}

}