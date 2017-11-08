<?php
class Application_Model_BoletoCaixaCef extends Application_Model_Boleto
{
	protected $_digitoGeral;
	protected $_codigoBanco = 104;

	public function __construct($params) {
		$this->_init($params,'CAIXA_SIGCB');
	}

	/**
	 * Formata e retorna os valores necess�rios para o boleto
	 *
	 * @return array Valores Formatados
	 */
	public function getParams(){
		// coloca os par�metros da carteira no in�cio do nosso n�mero
		$variacaoCarteira = ((isset($this->variacao_carteira)) ? $this->variacao_carteira : null);

		$this->nosso_numero = ((!empty($variacaoCarteira)) ? $variacaoCarteira : "24") . $this->_formatNumbers((int)$this->nosso_numero, "0", 8);
		// calcula o fator de vencimento para o boleto
		$fator_vencimento = $this->_fatorVencimento($this->vencimento);

		// pega os c�digos para o c�digo de barras e a linha digit�vel
		$codes = $this->_getCodes($this->_params);

		$barcode = $this->_getBarcode($codes['barcode']);

		// pega o logo da caixa
		$logo_caixa = file_get_contents(APPLICATION_PATH.'/../public/img/logocaixa.jpg');

		// novo objeto de data com a data de vencimento
		$data = new Zend_Date($this->vencimento);

		// d�gito verificador da conta
		$dvConta = isset($this->dv_conta) ? $this->dv_conta : null;

		// C�digo do cedente
		//$codigoCedente = isset($this->codigo_cedente) ? $this->codigo_cedente : $this->_modulo11($this->codigo_cedente);

		// Conta do cedente
		$contaCedente = ((isset($this->conta_cedente)) ? $this->conta_cedente : null);
		$contaCedente = ((!empty($contaCedente)) ? $contaCedente : $this->conta);

		// vari�veis para a camada de visualiza��o
		$vars = array (
			'barcode'         => $barcode,
			'carteira'        => (isset($this->carteira)) ? $this->carteira : 'SR',
			'codigo_banco'    => $this->_codigoBanco.'-'.$this->_modulo11($this->_codigoBanco,false),
			'codigo_cedente'  => $contaCedente.'-'.((int)$this->codigo_cedente),
			'conta'           => $this->_modulo11($this->conta,false),
			'data_hoje'       => "13/10/2014",#date('d/m/Y'),
			'especie'         => 'R$',
			'linha_digitavel' => $codes['linha_digitavel'],
			'logo_caixa'      => base64_encode($logo_caixa),
			'valor'           => number_format(($this->valor/100),2,',','.'),
			'vencimento'      => $data->toString('dd/MM/y'),
		);
		$vars += $this->_params;
		// adiciona o digito verificador para o campo nosso n�mero
		$vars['nosso_numero'] .= '-'.$this->_modulo11($vars['nosso_numero'],false);
		return $vars;
	}



	/**
	 * Monta e retorna o Campo livre
	 *
	 * @param string $codigo_cedente C�digo num�rico do cedente
	 * @param string $nosso_numero N�mero de identifica��o do Boleto para a loja
	 * @param int $tipo_registro 1 para Registrado ou 2 para Sem Registro
	 * @return string Campo livre com 25 d�gitos
	 */
	protected function _getCampoLivre($codigo_cedente, $nosso_numero, $tipo_registro = 2) {

		$campo_livre  = $codigo_cedente;
		// digito verificador do c�digo de cedente
		$campo_livre .= $this->_modulo11($codigo_cedente,false);
		// posi��es 3,4 e 5 do nosso n�mero
		$campo_livre .= substr($nosso_numero, 2,3);
		// tipo de registro
		$campo_livre .= $tipo_registro;
		//posi��es 6,7 e 8 do nosso n�mero
		$campo_livre .= substr($nosso_numero, 5,3);
		// constante para identifica��o de responsabilidade de impress��o do Cedente
		$campo_livre .= 4;
		// da posi��o 9 � posi��o 17 do nosso n�mero
		$campo_livre .= substr($nosso_numero, 8,9);
		// d�gito verificador do nosso n�mero
		$campo_livre .= $this->_modulo11($campo_livre,false);
		return $campo_livre;
	}

/**
	 * Gera o c�digo num�rico formatado para a linha digit�vel de acordo com a
	 * documenta��o do Caixa
	 *
	 * @param array $params com os dados do boleto
	 * @return string C�digo Gerado
	 */
	protected function _getLinhaDigitavel($params) {

		$codigo_banco = $this->_codigoBanco;
		$moeda = $this->_moeda;

		//$campo_livre = $this->_getCampoLivre($params['codigo_cedente'], $params['nosso_numero']);

		$campo1 = $codigo_banco.$moeda.substr($params['nosso_numero'], 0, 5);
		$digito_campo1 = $this->_modulo10($campo1);
		$campo1 = substr($campo1, 0, 5).'.'.substr($campo1, 5, 5).$digito_campo1;

		$campo2 = substr($params['nosso_numero'].$params['agencia'].$params['conta_cedente'], 5, 10);
		$digito_campo2 = $this->_modulo10($campo2);
		$campo2 = substr($campo2, 0, 5).'.'.substr($campo2, 5, 5).$digito_campo2;

		$campo3 = substr($params['conta_cedente'], 1,10);
		$digito_campo3 = $this->_modulo10($campo3);
		$campo3 = substr($campo3, 0, 5).'.'.substr($campo3, 5, 5).$digito_campo3;

		$campo4 = $this->_digitoGeral;

		$campo5 = $this->_fatorVencimento($params['vencimento']).$params['valor'];

		return "$campo1 $campo2 $campo3 $campo4 $campo5";
	}

	/**
	 * Gera o c�digo num�rico formatado para o c�digo de barras de acordo com a
	 * documenta��o do Caixa
	 *
	 * @param array $params com os dados do boleto
	 * @return string C�digo Gerado
	 */
	protected function _getValorCodigoBarras($params) {
		$codigo_banco = $this->_codigoBanco;
		$moeda = $this->_moeda;
		$fator_vencimento = $this->_fatorVencimento($params['vencimento']);

		$parte1 = $codigo_banco.$moeda;
		$parte2 = $fator_vencimento.$params['valor'].$params['nosso_numero'].$params['agencia'].$params['conta_cedente'];
		$digito_geral = $this->_modulo11($parte1.$parte2);
		$this->_digitoGeral = $digito_geral;

		return $parte1.$digito_geral.$parte2;
	}

	/**
	 * Pega os c�digos para gera��o do boleto
	 *
	 * @param array $params Par�metros do boleto
	 * @return array C�digos
	 */
	protected function _getCodes($params) {
		$barcode = $this->_getValorCodigoBarras($params);
		$linha_digitavel = $this->_getLinhaDigitavel($params);
		return array('barcode' => $barcode, 'linha_digitavel' => $linha_digitavel);
	}

	protected function _formatNumbers($number, $insert, $loop) {
		$number = str_replace(",","",(string)$number);
		while(strlen($number)<$loop) {
			$number = $insert . $number;
		}

		return $number;
	}
}
