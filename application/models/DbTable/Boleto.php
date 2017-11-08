<?php
class Application_Model_DbTable_Boleto extends Zend_Db_Table_Abstract
{
	protected $_name = 'webboleto_boleto';

	protected $_primary = array('codigo');

	public function getParams($codigo_boleto, $statics = false, $version = 1) {
		$db = $this->getAdapter();
		$select = $db
			->select()
			->from(array('b' => 'webboleto_boleto'),array())
			->where("b.codigo = '$codigo_boleto'");
		if ($statics) {
			$select->where('p.tipo_parametro = \'ESTATICO\'');
		}
		if ($statics && $version == 2) {
			$result = $this->_version2($select);
		} else {
			$result = $this->_version1($select);
		}
		return $result;
	}

	/**
	 * Método para a obtenção de valores dos campos para a versão 1 do sistema
	 */
	protected function _version1($select) {
		$db = $this->getAdapter();
		$select
			->joinLeft(array('bhp' => 'webboleto_boleto_has_parametro'),'b.codigo = bhp.codigo',array())
			->joinLeft(array('p' => 'webboleto_parametro'),'p.id_parametro = bhp.id_parametro',array('nome_parametro'));
		return $db->fetchCol($select);
	}

	/**
	 * Método para a obtenção de valores dos campos para a versão 2 do sistema
	 */
	protected function _version2($select) {
		$db = $this->getAdapter();
		$select
			->joinLeft(array('bhp' => 'webboleto_boleto_has_parametro'),'b.codigo = bhp.codigo',array('regex','error_message'))
			->joinLeft(array('p' => 'webboleto_parametro'),'p.id_parametro = bhp.id_parametro',array('nome_parametro','descricao_parametro'));
		return $db->fetchAll($select);
	}

	public function getBoletosDisponiveis() {
		$select =  $this->select()->from($this,'codigo');
		return $this->getAdapter()->fetchCol($select);
	}

}
