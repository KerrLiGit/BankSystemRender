<?php

class Controller_Accountant extends Controller {

	function __construct() {
		$this->model = new Model_Accountant();
		$this->view = new View();
	}

	/**
	 * @throws Exception
	 */
	function action_index() {
		Session::safe_session_start();
		$user = Session::auth($_SESSION['token']);
		if ($user['role'] == 'admin' || $user['role'] == 'accountant') {
			$data = $this->model->get_data($_SESSION['token'], null, $_SESSION['message']);
			unset($_SESSION['message']);
			$this->view->generate('view_accountant.php', 'view_header.php', $data);
		}
		else {
			throw new LogicException(403);
		}
	}

	function action_change_operdate() {
		Session::safe_session_start();
		$user = Session::auth($_SESSION['token']);
		if ($user['role'] == 'admin' || $user['role'] == 'accountant') {
			try {
				$this->model->change_operdate($_POST['date']);
				$_SESSION['message']['change_operdate'] = 'Установлена новая дата';
			}
			catch (Exception $exception) {
				$_SESSION['message']['change_operdate'] = $exception->getMessage();
			}
			header('Location: /accountant#change_operdate');
		}
		else {
			throw new LogicException(403);
		}
	}

	function action_transaction_acc() {
		Session::safe_session_start();
		$user = Session::auth($_SESSION['token']);
		if ($user['role'] == 'admin' || $user['role'] == 'accountant') {
			try {
				$this->model->transaction_acc($_POST['debit_account_number'],
					$_POST['credit_account_number'], $_POST['sum']);
				$_SESSION['message']['transaction_acc'] = 'Успешный перевод';
			} catch (Exception $exception) {
				$_SESSION['message']['transaction_acc'] = 'Ошибка при переводе: ' . $exception->getMessage();
			}
			header('Location: /accountant#transaction_acc');
		}
		else {
			throw new LogicException(403);
		}
	}

	function action_change_currency_cost() {
		Session::safe_session_start();
		$user = Session::auth($_SESSION['token']);
		if ($user['role'] == 'admin' || $user['role'] == 'accountant') {
			try {
				$this->model->change_currency_cost($_POST['currency'],
					$_POST['buy_sum'], $_POST['cost_sum'], $_POST['sell_sum']);
				$_SESSION['message']['change_currency_cost'] = 'Данные о стоимоти валюты успешно обновлены';
			} catch (Exception $exception) {
				$_SESSION['message']['change_currency_cost'] = 'Ошибка при выполнении операции: ' . $exception->getMessage();
			}
			header('Location: /accountant#change_currency_cost');
		}
		else {
			throw new LogicException(403);
		}
	}

}