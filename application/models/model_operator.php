<?php

class Model_Operator extends Model {

	/**
	 * @throws exception
	 */
	public function get_data($token, $client_uuid, $message) {
		$data = array(
			'code' => 200,
			'user' => Session::auth($token),
			'message' => $message
		);
		return $data;
	}

	public function find_by_passport($passport) {
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->prepare('SELECT client_uuid FROM public.client WHERE passport = :passport');
		$stmt->execute(array('passport' => $passport));
		$client_uuid = $stmt->fetch()['client_uuid'];
		if (!$client_uuid) {
			throw new LogicException('Нет клиента с таким паспортом');
		}
		return $client_uuid;
	}

	/**
	 * @throws Exception
	 */
	public function create_client($client) {
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
			INSERT INTO public.client (
            	name, email, birthdate, passport, address, phone, 
    			passgiven, passcode, passdate, sex, birthplace, registration
    		) VALUES (
    			:name, :email, :birthdate, :passport, :address, :phone, 
    		    :passgiven, :passcode, :passdate, :sex, :birthplace, :registration
    		)
    		');
		$stmt->execute(array(
			'name' => $client['name'],
			'phone' => self::standart_phone($client['phone']),
			'passport' => $client['passport'],
			'passgiven' => htmlspecialchars($client['passgiven']),
			'passcode' => $client['passcode'],
			'passdate' => $_POST['passdate'],
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
}