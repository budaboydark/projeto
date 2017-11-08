<?php

namespace Cnab\Remessa\Cnab240;

class TrailerLote extends \Cnab\Format\Linha
{
    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'trailer_lote');
    }

    public function carregaTrailerLote($dados){
        $dados->trailerLote->codigo_banco = $dados->headerArquivo->codigo_banco;
        $dados->trailerLote->lote_servico = $dados->headerLote->lote_servico;
    }

}
