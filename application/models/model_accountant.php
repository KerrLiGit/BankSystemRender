<?php

class Model_Accountant extends Model {

	/**
	 * @throws exception
	 */
	public function get_data($token, $client_uuid, $message) {
		$data = array(
			'code' => 200,
			'user' => Session::auth($token),
			'current_date' => null,
			'bank_account' => array(),
			'message' => $message
		);
		$pdo = Session::get_sql_connection();
		// Получение текущей даты
		$stmt = $pdo->query("SELECT TO_CHAR(operation_date, 'dd.mm.yyyy') current_date 
    		FROM public.operdate WHERE current IS TRUE");
		$data['current_date'] = $stmt->fetch()['current_date'];
		// Получение списка банковских счетов
		$stmt = $pdo->query("
			SELECT account_number, isocode, a.description, 
    			CASE WHEN t.type = 'active' THEN 'А' WHEN t.type = 'passive' THEN 'П' ELSE '?' END typemark,
				(SELECT account.calc_balance(account_number)) balance
			FROM account.list a
    			LEFT JOIN public.currency c ON c.code = a.currency_code
    			LEFT JOIN account.classifier t ON t.acc2p = a.acc2p
			WHERE closed IS NULL AND client_uuid = (SELECT client_uuid FROM public.client WHERE name = 'bank')");
		$data['bank_account'] = $stmt->fetchAll();
		// Получение списка валют для изменения курса
		$stmt = $pdo->query("SELECT code, isocode, name FROM public.currency WHERE code != '810'");
		$data['currency'] = $stmt->fetchAll();
		return $data;
	}

	public function change_operdate($date) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('CALL public.change_operdate(:date)');
		$stmt->execute(array('date' => $date));
	}

	/**
	 * @throws Exception
	 */
	public function transaction_acc($debit_account_number, $credit_account_number, $sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('SELECT currency_code FROM account.list WHERE account_number = :account_number');
		$stmt->execute(array('account_number' => $debit_account_number));
		$debit_currency_code = $stmt->fetch()['currency_code']; // Валюта исходного счета
		$stmt->execute(array('account_number' => $credit_account_number));
		$credit_currency_code = $stmt->fetch()['currency_code']; // Валюта конечного счета
		if ($debit_currency_code != $credit_currency_code) {
			// Разные валюты - перевод невозможен
			throw new LogicException('Выберите счета с одинаковой валютой');
		} else {
			// Одинаковые валюты - произведем перевод
			$stmt = $pdo->prepare(
				'CALL account.transaction(:credit_account_number, :debit_account_number, :sum)');
			$stmt->execute(array(
				'credit_account_number' => $credit_account_number,
				'debit_account_number' => $debit_account_number,
				'sum' => $sum
			));
		}
	}

	/**
	 * @throws Exception
	 */
	public function change_currency_cost($currency, $buy_sum, $cost_sum, $sell_sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare("
			INSERT INTO account.converter 
			    (currency_code, convert_date, buy, cost, sell) 
			VALUES 
			    (:currency, 
			    (SELECT (operation_date + NOW()::time) FROM public.operdate WHERE current IS TRUE), 
			    :buy, :cost, :sell)");
		$stmt->execute(array(
			'currency' => $currency,
			'buy' => $buy_sum,
			'cost' => $cost_sum,
			'sell' => $sell_sum
		));
	}
}