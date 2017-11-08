<?php
class Application_Form_Boleto extends Zend_Form {
	public function init() {
		parent::init();

		$urldecode = new Zend_Filter_Callback('urldecode');

		// codigo do cedente
		$codigo_cedente = new Zend_Form_Element('codigo_cedente');
		$codigo_cedente
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>1)))
			->setRequired(true)
			->addFilter(new Zend_Filter_Padding(6, 0));
		$this->addElement($codigo_cedente);

		// nome do cedente
		$nome_cedente = new Zend_Form_Element('nome_cedente');
		$nome_cedente
			->setRequired(true)
			->setAllowEmpty(false)
			->addFilter($urldecode);
		$this->addElement($nome_cedente);

		// cnpj cedente
		$cnpj = new Zend_Form_Element('cnpj_cedente');
		$cnpj
			->setAllowEmpty(false)
			->setRequired(true)
			->addFilter($urldecode);
		$this->addElement($cnpj);


		// nosso número
		$nosso_numero = new Zend_Form_Element('nosso_numero');
		$nosso_numero
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('max' => 15, 'min' => 1)))
			->setRequired(true)
			->addFilter(new Zend_Filter_Padding(15, 0));
		$this->addElement($nosso_numero);

		// vencimento
		$vencimento = new Zend_Form_Element('vencimento');
		$vencimento
			->addValidator(new Zend_Validate_Date('yyyy-mm-dd'))
			->setRequired(true);
		$this->addElement($vencimento);

		// valor
		$valor = new Zend_Form_Element('valor');
		$valor
			->addValidator(new Zend_Validate_Digits())
			->addFilter(new Zend_Filter_Padding(10, 0))
			->setRequired(true);
		$this->addElement($valor);

		// agencia
		$agencia = new Zend_Form_Element('agencia');
		$agencia
			->addValidator(new Zend_Validate_Digits())
			->setRequired(true);
		$this->addElement($agencia);

		// dv_conta
		// $dv_conta = new Zend_Form_Element('dv_conta');
		// $dv_conta
		// 	->addValidator(new Zend_Validate_Digits())
		// 	->setRequired(false);
		// $this->addElement($dv_conta);

		// agencia
		$agencia_digito = new Zend_Form_Element('agencia_digito');
		$agencia_digito
			->setRequired(false);
		$this->addElement($agencia_digito);

		// codigo_cliente
		// $codigo_cliente = new Zend_Form_Element('codigo_cliente');
		// $codigo_cliente
		// 	->setRequired(false);
		// $this->addElement($codigo_cliente);

		// endereço do cliente
		$cliente_endereco = new Zend_Form_Element('cliente_endereco');
		$cliente_endereco
			->setAllowEmpty(false)
			->setRequired(true)
			->addFilter($urldecode);
		$this->addElement($cliente_endereco);

		// nome do cliente
		$cliente_nome = new Zend_Form_Element('cliente_nome');
		$cliente_nome
			->setAllowEmpty(false)
			->setRequired(true)
			->addFilter($urldecode);
		$this->addElement($cliente_nome);

		// estado do cliente
		$cliente_uf = new Zend_Form_Element('cliente_uf');
		$cliente_uf
			->setAllowEmpty(false)
			->setRequired(true);
		$this->addElement($cliente_uf);

		// cpf do cliente
		$cliente_cpf = new Zend_Form_Element('cliente_cpf');
		$cliente_cpf
			->setAllowEmpty(false)
			->setRequired(true);
		$this->addElement($cliente_cpf);

		// Cidade do cliente
		$cliente_cidade = new Zend_Form_Element('cliente_cidade');
		$cliente_cidade
			->setAllowEmpty(false)
			->setRequired(true)
			->addFilter($urldecode);
		$this->addElement($cliente_cidade);

		$conta = new Zend_Form_Element('conta');
		$conta
			->setAllowEmpty(true)
			->setRequired(false);
		$this->addElement($conta);

		$dv_conta = new Zend_Form_Element('dv_conta');
		$dv_conta
			->setAllowEmpty(true)
			->setRequired(false);
		$this->addElement($dv_conta);

		$conta_cedente = new Zend_Form_Element('conta_cedente');
		$conta_cedente
			->setAllowEmpty(true)
			->setRequired(false);
		$this->addElement($conta_cedente);

		$carteira = new Zend_Form_Element('carteira');
		$carteira
			->setAllowEmpty(true)
			->setRequired(false);
		$this->addElement($carteira);

		$variacao_carteira = new Zend_Form_Element('variacao_carteira');
		$variacao_carteira
			->setAllowEmpty(true)
			->setRequired(false);
		$this->addElement($variacao_carteira);

		// cep do cliente
		$cliente_cep = new Zend_Form_Element('cliente_cep');
		$cliente_cep
			->setAllowEmpty(false)
			->addValidator(new Zend_Validate_PostCode())
			->setRequired(true);
		$this->addElement($cliente_cep);

		// tipo de boleto
		$model_boleto = new Application_Model_DbTable_Boleto();
		$boletos = $model_boleto->getBoletosDisponiveis();
		$tipo_boleto = new Zend_Form_Element('boleto');
		$tipo_boleto
			->addValidator(new Zend_Validate_InArray($boletos))
			->setRequired();
		$this->addElement($tipo_boleto);

		$endereco_cedente = new Zend_Form_Element('endereco_cedente');
		$this->addElement($endereco_cedente);

		$instrucao_boleto = new Zend_Form_Element('instrucao_boleto');
		$instrucao_boleto
			->setRequired(false);
		$this->addElement($instrucao_boleto);

		// boleto com registro
		// $boleto_com_registro = new Zend_Form_Element('boleto_com_registro');
		// $boleto_com_registro
		// 	->addValidator(new Zend_Validate_Digits())
		// 	->addValidator(new Zend_Validate_StringLength(array('max' => 1, 'min' => 1)))
		// 	->setRequired(true);
		// $this->addElement($boleto_com_registro);
	}
}
