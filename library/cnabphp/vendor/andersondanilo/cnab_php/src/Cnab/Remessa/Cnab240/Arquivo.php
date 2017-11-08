<?php
namespace Cnab\Remessa\Cnab240;

class Arquivo implements \Cnab\Remessa\IArquivo
{
    private $_data_gravacao;
    private $_data_geracao;
    public $headerArquivo;
    public $headerLote;
    public $detalhes = array();
    public $trailerLote;
    public $trailerArquivo;
    public $banco;
    public $codigo_banco;
    public $configuracao = array();
    public $layoutVersao;
    const   QUEBRA_LINHA = "\r\n";

    public function __construct($codigo_banco, $layoutVersao = null)
    {
        $this->codigo_banco = $codigo_banco;
        $this->layoutVersao = $layoutVersao;
        $this->banco = \Cnab\Banco::getBanco($this->codigo_banco);
    }

    public function configure(array $params)
    {

        $banco = \Cnab\Banco::getBanco($this->codigo_banco);
        $this->headerArquivo = new HeaderArquivo($this);
        $this->headerLote = new HeaderLote($this);
        $this->trailerLote = new TrailerLote($this);
        $this->trailerArquivo = new TrailerArquivo($this);
        $segmentoCarrega = new SegmentosCarrega();

        switch($this->codigo_banco){
            case \Cnab\Banco::CEF:
                //carrega os campos validados de acordo com o banco para configuracao.
                $this->configuracao = $this->headerArquivo->carregaCampos($this->codigo_banco,$params);
                //carrega o objeto headerArquivo os dados validados.
                $this->headerArquivo = $this->headerArquivo->carregaHeaderArquivo($this->headerArquivo,$this->configuracao,$this->banco);               
                $this->headerLote->carregaHeaderLote($this);
                $this->trailerLote->carregaTrailerLote($this);                
            break;
            case \Cnab\Banco::BANCO_DO_BRASIL:
                //carrega os campos validados de acordo com o banco para configuracao.
                $this->configuracao = $this->headerArquivo->carregaCampos($this->codigo_banco,$params);
                //carrega o objeto headerArquivo os dados validados.                       
                $this->headerArquivo = $this->headerArquivo->carregaHeaderArquivo($this->headerArquivo,$this->configuracao,$this->banco);               
                $this->headerLote->carregaHeaderLote($this);
                $this->trailerLote->carregaTrailerLote($this);
                break;
            case \Cnab\Banco::BRADESCO:
                //carrega os campos validados de acordo com o banco para configuracao.       
                $this->configuracao = $this->headerArquivo->carregaCampos($this->codigo_banco,$params);
                //carrega o objeto headerArquivo os dados validados.
                $this->headerArquivo = $this->headerArquivo->carregaHeaderArquivo($this->headerArquivo,$this->configuracao,$this->banco);               
                $this->headerLote->carregaHeaderLote($this);
                $this->trailerLote->carregaTrailerLote($this);                     
            break;

        }
        $this->trailerArquivo = $segmentoCarrega->carregaTrailerArquivo($this);
        $this->data_geracao = $this->configuracao['data_geracao'];
        $this->data_gravacao = $this->configuracao['data_gravacao'];
       
    }

    public function mod11($num, $base = 9, $r = 0)
    {
        $soma = 0;
        $fator = 2;
        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; --$i) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num, $i - 1, 1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) { // restaura fator de multiplicacao para 2
                $fator = 1;
            }
            ++$fator;
        }
        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }

            return $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;

            return $resto;
        }

    }

    public function insertDetalhe(array $boleto, $tipo = 'remessa')
    {
        //Fazer switch dos Segmentos.
        $detalhe = new Detalhe($this);
        $segmentosCarrega = new SegmentosCarrega();
        $boleto['tipo'] = $tipo;
        switch($this->codigo_banco){
            case \Cnab\Banco::BANCO_DO_BRASIL:
                //configuração do SEGMENTO P
                $segP = $segmentosCarrega->carregaSegmentoP($detalhe,$boleto,$this);
                $segmentP = $segmentosCarrega->carregaSegmentoPEspecial($segP,$boleto,$this);
                $detalhe->segmento_p = $segmentP;
                $detalhe->segmento_p->nosso_numero = $this->formatarNossoNumero($boleto['nosso_numero']);
                // configuração do SEGMENTO Q
                $detalhe->segmento_q = $segmentosCarrega->carregaSegmentoQ($detalhe,$boleto,$this);           
                $detalhe->segmento_q->codigo_ocorrencia = $detalhe->segmento_p->codigo_ocorrencia;
                /*
                *   VALIDADO OS DADOS do BB falta o restante.
                */
            break;
            case \Cnab\Banco::BRADESCO:
                //configuração do SEGMENTO P
                $segP = $segmentosCarrega->carregaSegmentoP($detalhe,$boleto,$this);
                $segmentP = $segmentosCarrega->carregaSegmentoPEspecial($segP,$boleto,$this);                      
                $detalhe->segmento_p = $segmentP;
                $detalhe->segmento_p->nosso_numero = $this->formatarNossoNumero($boleto['nosso_numero']);
                // configuração do SEGMENTO Q
                $detalhe->segmento_q = $segmentosCarrega->carregaSegmentoQ($detalhe,$boleto,$this);         
            break;
            case \Cnab\Banco::CEF:
                //configuração do SEGMENTO P
                $segP = $segmentosCarrega->carregaSegmentoP($detalhe,$boleto,$this);
                $segmentP = $segmentosCarrega->carregaSegmentoPEspecial($segP,$boleto,$this);
                $detalhe->segmento_p = $segmentP;
                $detalhe->segmento_p->nosso_numero = $this->formatarNossoNumero($boleto['nosso_numero']);
                // configuração do SEGMENTO Q
                $detalhe->segmento_q = $segmentosCarrega->carregaSegmentoQ($detalhe,$boleto,$this);           
                $detalhe->segmento_q->codigo_ocorrencia = $detalhe->segmento_p->codigo_ocorrencia;
            break;
        }
        
        $this->detalhes[] = $detalhe;
    }

    public function formatarNossoNumero($nossoNumero)
    {
        if(!$nossoNumero)
            return $nossoNumero;

        if ($this->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $codigo_convenio = $this->configuracao['codigo_convenio'];

            if(strlen($codigo_convenio) <= 4) {
                # Convênio de 4 digitos
                if(strlen($nossoNumero) > 7) {
                    throw new \InvalidArgumentException(
                        "Para número de convênio de 4 posições o nosso número deve ter no máximo 7 posições (sem o digito)"
                    );
                }
                $number = sprintf('%04d%07d', $codigo_convenio, $nossoNumero);
                return $number . $this->mod11($number);
            } elseif (strlen($codigo_convenio) <= 6) {
                # Convênio de 6 digitos
                if(strlen($nossoNumero) > 5) {
                    throw new \InvalidArgumentException(
                        "Para número de convênio de 6 posições o nosso número deve ter no máximo 5 posições (sem o digito)"
                    );
                }
                $number = sprintf('%06d%05d', $codigo_convenio, $nossoNumero);
                return $number . $this->mod11($number);
            } else {
                if(strlen($nossoNumero) > 10) {
                    throw new \InvalidArgumentException(
                        "Para número de convênio de 7 posições o nosso número deve ter no máximo 10 posições"
                    );
                }
                $number = sprintf('%07d%010d', $codigo_convenio, $nossoNumero);
                return $number;
            }
        }

        return $nossoNumero;
    }

    public function listDetalhes()
    {
        return $this->detalhes;
    }

    public function getText()
    {
        
        $numero_sequencial_lote = 1;
        $qtde_registro_lote = 2; // header e trailer = 2
        $qtde_titulo_cobranca_simples = 0;
        $valor_total_titulo_simples = 0;
        // valida os dados
        
        if (!$this->headerArquivo->validate()) {
            throw new \InvalidArgumentException($this->headerArquivo->last_error.'1');
        }

        if (!$this->headerLote->validate()) {
            throw new \InvalidArgumentException($this->headerLote->last_error.'2');
        }        
        
        $dados = $this->headerArquivo->getEncoded().self::QUEBRA_LINHA;
        $dados .= $this->headerLote->getEncoded().self::QUEBRA_LINHA;       
        foreach ($this->detalhes as $detalhe) {
            ++$qtde_titulo_cobranca_simples;
            $valor_total_titulo_simples += $detalhe->segmento_p->valor_titulo;
            foreach ($detalhe->listSegmento() as $segmento) {
                ++$qtde_registro_lote;                  
                $segmento->numero_sequencial_lote = $numero_sequencial_lote++;
                    
            }
           
            if (!$detalhe->validate()) {
                throw new \InvalidArgumentException($detalhe->last_error.'3');
            }
 
            $dados .= $detalhe->getEncoded().self::QUEBRA_LINHA;
        }

        $this->trailerLote->qtde_registro_lote = $qtde_registro_lote;
        $this->trailerArquivo->qtde_lotes = 1;
        $this->trailerArquivo->qtde_registros = $this->trailerLote->qtde_registro_lote + 2;

        if (!$this->trailerLote->validate()) {
            throw new \InvalidArgumentException($this->trailerLote->last_error);
        }
        if (!$this->trailerArquivo->validate()) {               
            throw new \InvalidArgumentException($this->trailerArquivo->last_error);
        }
        $dados .= $this->trailerLote->getEncoded().self::QUEBRA_LINHA;
        $dados .= $this->trailerArquivo->getEncoded().self::QUEBRA_LINHA;
        return $dados;

    }

    public function countDetalhes()
    {
        return count($this->detalhes);
    }
    public function save($filename){
        
    }

}
