<?php
class Application_Form_Detalhe extends Zend_Form {
	public function init() {
		parent::init();
		$urldecode = new Zend_Filter_Callback('urldecode');

		$codigo_ocorrencia = new Zend_Form_Element('codigo_ocorrencia');
		$codigo_ocorrencia
		->addValidator(new Zend_Validate_Digits())
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>1)))
		->setRequired(true);
		$this->addElement($codigo_ocorrencia);

		// nosso numero
		$nosso_numero = new Zend_Form_Element('nosso_numero');
		$nosso_numero
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('max' => 20, 'min' => 1)))
			->setRequired(true);
		$this->addElement($nosso_numero);

		//numero_documento
		$numero_documento = new Zend_Form_Element('numero_documento');
		$numero_documento
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>25)))
		->setRequired(true);
		$this->addElement($numero_documento);

		//Valor do Documento
		$valor = new Zend_Form_Element('valor');
		$valor
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>15)))
			->setRequired(true);
		$this->addElement($valor);

		//Sacado_tipo 1 para CPF e 2 para CNPJ
		$sacado_tipo = new Zend_Form_Element('sacado_tipo');
		$sacado_tipo
		->addValidator(new Zend_Validate_Digits())
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>1)))
		->setRequired(true);
		$this->addElement($sacado_tipo);

		// sacado cpfcnpj
		$sacado_cpfcnpj = new Zend_Form_Element('sacado_cpfcnpj');
		$sacado_cpfcnpj
		->addValidator(new Zend_Validate_Digits())
		->addValidator(new Zend_Validate_StringLength(array('min'=>11,'max'=>14)))
		->setRequired(true);
		$this->addElement($sacado_cpfcnpj);

		//sacado_nome: nome do pagador
		$sacado_nome = new Zend_Form_Element('sacado_nome');
		$sacado_nome
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>40)))
		->setRequired(true);
		$this->addElement($sacado_nome);

		//sacado_logradouro endereço do pagador
		$sacado_logradouro = new Zend_Form_Element('sacado_logradouro');
		$sacado_logradouro
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>40)))
		->setRequired(true);
		$this->addElement($sacado_logradouro);

		//sacado_bairro: bairro do pagador
		$sacado_bairro = new Zend_Form_Element('sacado_bairro');
		$sacado_bairro
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>15)))
		->setRequired(true);
		$this->addElement($sacado_bairro);

		//sacado_cep: cep do pagador.
		$sacado_cep = new Zend_Form_Element('sacado_cep');
		$sacado_cep
		->addValidator(new Zend_Validate_Digits())
		->addValidator(new Zend_Validate_StringLength(array('min'=>8,'max'=>8)))
		->setRequired(true);
		$this->addElement($sacado_cep);

		//sacado cidade
		$sacado_cidade = new Zend_Form_Element('sacado_cidade');
		$sacado_cidade
		->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>15)))
		->setRequired(true);
		$this->addElement($sacado_cidade);

		//sacado uf
		$sacado_uf = new Zend_Form_Element('sacado_uf');
		$sacado_uf
		->addValidator(new Zend_Validate_StringLength(array('min'=>2,'max'=>2)))
		->setRequired(true);
		$this->addElement($sacado_uf);

		// vencimento
		$data_vencimento = new Zend_Form_Element('data_vencimento');
		$data_vencimento
			->addValidator(new Zend_Validate_Date('yyyy-mm-dd'))
			->addValidator(new Zend_Validate_StringLength(array('min'=>10,'max'=>10)))
			->setRequired(true);
		$this->addElement($data_vencimento);

		// Data cadastro
		$data_cadastro = new Zend_Form_Element('data_cadastro');
		$data_cadastro
			->addValidator(new Zend_Validate_Date('yyyy-mm-dd'))
			->addValidator(new Zend_Validate_StringLength(array('min'=>10,'max'=>10)))
			->setRequired(true);
		$this->addElement($data_cadastro);

		//juros de mora de um dia/taxa
		$juros_de_um_dia = new Zend_Form_Element('juros_de_um_dia');
		$juros_de_um_dia
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>1,'max'=>15)))
			->setRequired(true);
		$this->addElement($juros_de_um_dia);

		//data data_desconto
		$data_desconto = new Zend_Form_Element('data_desconto');
		$data_desconto
			->addValidator(new Zend_Validate_Digits())
			->addValidator(new Zend_Validate_StringLength(array('min'=>8,'max'=>8)))
			->setRequired(true);
		$this->addElement($data_desconto);

		//valor de desconto caso houver
		$valor_desconto = new Zend_Form_Element('valor_desconto');
		$valor_desconto
			->addValidator(new Zend_Validate_StringLength(array()))
			->setRequired(true);
		$this->addElement($valor_desconto);

		//prazo de dias para o cliente pagar após o vencimento
		$prazo = new Zend_Form_Element('prazo');
		$prazo
			->addValidator(new Zend_Validate_Digits())
			->setRequired(true);
		$this->addElement($prazo);
	}
}
