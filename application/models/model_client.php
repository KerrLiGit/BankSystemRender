<?php

class Model_Client extends Model {

	/**
	 * @throws exception
	 */
	public function get_data($token, $client_uuid, $message) {
		$data = array(
			'code' => 200,
			'user' => Session::auth($token),
			'account_list' => array(
				'currency' => array(),
				'close' => array(),
				'all' => array()
			),
			'deposit_list' => array(
				'term' => array(),
				'all' => array()
			),
			'credit_list' => array(
				'term' => array(),
				'all' => array()
			),
			'message' => $message
		);
		$pdo = Session::get_sql_connection();
		// Получение личных данных клиента
		$stmt = $pdo->prepare('SELECT * FROM public.client WHERE client_uuid = :client_uuid LIMIT 1');
		$stmt->execute(array('client_uuid' => $client_uuid));
		$data['client'] = $stmt->fetch();
		// Получение возможных валют для создания счета
		$stmt = $pdo->query('SELECT code, isocode, name FROM public.currency');
		$data['account_list']['currency'] = $stmt->fetchAll();
		// Получение списка пустых 40817 счетов клиента
		$stmt = $pdo->prepare("
		SELECT a.account_number, c.isocode
		FROM account.list a 
   			LEFT JOIN public.currency c ON c.code = a.currency_code
   			LEFT JOIN account.classifier t ON t.acc2p = SUBSTR(a.account_number, 1, 5) 
		WHERE (a.closed IS NULL) AND a.client_uuid = :client_uuid AND a.acc2p = '40817'
		AND account.calc_balance(a.account_number) = 0.00");
		$stmt->execute(array('client_uuid' => $data['client']['client_uuid']));
		while ($row = $stmt->fetch()) {
			$data['account_list']['close'][] = $row;
		}
		// Получение списка всех 40817 счетов клиента
		$stmt = $pdo->prepare("
		SELECT a.account_number, c.isocode,	account.calc_balance(a.account_number) balance
		FROM account.list a 
   			LEFT JOIN public.currency c ON c.code = a.currency_code
   			LEFT JOIN account.classifier t ON t.acc2p = SUBSTR(a.account_number, 1, 5) 
		WHERE (a.closed IS NULL) AND a.client_uuid = :client_uuid AND a.acc2p = '40817'");
		$stmt->execute(array('client_uuid' => $data['client']['client_uuid']));
		while ($row = $stmt->fetch()) {
			$data['account_list']['all'][] = $row;
		}
		// Получение списка всех типов вкладов
		$stmt = $pdo->query("SELECT type, description FROM deposit.term WHERE type != 'post_rest'");
		while ($row = $stmt->fetch()) {
			$data['deposit_list']['term'][] = $row;
		}
		// Получение списка всех вкладов
		$stmt = $pdo->prepare("
		SELECT d.deposit_id, ROUND(account.calc_balance(d.main_account_number), 2) balance, t.description, 
		    c.isocode, TO_CHAR((d.open_date + (t.month_cnt || ' month')::interval)::date, 'dd.mm.yyyy') end_date
		FROM deposit.list d
   			LEFT JOIN deposit.term t ON t.type = d.type
   			LEFT JOIN public.currency c ON c.code = t.currency_code
		WHERE d.client_uuid = :client_uuid AND d.close_date IS NULL");
		$stmt->execute(array('client_uuid' => $data['client']['client_uuid']));
		while ($row = $stmt->fetch()) {
			$data['deposit_list']['all'][] = $row;
		}
		// Получение списка всех типов кредитов
		$stmt = $pdo->query("
		SELECT description, month_cnt, type, rate FROM credit.term ORDER BY month_cnt, rate");
		while ($row = $stmt->fetch()) {
			$data['credit_list']['term'][] = $row;
		}
		// Получение списка всех кредитов
		$stmt = $pdo->prepare("
		SELECT c.credit_id, t.description, t.rate, cur.isocode, 
   			(SELECT ROUND(sum, 2) FROM account.operation 
   			WHERE credit_account_number = c.main_account_number ORDER BY oper_id LIMIT 1) balance
		FROM credit.list c
   			LEFT JOIN credit.term t ON t.type = c.type
   			LEFT JOIN public.currency cur ON cur.code = t.currency_code
		WHERE c.client_uuid = :client_uuid AND c.close_date IS NULL");
		$stmt->execute(array('client_uuid' => $data['client']['client_uuid']));
		while ($row = $stmt->fetch()) {
			$data['credit_list']['all'][] = $row;
		}
		return $data;
	}

	/**
	 * @throws Exception
	 */
	public function edit($client) {
		foreach ($client as $key => $value) {
			if ($value == '') unset($client[$key]);
		}
		if (!array_key_exists('name', $client) ||
			!array_key_exists('phone', $client) ||
			!array_key_exists('passport', $client)) {
			throw new LogicException('Обязательные поля не заполнены');
		}
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('
			UPDATE public.client SET 
           		name = :name, email = :email, birthdate = :birthdate, passport = :passport, address = :address, 
		    	phone = :phone, passgiven = :passgiven, passcode = :passcode, passdate = :passdate, sex = :sex,
		    	birthplace = :birthplace, registration = :registration
			WHERE client_uuid = :client_uuid
    	');
		$stmt->execute(array(
			'client_uuid' => htmlspecialchars($client['client_uuid']),
			'name' => $client['name'],
			'phone' => self::standart_phone($client['phone']),
			'passport' => $client['passport'],
			'passgiven' => htmlspecialchars($client['passgiven']),
			'passcode' => $client['passcode'],
			'passdate' => $client['passdate'],
			'sex' => $client['sex'],
			'birthdate' => $client['birthdate'] != '' ? $client['birthdate'] : null,
			'birthplace' => htmlspecialchars($client['birthplace']),
			'address' => htmlspecialchars($client['address']),
			'registration' => htmlspecialchars($client['registration']),
			'email' => $client['email']
		));
	}

	private function standart_phone($phone) {
		$newphone = "";
		$flag = 0;
		for ($i = 0; $i < strlen($phone); $i++) {
			$num = $phone[$i];
			if ($num == '+') {
				$flag = 1;
				$newphone = "8";
				continue;
			}
			if ($flag == 1) {
				$flag = 0;
				continue;
			}
			if ($num >= "0" && $num <= "9") {
				$newphone = $newphone . $num;
				continue;
			}
		}
		return $newphone;
	}

	/**
	 * @throws Exception
	 */
	public function create_account($client_uuid, $currency) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('SELECT account.open(:client_uuid, :currency_code)');
		$stmt->execute(array('client_uuid' => $client_uuid, 'currency_code' => $currency));
	}

	/**
	 * @throws Exception
	 */
	public function close_account($account_number) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('SELECT COUNT(*) FROM credit.list 
			WHERE current_account_number = :account_number AND close_date IS NULL');
		$stmt->execute(array('account_number' => $account_number));
		if ($stmt->fetch()['count'] == 1) {
			throw new LogicException('Невозможно закрыть счет действующего кредита');
		}
		$stmt = $pdo->prepare('CALL account.close(:account_number)');
		$stmt->execute(array('account_number' => $account_number));
	}

	/**
	 * @throws Exception
	 */
	public function push_account($credit_account_number, $sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare("
			SELECT account_number FROM account.list
			WHERE client_uuid = (SELECT client_uuid FROM public.client WHERE name = 'bank') 
				AND acc2p = '20202' AND closed IS NULL AND currency_code = 
    				(SELECT currency_code FROM account.list WHERE account_number = :credit_account_number)
    	");
		$stmt->execute(array('credit_account_number' => $credit_account_number));
		$cashbox_account_number = $stmt->fetch()['account_number']; // Счет кассы
		$stmt = $pdo->prepare('CALL account.transaction(:credit_account_number, :cashbox_account_number, :sum)');
		$stmt->execute(array(
			'credit_account_number' => $credit_account_number,
			'cashbox_account_number' => $cashbox_account_number,
			'sum' => $sum
		));
	}

	/**
	 * @throws Exception
	 */
	public function pop_account($debit_account_number, $sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare("
			SELECT account_number FROM account.list
			WHERE client_uuid = (SELECT client_uuid FROM public.client WHERE name = 'bank') 
				AND acc2p = '20202' AND closed IS NULL AND currency_code = 
    				(SELECT currency_code FROM account.list WHERE account_number = :debit_account_number)
    	");
		$stmt->execute(array('debit_account_number' => $debit_account_number));
		$cashbox_account_number = $stmt->fetch()['account_number']; // Счет кассы
		$stmt = $pdo->prepare('CALL account.transaction(:cashbox_account_number, :debit_account_number, :sum)');
		$stmt->execute(array(
			'debit_account_number' => $debit_account_number,
			'cashbox_account_number' => $cashbox_account_number,
			'sum' => $sum
		));
	}

	/**
	 * @throws Exception
	 */
	public function transaction_in($debit_account_number, $credit_account_number, $sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare("SELECT currency_code FROM account.list WHERE account_number = :account_number");
		$stmt->execute(array('account_number' => $debit_account_number));
		$debit_currency_code = $stmt->fetch()['currency_code']; // Валюта исходного счета
		$stmt->execute(array('account_number' => $credit_account_number));
		$credit_currency_code = $stmt->fetch()['currency_code']; // Валюта конечного счета
		if ($debit_currency_code != $credit_currency_code) {
			// Разные валюты - произведем конвертацию
			$stmt = $pdo->prepare('CALL account.converting(:debit_account_number, :credit_account_number, :sum)');
			$stmt->execute(array(
				'credit_account_number' => $credit_account_number,
				'debit_account_number' => $debit_account_number,
				'sum' => $sum
			));
		}
		else {
			// Одинаковые валюты - произведем обычный перевод
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
	public function transaction_out($client_uuid, $debit_account_number, $credit_phone, $sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('SELECT client_uuid FROM public.client WHERE phone = :credit_phone');
		$stmt->execute(array('credit_phone' => self::standart_phone($credit_phone)));
		$res = $stmt->fetch();
		if (!$res) {
			throw new LogicException('Клиент с таким номером телефона не найден');
		}
		$credit_client_uuid = $res['client_uuid']; // Клиент, которому выполняется перевод
		if ($client_uuid == $credit_client_uuid) {
			// UUID текущего клиента и клиента, которому выполняется перевод, совпадают
			throw new LogicException('Перевод себе недоступен по номеру телефона');
		}
		$stmt = $pdo->prepare("
			SELECT account_number FROM account.list 
			WHERE client_uuid = :client_uuid AND currency_code = (
				SELECT currency_code FROM account.list 
				WHERE account_number = :debit_account_number AND closed IS NULL 
			) AND closed IS NULL AND basic IS TRUE
		");
		$stmt->execute(array(
			'debit_account_number' => $debit_account_number,
			'client_uuid' => $credit_client_uuid
		));
		$res = $stmt->fetch();
		if (!$res) {
			// Счет в той же валюте не найден. Попробуем найти счет в другой валюте
			$stmt = $pdo->prepare(
				'SELECT account_number FROM account.list WHERE client_uuid = :client_uuid AND basic IS TRUE');
			$stmt->execute(array('client_uuid' => $credit_client_uuid));
			$res = $stmt->fetch();
			if (!$res) {
				throw new LogicException('У клиента нет подходящего счета для принятия перевода');
			}
			$credit_account_number = $res['account_number'];
			$stmt = $pdo->prepare(
				'CALL account.converting(:debit_account_number, :credit_account_number, :sum)');
			$stmt->execute(array(
				'debit_account_number' => $debit_account_number,
				'credit_account_number' => $credit_account_number,
				'sum' => $sum
			));
		}
		else {
			// Найдн счет в нужной валюте. Выполним перевод
			$credit_account_number = $res['account_number'];
			$stmt = $pdo->prepare(
				'CALL account.transaction(:credit_account_number, :debit_account_number, :sum)');
			$stmt->execute(array(
				'debit_account_number' => $debit_account_number,
				'credit_account_number' => $credit_account_number,
				'sum' => $sum
			));
		}
	}

	/**
	 * @throws Exception
	 */
	public function open_deposit($client_uuid, $type, $debit_account_number, $sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('SELECT currency_code FROM deposit.term WHERE type = :type');
		$stmt->execute(array('type' => $type));
		$deposit_currency_code = $stmt->fetch()['currency_code'];
		$query = 'SELECT currency_code FROM account.list WHERE account_number = :account_number';
		$stmt = $pdo->prepare($query);
		$stmt->execute(array('account_number' => $debit_account_number));
		$account_currency_code = $stmt->fetch()['currency_code'];
		if ($deposit_currency_code != $account_currency_code) {
			throw new LogicException('Валюта выбранного вклада и счета не совпадают');
		}
		$stmt = $pdo->prepare('CALL deposit.open(:type, :client_uuid, :account_number, :sum)');
		$stmt->execute(array(
			'type' => $type,
			'client_uuid' => $client_uuid,
			'account_number' => $debit_account_number,
			'sum' => $sum
		));
	}

	/**
	 * @throws Exception
	 */
	public function close_deposit($deposit_id, $account_number) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('CALL deposit.close(:deposit_id, :account_number)');
		$stmt->execute(array('deposit_id' => $deposit_id, 'account_number' => $account_number));
	}

	/**
	 * @throws Exception
	 */
	public function open_credit($client_uuid, $type, $sum) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('SELECT credit.open(:type, :client_uuid, :sum)');
		$stmt->execute(array(
			'type' => $type,
			'client_uuid' => $client_uuid,
			'sum' => $sum
		));
	}

	/**
	 * @throws Exception
	 */
	public function close_credit($credit_id) {
		$pdo = Session::get_sql_connection();
		$pdo->beginTransaction();
		try {
			$stmt = $pdo->prepare('CALL credit.repayment(:credit_id)');
			$stmt->execute(array('credit_id' => $credit_id));
			$stmt = $pdo->prepare('CALL credit.close(:credit_id)');
			$stmt->execute(array('credit_id' => $credit_id));
			$pdo->commit();
		}
		catch (Exception $exception) {
			$pdo->rollBack();
			throw new LogicException('Ошибка выполнения запроса');
		}
	}
}