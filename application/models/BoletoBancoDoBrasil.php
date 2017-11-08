<?php
class Application_Model_BoletoBancoDoBrasil extends Application_Model_Boleto
{

	/**
	 * Código do banco
	 * @var string
	 */
	protected $_codigoBanco = '001';

    protected $_nossoNumeroComDV = false;

	/**
	 * Construtor da classe.
	 * Este método passa os parâmetros recebidos para o método _init() da classe
	 * pai.
	 *
	 * @param array $params Parâmetros necessários para geração do boleto
	 */
	public function __construct($params) {
		$this->_init($params,'BANCO_DO_BRASIL');
	}


	/**
	 * Método responsáel pela montagem dos parâmetros do boleto
	 *
	 * @return array Valores necessários para geração do boleto
	 */
	public function getParams() {
		// pega a numeração do código de barras
		$numero_codigo_barras = $this->_getValorCodigoBarras();

		// pega a linha digitável
		$linha_digitavel = $this->_getLinhaDigitavel($numero_codigo_barras);

		// pega o código de barras
		$barcode = $this->_getBarcode($numero_codigo_barras);

		// novo objeto de data com a data de vencimento
		$data = new Zend_Date($this->vencimento);

		// pega o logo do banco
		$logo = file_get_contents(APPLICATION_PATH.'/../public/img/logo-bb.jpg');

		if (isset($this->agencia_digito)) {
			$agencia_digito = $this->agencia_digito[0];
		} else {
			$agencia_digito = $this->_modulo11($this->agencia,false,true);
		}

        $nosso_numero = $this->_getNossoNumero();
        if ($this->_nossoNumeroComDV) {
            $nosso_numero .= '-'.$this->_modulo11($nosso_numero,false,true);
        }

		// variáveis para a camada de visualização
		$vars = array (
			'barcode'          => $barcode,
			'codigo_banco'     => $this->_codigoBanco.'-'.$this->_modulo11($this->_codigoBanco,false),
			'codigo_cedente'   => $this->codigo_cedente.'-'.$this->_modulo11($this->codigo_cedente,false),
			'conta'            => $this->conta.'-'.$this->_modulo11($this->conta,false),
			'data_hoje'        => date('d/m/Y'),
			'especie'          => 'R$',
			'linha_digitavel'  => $linha_digitavel,
			'logo'             => base64_encode($logo),
			'valor'            => number_format(($this->valor/100),2,',','.'),
			'vencimento'       => $data->toString('dd/MM/y'),
			'nosso_numero'     => $nosso_numero,
			'numero_documento' => (int)$this->nosso_numero,
			'agencia'          => $this->agencia.'-'.$agencia_digito,
		);
		$vars += $this->_params;
		return $vars;
	}

	/**
	 * Método para geração da linha digitável
	 *
	 * @param string $codigo_barras Numeração do código de barras
	 * @return string Numeração da linha digitável
	 */
	protected function _getLinhaDigitavel($codigo_barras) {

		// as definições abaixo realizadas são executas conforme o manual de
		// desenvolvimento

		$campo1 = $this->_codigoBanco.$this->_moeda.substr($codigo_barras,19,5);
		$campo1 .= $this->_modulo10($campo1);
		$campo1 = substr($campo1,0,5).'.'.substr($campo1,5,6);

		$campo2 = substr($codigo_barras,24,10);
		$campo2 .= $this->_modulo10($campo2);
		$campo2 = substr($campo2,0,5).'.'.substr($campo2,5,6);

		$campo3 = substr($codigo_barras,34,10);
		$campo3 .= $this->_modulo10($campo3);
		$campo3 = substr($campo3,0,5).'.'.substr($campo3,5,6);

		$campo4 = substr($codigo_barras,4,1);

		$campo5 = substr($codigo_barras,5,14);
		return "$campo1 $campo2 $campo3 $campo4 $campo5";
	}

	/**
	 * Montagem do Código de Barras
	 *
	 *@return string Numeração do Código de barras
	 */
	protected function _getValorCodigoBarras() {
		// montagem do dados antes do digito verificador
		$parte1 = $this->_codigoBanco.$this->_moeda;

		// montagem dos dados depois do digito verificador
		$fator_vencimento = $this->_fatorVencimento($this->vencimento);
		$codigo_cedente = $this->codigo_cedente;
		$nosso_numero = $this->_getNossoNumero();
		if (strlen($nosso_numero) == 11) {
			$nosso_numero = $nosso_numero.$this->agencia.str_pad((int)$this->conta,8,0,STR_PAD_LEFT);
		} else {
			$nosso_numero = '000000'.$nosso_numero;
		}
		$valor = str_pad($this->valor,10,0,STR_PAD_LEFT);
		$parte2 = $fator_vencimento.$valor.$nosso_numero.substr($this->carteira, -2);

		// calculo do digito verificador
		$digito_verificador = $this->_modulo11($parte1.$parte2);

		//retorno do codigo completo
		return $parte1.$digito_verificador.$parte2;
	}

	/**
	 * Método para montagem do nosso número
	 *
	 * @return string Nosso Número
	 */
	protected function _getNossoNumero() {
		$convenio_length = strlen($this->codigo_cedente);
		switch ($convenio_length) {
			case 4:
			case 6:
				$padding = 11 - $convenio_length;
                $this->_nossoNumeroComDV = true;
				break;
			case 7:
			case 8:
				$padding = 17 - $convenio_length;
		}
		$nosso_numero = $this->codigo_cedente . str_pad((int)$this->nosso_numero,$padding,0,STR_PAD_LEFT);
		return $nosso_numero;
	}
}
