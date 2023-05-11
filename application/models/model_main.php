<?php

class Model_Main extends Model {

	public function get_data($token, $client_uuid, $message)
	{
		$data = array(
			'code' => 200,
			'message' => $message
		);
		return $data;
	}

}