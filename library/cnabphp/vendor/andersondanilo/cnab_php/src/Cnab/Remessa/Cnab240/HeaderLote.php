<?php

namespace Cnab\Remessa\Cnab240;

class HeaderLote extends \Cnab\Format\Linha
{
    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'header_lote');
    }


    public function carregaHeaderLote($dados){

        $dados->headerLote->codigo_banco = $dados->headerArquivo->codigo_banco;
        $dados->headerLote->lote_servico = 1;
        $dados->headerLote->tipo_operacao = 'R';
        $dados->headerLote->codigo_inscricao = $dados->headerArquivo->codigo_inscricao;
        $dados->headerLote->numero_inscricao = $dados->headerArquivo->numero_inscricao;
        $dados->headerLote->agencia = $dados->headerArquivo->agencia;
        $dados->headerLote->agencia_dv = $dados->headerArquivo->agencia_dv;       
        $dados->headerLote->codigo_convenio = $dados->headerArquivo->codigo_convenio;
        $dados->headerLote->codigo_carteira = $dados->headerArquivo->codigo_carteira;
        $dados->headerLote->variacao_carteira = $dados->headerArquivo->variacao_carteira;
        $dados->headerLote->codigo_cedente = $dados->headerArquivo->conta;
        $dados->headerLote->codigo_cedente_dv = $dados->headerArquivo->conta_dv;
        $dados->headerLote->nome_empresa = $dados->headerArquivo->nome_empresa;
        $dados->headerLote->numero_remessa = $dados->headerArquivo->numero_sequencial_arquivo;
        $dados->headerLote->data_gravacao = $dados->headerArquivo->data_geracao;
        $dados->headerLote->agencia_mais_cedente_dv = 0;

    }
}
