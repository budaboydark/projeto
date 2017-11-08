<?php
class Application_Model_BoletoSantander extends Application_Model_Boleto
{
	protected $_codigoBanco = '033';

	public function __construct($params) {
		$this->_init($params,'SANTANDER');
	}

	public function getParams() {
		$codigo_barras = $this->_getValorCodigoBarras();
		$barcode = $this->_getBarcode($codigo_barras);

		$linha_digitavel = $this->_getLinhaDigitavel();

		$data = new Zend_Date($this->vencimento);

		$logo = file_get_contents(APPLICATION_PATH.'/../public/img/logo-santander.jpg');
		$nosso_numero = $this->_getNossoNumero();
		$nosso_numero = substr($nosso_numero,0,12).'-'.substr($nosso_numero,12,1);

		// monta o array com os valores a serem retornados
		$vars = array(
			'barcode'         => $barcode,
			'linha_digitavel' => $linha_digitavel,
			'logo'            => base64_encode($logo),
			'codigo_banco'    => $this->_codigoBanco.'-'.$this->_modulo11($this->_codigoBanco,false),
			'agencia'         => $this->agencia.'-'.$this->_modulo11($this->agencia,false),
			'nosso_numero'    => $nosso_numero,
			'data_hoje'       => date('d/m/Y'),
			'vencimento'      => $data->toString('dd/MM/yy'),
			'valor'           => number_format(($this->valor/100),2,',','.'),
			'carteira'        => '102 - Cobrança Simples CSR',
		);
		$vars += $this->_params;

		return $vars;
	}

	protected function _getLinhaDigitavel() {
		$codigo_cedente_parte1 = substr($this->codigo_cedente,0,4);
		$codigo_cedente_parte2 = substr($this->codigo_cedente,4,3);

		$campo1 = $this->_formatCampo($this->_codigoBanco.$this->_moeda.'9'.$codigo_cedente_parte1);

		$nosso_numero_parte1 = substr($this->_getNossoNumero(),0,7);
		$nosso_numero_parte2 = substr($this->_getNossoNumero(),7,6);

		$campo2 = $this->_formatCampo($codigo_cedente_parte2.$nosso_numero_parte1);

		$campo3 = $this->_formatCampo($nosso_numero_parte2.'0102');

		$codigo_barras = $this->_getvalorCodigoBarras();

		$campo4 = substr($codigo_barras,4,1);

		$campo5 = $this->_fatorVencimento($this->vencimento).$this->_getValor();

		return "$campo1 $campo2 $campo3 $campo4 $campo5";
	}

	protected function _getvalorCodigoBarras() {
		$parte1 = $this->_codigoBanco.$this->_moeda;

		$parte2 =
			$this->_fatorVencimento($this->vencimento).
			$this->_getValor().
			'9'.
			$this->codigo_cedente.
			$this->_getNossoNumero().
			'0102';
		$digito = $this->_modulo11($parte1.$parte2);
		return $parte1.$digito.$parte2;
	}

	protected function _getValor() {
		return str_pad($this->valor,10,'0',STR_PAD_LEFT);
	}

	protected function _getNossoNumero() {
		$nosso_numero = str_pad((int)$this->nosso_numero,12,'0',STR_PAD_LEFT);
		return $nosso_numero.$this->_modulo11($nosso_numero,false);
	}
}
