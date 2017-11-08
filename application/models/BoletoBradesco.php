<?php
/**
 * Classe responsável pelo tratamento de dados para a geração de boletos do
 * Bradesco
 */
class Application_Model_BoletoBradesco extends Application_Model_Boleto
{
	/**
	 * Codigo do Banco
	 * @var string
	 */
	protected $_codigoBanco = '237';

	/**
	 * Construtor da classe
	 *
	 * @param array $params parametros necessários para a geração do boleto
	 */
	public function __construct($params) {
		// inicializa os atributos do modelo
		$this->_init($params,'BRADESCO');
	}

	/**
	 * Método para obtenção dos valores do boleto já formatados
	 *
	 * @return array Valores Formatados
	 */
	public function getParams() {
		// pega os números do código de barras
		$codigo_barras = $this->_getValorCodigoBarras();
		// gera o código de barras
		$barcode = $this->_getBarcode($codigo_barras);

		// pega a linha digitável
		$linha_digitavel = $this->_getLinhaDigitavel();

		// pega o nosso número
		$nosso_numero = $this->_getNossoNumero();

		//gera o dígito verificador do nosso número
		// if($this->boleto_com_registro) {
		// 	$digito_verificador = $this->_modulo11ComRegistro($nosso_numero);
		// }
		// else {
		// 	$digito_verificador = $this->_modulo10($nosso_numero);
		// }

		// objeto de data para formatação da data de vencimento
		$data = new Zend_Date($this->vencimento);

		// logo do bradesco
		$logo = file_get_contents(APPLICATION_PATH.'/../public/img/logo-bradesco.jpg');

		// monta o array com os valores a serem retornados
		$vars = array(
			'barcode'         	=> $barcode,
			'linha_digitavel' 	=> $linha_digitavel,
			'logo'            	=> base64_encode($logo),
			'codigo_banco'    	=> $this->_codigoBanco.'-'.$this->_modulo11($this->_codigoBanco,false),
			'agencia'        	=> $this->agencia.'-'.$this->_modulo11($this->agencia,false),
			'conta'           	=> $this->conta.'-'.$this->_modulo11($this->conta,false),
			'nosso_numero'    	=> $nosso_numero.'-'.$this->_modulo10($nosso_numero),
			'numero_documento' 	=> (int)$this->nosso_numero,
			'data_hoje'       	=> date('d/m/Y'),
			'vencimento'      	=> $data->toString('dd/MM/yy'),
			'valor'           	=> number_format(($this->valor/100),2,',','.'),
		);
		$vars += $this->_params;
		return $vars;
	}

	/**
	 * Monta a linha digitável do boleto conforme especificações do Bradesco
	 *
	 * @return string
	 */
	protected function _getLinhaDigitavel() {
		$campo_livre = $this->_getCampoLivre();

		$campo1 = $this->_codigoBanco.$this->_moeda.substr($campo_livre,0,5);
		$campo1 = $this->_formatCampo($campo1);

		$campo2 = substr($campo_livre,5,10);
		$campo2 = $this->_formatCampo($campo2);

		$campo3 = substr($campo_livre,15,10);
		$campo3 = $this->_formatCampo($campo3);

		$cod_barras = $this->_getValorCodigoBarras();
		$campo4 = substr($cod_barras,4,1);

		$campo5 = $this->_fatorVencimento($this->vencimento).str_pad($this->valor,10,'0',STR_PAD_LEFT);

		return "$campo1 $campo2 $campo3 $campo4 $campo5";
	}


	/**
	 * Método para obtenção da numeração código de barras, conforme documentação
	 * do bradesco
	 *
	 * @return string
	 */
	protected function _getValorCodigoBarras() {
		$parte1 = $this->_codigoBanco.$this->_moeda;
		$parte2 =
			$this->_fatorVencimento($this->vencimento).
			str_pad($this->valor,10,'0',STR_PAD_LEFT).
			$this->_getCampoLivre();
		$digito = $this->_modulo11($parte1.$parte2);
		return $parte1.$digito.$parte2;
	}

	/**
	 * Método que monta e retorna o Campo Livre conforme documentação do
	 * bradesco
	 *
	 * @return string
	 */
	protected function _getCampoLivre() {
		return
			$this->agencia.
			$this->carteira.
			$this->_getNossoNumero().
			str_pad($this->conta,7,'0',STR_PAD_LEFT).
			'0';
	}

	/**
	 * Método para formatação e retorno do nosso número, conforme documentação
	 * do bradesco
	 *
	 * @return string
	 */
	protected function _getNossoNumero() {
		return str_pad((int)$this->nosso_numero,11,'0',STR_PAD_LEFT);
	}
}
