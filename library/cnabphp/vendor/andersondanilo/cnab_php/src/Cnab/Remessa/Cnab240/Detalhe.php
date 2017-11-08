<?php

namespace Cnab\Remessa\Cnab240;

class Detalhe
{
    public $segmento_p;
    public $segmento_q;
    public $banco_codigo;
    public $last_error;

    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {

        $banco = $arquivo->headerArquivo->codigo_banco;
        $this->banco_codigo = $banco;
        $this->segmento_p = new SegmentoP($arquivo);
        $this->segmento_q = new SegmentoQ($arquivo);
    }

    public function validate()
    {
        $this->last_error = null;
        foreach ($this->listSegmento() as $segmento) {
            if (!$segmento->validate()) {
                $this->last_error = get_class($segmento).': '.$segmento->last_error;
            }
        }
        return is_null($this->last_error);
    }

    /**
     * Lista todos os segmentos deste detalhe.
     *
     * @return array
     */
    public function listSegmento()
    {
        $array[] = $this->segmento_p;
        $array[] = $this->segmento_q;
        return $array;
    }

    /**
     * Retorna todas as linhas destes detalhes.
     *
     * @return string
     */
    public function getEncoded()
    {
        $text = array();
        foreach ($this->listSegmento() as $segmento) {
            $text[] = $segmento->getEncoded();
        }

        return implode(Arquivo::QUEBRA_LINHA, $text);
    }
}
