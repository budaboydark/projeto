<?php
class Application_Form_Remessa extends Zend_Form {
	public function init() {
		parent::init();
		$urldecode = new Zend_Filter_Callback('urldecode');

		// tipo de boleto "nome";
		$model_boleto = new Application_Model_DbTable_Boleto();
		$boletos = $model_boleto->getBoletosDisponiveis();
		$tipo_boleto = new Zend_Form_Element('boleto');
		$tipo_boleto
			->addValidator(new Zend_Validate_InArray($boletos))
			->setRequired();
		$this->addElement($tipo_boleto);
		//Lote Sequencial
		$lote = new Zend_Form_Element('lote');
		$lote
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>4,'max'=>4)))
			->setRequired(true);
		$this->addElement($lote);

		//codigo inscricao 1 para CPF e 2 para CNPJ
		$codigo_inscricao = new Zend_Form_Element('codigo_inscricao');
		$codigo_inscricao
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>1)))
			->setRequired(true);
		$this->addElement($codigo_inscricao);

		//numero inscricao recebe de acordo com o codigo; CPF 11 digitos ou CNPJ 14 digitos
		$numero_inscricao = new Zend_Form_Element('numero_inscricao');
		$numero_inscricao
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>11, 'max' => 15)))
			->setRequired(true);
		$this->addElement($numero_inscricao);

		//codigo do codigo_convenio
		$codigo_convenio = new Zend_Form_Element('codigo_convenio');
		$codigo_convenio
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>6,'max'=>9)))
			->setRequired(true);
		$this->addElement($codigo_convenio);

		//codigo da agencia da empresa conveniada
		$codigo_agencia = new Zend_Form_Element('codigo_agencia');
		$codigo_agencia
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>5)))
			->setRequired(true);
		$this->addElement($codigo_agencia);

		// Digito Verificador da agencia conveniada.
		$agencia_dv = new Zend_Form_Element('agencia_dv');
		$agencia_dv
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>1)))
			->setRequired(true);
		$this->addElement($agencia_dv);

		//conta corrente Sem DV
		$conta = new Zend_Form_Element('conta');
		$conta
		->addValidator(new Zend_Validate_Digits())
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>12)))
		->setRequired(true);
		$this->addElement($conta);

		//Digito Verificador da Conta corrente
		$conta_dv = new Zend_Form_Element('conta_dv');
		$conta_dv
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>1)))
			->setRequired(true);
		$this->addElement($conta_dv);

		//Digito Verificador da Conta corrente
		$conta_dac = new Zend_Form_Element('conta_dac');
		$conta_dac
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>1)))
			->setRequired(true);
		$this->addElement($conta_dac);

		// nome empresa
		$nome_empresa = new Zend_Form_Element('nome_empresa');
		$nome_empresa
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>30)))
			->setRequired(true);
		$this->addElement($nome_empresa);

		// razao social
		$razao_social = new Zend_Form_Element('razao_social');
		$razao_social
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>30)))
			->setRequired(true);
		$this->addElement($razao_social);

		// codigo da carteira do conveniado com o banco.
		$codigo_carteira = new Zend_Form_Element('codigo_carteira');
		$codigo_carteira
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>2,'max'=>2)))
			->setRequired(true);
		$this->addElement($codigo_carteira);

		//Variacao da carteira do conveniado com o banco
		$variacao_carteira = new Zend_Form_Element('variacao_carteira');
		$variacao_carteira
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>3,'max'=>3)))
			->setRequired(true);
		$this->addElement($variacao_carteira);

		//logradouro
		$logradouro = new Zend_Form_Element('logradouro');
		$logradouro
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>40)))
			->setRequired(true);
		$this->addElement($logradouro);

		//bairro
		$bairro = new Zend_Form_Element('bairro');
		$bairro
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>15)))
			->setRequired(true);
		$this->addElement($bairro);

		//cidade
		$cidade = new Zend_Form_Element('cidade');
		$cidade
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>15)))
		->setRequired(true);
		$this->addElement($cidade);

		//uf
		$uf = new Zend_Form_Element('uf');
		$uf
		->addValidator(new Zend_Validate_StringLength(array('min'=>2,'max'=>2)))
		->setRequired(true);
		$this->addElement($uf);

		//cep
		$cep = new Zend_Form_Element('cep');
		$cep
		->addValidator(new Zend_Validate_Digits())
		->addValidator(new Zend_Validate_StringLength(array('min'=>8,'max'=>8)))
		->setRequired(true);
		$this->addElement($cep);

		$numero_sequencial_arquivo = new Zend_Form_Element('numero_sequencial_arquivo');
		$numero_sequencial_arquivo
		->addValidator(new Zend_Validate_Digits())
		->addValidator(new Zend_Validate_StringLength(array('min'=>6,'max'=>6)))
		->setRequired(true);
		$this->addElement($numero_sequencial_arquivo);

	}
}
