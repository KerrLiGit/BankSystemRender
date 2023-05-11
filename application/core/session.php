<?php

class Session {

	private static $pdo = null;

	public static function get_sql_connection() {
		$dsn = "pgsql:host=dpg-cheegj5269v75d7redr0-a.oregon-postgres.render.com;port=5432;dbname=bank_base";
		$opt = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		if (self::$pdo === null) {
			return new PDO($dsn, 'anna', 'g1P9Kzqd3Lb9ALAMKVKZPeIgG945VE0o', $opt);
		}
		/*$stmt = $pdo->query('SELECT * FROM emprole');
		while ($row = $stmt->fetch()) {
			Route::addlog($row['role'] . " " . $row['descript']);
		}*/
		return self::$pdo;
	}

	public static function request_uri() {
		return $_SERVER['REQUEST_URI'];
	}

	public static function safe_session_start() {
		if(!isset($_SESSION))
			session_start();
	}

	/**
	 * @throws Exception
	 */
	public static function auth($token) {
		$pdo = self::get_sql_connection();
		$query = "
			SELECT name, e.role,
       			(SELECT description FROM public.emprole r WHERE r.role = e.role) AS role_description 
			FROM public.employee e
			WHERE MD5(CONCAT(login, ':', password)) = :token LIMIT 1
			";
		$stmt = $pdo->prepare($query);
		$stmt->execute(array('token' => $token));
		$user = $stmt->fetchAll();
		if (count($user) == 0) {
			throw new LogicException(403);
		}
		return $user[0];
	}

	/**
	 * @throws Exception
	 */
	public static function check_client($client_uuid) {
		$pdo = self::get_sql_connection();
		$query = "SELECT COUNT(*) FROM public.client WHERE client_uuid = :client_uuid";
		$stmt = $pdo->prepare($query);
		$stmt->execute(array('client_uuid' => $client_uuid));
		$cnt = $stmt->fetchAll();
		if ($cnt == 0) {
			throw new LogicException(403);
		}
	}

}