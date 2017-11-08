<?php
class Application_Model_DbTable_Logs extends Zend_Db_Table_Abstract
{
	protected $_name = 'webboleto_logs';

	protected $_primary = 'id';

	public function generateLog($params, $client_ip, $boleto_type) {

		if (is_array($params)) {
			$aux = array();
			foreach ($params as $key=>$value){
				$aux[] = "$key=$value";
			}
			$params = implode('&', $aux);
		}
		$data = array (
			'boleto'        => $boleto_type,
			'date_consulta' => date('Y-m-d H:i:s'),
			'passed_params' => $params,
			'ip'            => $client_ip,
		);

		$row = $this->createRow($data)->save();
	}
}
