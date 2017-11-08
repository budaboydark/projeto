<?php
class Application_Model_BoletoBanrisul extends Application_Model_Boleto
{
	/**
	 * Código do banco
	 * @var string
	 */
	protected $_codigoBanco = '041';

	/**
	 * Construtor da classe.
	 * Este método passa os parâmetros recebidos para o método _init() da classe
	 * pai.
	 *
	 * @param array $params Parâmetros necessários para geração do boleto
	 */
	public function __construct($params) {
		$this->_init($params,'BANRISUL');
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
		$logo = file_get_contents(APPLICATION_PATH.'/../public/img/logo-banrisul.jpg');

		// variáveis para a camada de visualização
		$vars = array (
			'barcode'          => $barcode,
			'codigo_banco'     => $this->_codigoBanco.'-'.$this->_modulo11($this->_codigoBanco,false),
			'codigo_cedente'   => $this->codigo_cedente.'-'.$this->_modulo11($this->codigo_cedente,false),
			'data_hoje'        => date('d/m/Y'),
			'especie'          => 'R$',
			'linha_digitavel'  => $linha_digitavel,
			'logo'             => base64_encode($logo),
			'valor'            => number_format(($this->valor/100),2,',','.'),
			'vencimento'       => $data->toString('dd/MM/y'),
			'nosso_numero'     => $this->_getNossoNumero(),
			'numero_documento' => (int)$this->nosso_numero,
			'agencia'          => $this->agencia.'-'.$this->_modulo11($this->agencia,false),
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
		$valor = str_pad($this->valor,10,0,STR_PAD_LEFT);
		$parte2 = $fator_vencimento.$valor.$this->_getCampoLivre();

		// calculo do digito verificador
		$digito_verificador = $this->_modulo11($parte1.$parte2);

		//retorno do codigo completo
		return $parte1.$digito_verificador.$parte2;
	}


	/**
	 * Método para geração dos digitos verificadores de módulos 10 e 11
	 * consecutivamente
	 *
	 * @param string $numero Numero para geração dos verificadores
	 * @return string Digitos verificadores gerados
	 */
	protected function _modulo10eModulo11($numero) {
		$digito_modulo10 = $this->_modulo10($numero);
		$flag = false;
		$segundo_digito = 0;
		while (!$flag) {
			$numeros = str_split($numero.$digito_modulo10);
			// variavel para multiplicação
			$aux = 2;
			// variavel para aramazenamento da soma dos produtos
			$somatorio = 0;
			// laço do fim até o iní­cio do array de números
			for ($i=count($numeros)-1;$i>=0;$i--) {
				// soma o produto da posição atual com o auxiliar ao somatório
				$somatorio += $numeros[$i]*$aux;

				// verifica se auxiliar é igual a sete
				if ($aux == 7) { // volta para 2
					$aux = 2;
				} else { // incrementa auxiliar
					$aux ++;
				}
			}

			// resultado do cálculo do digito verificador
			$segundo_digito = 11 - $somatorio%11;

			if ($segundo_digito > 9) {
				$digito_modulo10 += 1;
				if ($digito_modulo10 > 9) {
					$digito_modulo10 = 0;
				}
			} else {
				$flag = true;
			}
		}
		return $digito_modulo10.$segundo_digito;
	}

	/**
	 * Método para montagem do nosso número
	 *
	 * @return string Nosso Número
	 */
	protected function _getNossoNumero() {
		$nosso_numero = str_pad((int)$this->nosso_numero,8,'0',STR_PAD_LEFT);
		return $nosso_numero.'-'.$this->_modulo10eModulo11($nosso_numero);
	}


	/**
	 * Método para montagem e obtenção do campo livre
	 *
	 * @return string Valor do Campo Livre
	 */
	protected function _getCampoLivre() {
		$campo_livre =
			'21'.
			$this->agencia.
			$this->codigo_cedente.
			str_pad((int)$this->nosso_numero,8,'0',STR_PAD_LEFT).
			'40';
		$campo_livre .= $this->_modulo10eModulo11($campo_livre);
		return $campo_livre;

	}

}
