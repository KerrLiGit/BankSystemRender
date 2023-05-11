<?php

class Model_Graph extends Model {

	/**
	 * @throws Exception
	 */
	public function get_data($token, $client_uuid, $credit_id) {
		$data = array(
			'code' => 200,
			'user' => Session::auth($token),
			'credit_id' => $credit_id,
			'credit' => array(),
			'tail_sum' => array(),
			'graph' => array(),
			'total_graph' => array(),
			'current_date' => null,
		);
		$pdo = Session::get_sql_connection();
		$stmt = $pdo->query(
			"SELECT TO_CHAR(operation_date, 'dd.mm.yyyy') date FROM public.operdate WHERE current IS TRUE");
		$data['current_date'] = $stmt->fetch()['date'];
		$stmt = $pdo->prepare("
			SELECT c.credit_id, c.open_date, t.description, t.rate, t.ovd_rate, cur.isocode,
				c.current_account_number, c.main_account_number, c.main_perc_account_number, 
			    c.delay_account_number, c.delay_perc_account_number, 
				(SELECT sum FROM account.operation 
				WHERE credit_account_number = c.main_account_number ORDER BY oper_id LIMIT 1) credit_sum 
			FROM credit.list c 
				LEFT JOIN credit.term t ON t.type = c.type 
				LEFT JOIN currency cur ON cur.code = t.currency_code
			WHERE c.credit_id = :credit_id");
		$stmt->execute(array('credit_id' => $credit_id));
		$data['credit'] = $stmt->fetch();
		$stmt = $pdo->prepare('SELECT credit.tail_sum(:credit_id)');
		$stmt->execute(array('credit_id' => $credit_id));
		// Из БД получаем строку, фугкцией substr() убираем первый и последний символ строки (скобки)
		// Потом разбиваем строку на массив строк по разделителю "запятая"
		$row = explode(',', substr($stmt->fetch()['tail_sum'], 1, -1));
		$data['tail_sum']['up_date'] = $row[0];
		$data['tail_sum']['tail_days'] = $row[1];
		$data['tail_sum']['main_sum'] = $row[2];
		$data['tail_sum']['main_perc_sum'] = $row[3];
		$data['tail_sum']['delay_sum'] = $row[4];
		$data['tail_sum']['delay_perc_sum'] = $row[5];
		$data['tail_sum']['main_perc_sum_after_update'] = $row[6];
		$data['tail_sum']['delay_perc_sum_after_update'] = $row[7];
		$data['tail_sum']['current_sum'] = $row[8];
		$data['tail_sum']['total'] = $row[9];
		$stmt = $pdo->prepare("
			SELECT TO_CHAR(payment_date, 'dd.mm.yyyy') payment_date, sum_main, sum_perc, 
			    sum_main + sum_perc AS sum_total, processed 
			FROM credit.graph WHERE credit_id = :credit_id ORDER BY payment_date");
		$stmt->execute(array('credit_id' => $credit_id));
		$data['graph'] = $stmt->fetchAll();
		$data['total_graph']['sum_main'] = 0;
		$data['total_graph']['sum_perc'] = 0;
		$data['total_graph']['sum_total'] = 0;
		foreach ($data['graph'] as $row) {
			$data['total_graph']['sum_main'] += $row['sum_main'];
			$data['total_graph']['sum_perc'] += $row['sum_perc'];
			$data['total_graph']['sum_total'] += $row['sum_total'];
		}
		return $data;
	}
}