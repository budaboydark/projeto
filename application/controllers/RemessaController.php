<?php
class RemessaController extends Zend_Controller_Action {

	public function indexAction() {
		$params = array();
		$errors = array();
		$vars = array();
		$boleto_nome = null;
		// se recebeu dados por POST
		if ($this->getRequest()->isPost()){
			$params = $this->getRequest()->getPost();
		} elseif ($this->getRequest()->isGet()) {
			$params = $this->getRequest()->getQuery();
		}
		if (!empty($params)) {
			// instancia a classe de Remessa para uso de funcões de validação de remessa.
			$modelRemessa = new Application_Model_Remessa();
			$parametros = $modelRemessa->validaDados($params);
			//$ip_cliente = $this->getRequest()->getServer('REMOTE_ADDR');
			$boleto_nome = $modelRemessa->normalizaNomeBanco($parametros['header']['boleto']);
			// instancia a classe de Remessa do boleto
	        $reflection = new Zend_Reflection_Class('Application_Model_Remessa'.$boleto_nome);
	        $boleto = $reflection->newInstance($parametros);
			$texto = $boleto->geraRemessa($parametros);
			echo $texto; //retorno de arquivo de remessa montado pelo WS
			die();
		} else {
			$errors = array('Parâmetros são obrigatórios');
			echo $errors;
			die();
		}
	}
}
