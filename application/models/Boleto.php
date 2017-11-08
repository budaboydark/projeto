<?php
abstract class Application_Model_Boleto
{
	/**
	 * Parametros do boleto
	 * @var array
	 */
	protected $_params = array();

	/**
	 * Código da moeda Brasileira para geração de boletos
	 * @var int
	 */
	protected $_moeda = 9;

	public abstract function __construct($params);

	public abstract function getParams();

	/**
	 * Método para iniicialização dos parâmetros de acordo com os parâmetros
	 * necessários para cada tipo de boleto
	 *
	 * @param array $params Parametros a serem inseridos
	 * @param string codigo_boleto Código do Boleto no banco de dados
	 */
	protected function _init($params,$codigo_boleto) {
		$model_boleto = new Application_Model_DbTable_Boleto();
		$atributos = $model_boleto->getParams($codigo_boleto);
		foreach ($atributos as $nome){
			if (isset($params[$nome])) {
				$this->_params[$nome] = $params[$nome];
			}
		}
	}

	/**
	 * Método para pesquisa de valores no atributo de parâmetros para quando
	 * houver tentativa de acesso a um atributo do objeto que não estiver setado
	 *
	 * @param string $name Nome do Atributo
	 * @return mixed
	 */
	public function __get($name) {
		$method = 'get' . ucfirst($name);
		$methods = get_class_methods($this);
		if (in_array($method, $methods)) {
			$result = call_user_func(array($this, $method));
		} else {
			if (!isset($this->$name)) {
				throw new InvalidArgumentException("Invalid Parameter: '$name'");
			} else {
				$result = $this->_params[$name];
			}
		}
		return $result;
	}


	/**
	 * Método para setagem de parametros dos boletos
	 *
	 * @param string $name Nome do parâmetro
	 * @param mixed $value Valor a ser atribuído ao parâmetro
	 */
	public function __set($name, $value) {
		if (!isset($this->$name)) {
			throw new InvalidArgumentException("Invalid Parameter: '$name'");
		}
		$this->_params[$name] = $value;
	}

	public function __isset($name) {
		return array_key_exists($name, $this->_params);
	}

	/**
	 * Módulo de 11
	 *
	 * Cálculo para dígito verificador
	 *
	 * @param string $numero Número para cálculo do dígito
	 * @return int Dígito Verificador
	 */
	protected function _modulo11($numero, $not_zero = true,$withX = false) {
		// transfora a string em um array
		$numeros = str_split($numero);
		// variavel para multiplicação
		$aux = 2;
		// variavel para aramazenamento da soma dos produtos
		$somatorio = 0;
		// laço do fim até o iní­cio do array de números
		for ($i=count($numeros)-1;$i>=0;$i--) {
			// soma o produto da posição atual com o auxiliar ao somatório
			$somatorio += $numeros[$i]*$aux;

			// verifica se auxiliar é igual a nove
			if ($aux == 9){ // volta para 2
				$aux = 2;
			} else { // incrementa auxiliar
				$aux ++;
			}
		}
		// resultado do cálculo do digito verificador
		$result = 11 - $somatorio%11;
		// se o resultado for maior que 9 entã o digito é 0;
		if ($result > 9) {
			if ($withX && $result == 10) {
				$result = 'x';
			} else {
				$result = 0;
			}
		}

		if ($result == 0 && $not_zero) {
			$result = 1;
		}

		return $result;
	}

	/**
	 * Módulo de 10
	 *
	 * Cálculo para dígito verificador
	 *
	 * @param string $numero Número para cálculo do dígito
	 * @return int Dígito Verificador
	 */
	protected function _modulo10($numero) {
		// transfora a string em um array
		$numeros = str_split($numero);
		// variavel para multiplicação
		$aux = 2;
		// variavel para aramazenamento da soma dos produtos
		$somatorio = 0;
		// laço do fim até o iní­cio do array de números
		for ($i = count($numeros) -1; $i >=0; $i--) {
			// produto da posição atual de numeros e auxiliar
			$produto = $numeros[$i]*$aux;
			// seo o produto for maior que 9 então decrementa 9
			if ($produto > 9){
				$produto -= 9;
			}
			// soma o produto ao somatório
			$somatorio += $produto;
			// se auxilar é 1 então para a próxima volta é 2 e vice-versa
			if ($aux == 1) {
				$aux = 2;
			} else {
				$aux = 1;
			}
		}
		// resultado do cálculo do dígito verificador
		$result = $somatorio%10;
		// se resultado maior que zero
		if ($result > 0) {
			$result = 10 - $result;
		}

		return $result;
	}

	/**
	 * Módulo de 11 para boleto com registro
	 * @param string $numero Número para cálculo do dígito
	 * @return int Dígito Verificador
	 */
	protected function _modulo11ComRegistro($numero) {
		// FATOR
		$fator = "2765432765432";
		$arrFator = str_split($fator);
		$lenFator = strlen($fator);

		// STRING
		$str = $this->carteira.$numero;
		$arrStr = str_split($str);
		$lenStr = strlen($str);

		$soma = 0;
		if($lenStr == $lenFator) {
			$i = 0;
			$dv = null;
			while($i < $lenFator){
				$soma += $arrFator[$i] * $arrStr[$i];
				$i++;
			}

			$resto = $soma % 11;

			switch($resto) {
				case '0':
					$dv = '0';
				break;

				case '1':
					$dv = 'P';
				break;

				default:
					$dv = 11 - $resto;
				break;
			}

			return $dv;
		}

		return false;
	}

	/**
	 * Cálculo de fator de vencimento
	 *
	 * @param int $data Data no formato timestamp
	 * @return int Fator Calculado
	 */
	protected function _fatorVencimento($data) {
		$data = strtotime($data);
		// data base para cálculo conforme manual da caixa
		$data_base = strtotime('1997-10-07');
		// coeficiente padrão para cálculo de dias
		$coeficiente = 86400; // 60*60*24 = 1 dia

		return round(($data-$data_base)/$coeficiente);
	}

	/**
	 * Método para geração de imagem do código de barras
	 *
	 * @param string $numero Número do código de barras a ser gerado
	 * @return string Imagem em codificação base64
	 */
	protected function _getBarcode($numero) {
		$barcode = new Zend_Barcode();
		$renderer = $barcode->factory('code25interleaved','image',array('text'=>$numero, 'drawText' => false),array());
		ob_start();
		$image = $renderer->draw();
		imagepng($image);

		return base64_encode(ob_get_clean());
	}

	/**
	 * Formata um campo da linha digitavel inserindo o ponto de separação e o
	 * dígito verificador
	 *
	 * @param string $campo Campo a ser formatado
	 * @return string Campo Formatado
	 */
	protected function _formatCampo($campo) {
		// adiciona ao campo o digito verificador
		$campo .= $this->_modulo10($campo);
		// separa o campo na quinta posição por um ponto
		$campo = substr($campo,0,5).'.'.substr($campo,5,strlen($campo)-5);
		return $campo;
	}
}
