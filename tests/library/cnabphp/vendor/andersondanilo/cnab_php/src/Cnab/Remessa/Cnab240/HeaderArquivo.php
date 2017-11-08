<?php

namespace Cnab\Remessa\Cnab240;

class HeaderArquivo extends \Cnab\Format\Linha
{
    public function __construct(\Cnab\Remessa\IArquivo $arquivo)
    {
        $yamlLoad = new \Cnab\Format\YamlLoad($arquivo->codigo_banco, $arquivo->layoutVersao);
        $yamlLoad->load($this, 'cnab240', 'header_arquivo');
    }


    public function carregaCampos($codigo_banco,$params){

        $campos = array(
            'data_geracao',
            'data_gravacao',
            'nome_empresa',
            'razao_social',
            'logradouro',
            'bairro',
            'cidade',
            'uf',
            'cep',
            'hora_geracao'
        );
        
        if($codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL || $codigo_banco == \Cnab\Banco::CEF) {
            $campos[] = 'codigo_convenio';
            $campos[] = 'codigo_carteira';
            $campos[] = 'variacao_carteira';
            $campos[] = 'conta_dv';
            $campos[] = 'numero_inscricao';
        }

        if ($codigo_banco == \Cnab\Banco::CEF || $codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {
            $campos[] = 'agencia';
            $campos[] = 'agencia_dv';
            $campos[] = 'conta';
            $campos[] = 'numero_sequencial_arquivo';
        }
        if($codigo_banco == \Cnab\Banco::BRADESCO){
            $campos[] = 'codigo_convenio';
            $campos[] = 'codigo_carteira';
            $campos[] = 'variacao_carteira';
            $campos[] = 'conta_dv';
            $campos[] = 'numero_inscricao';
            $campos[] = 'agencia';
            $campos[] = 'agencia_dv';
            $campos[] = 'conta';
            $campos[] = 'numero_sequencial_arquivo';
            
        }
        //Verifica os campos e retorna em caso de erro ou falta do mesmo de acordo com BANCO
        foreach ($campos as $campo) {
            if (array_key_exists($campo, $params)) {
                if (strpos($campo, 'data_') === 0 && !($params[$campo] instanceof \DateTime)) {
                    throw new \Exception("config '$campo' need to be instance of DateTime");
                }
                $configuracao[$campo] = $params[$campo];

            } else {
                throw new \Exception('Configuração "'.$campo.'" need to be set');
            }
        }
        foreach ($campos as $key) {
            if (!array_key_exists($key, $params)) {
                throw new Exception('Configuração "'.$key.'" dont exists');
            }
        }
        return $configuracao;
        
    }

    public function carregaHeaderArquivo($headerArquivo,$configuracao,$banco){

        $codigo_banco = $banco['codigo_do_banco'];
        $headerArquivo->codigo_banco = $banco['codigo_do_banco'];
        $headerArquivo->codigo_inscricao = 2;
        $headerArquivo->numero_inscricao = $configuracao['numero_inscricao'];
        $headerArquivo->agencia = $configuracao['agencia'];
        $headerArquivo->agencia_dv = $configuracao['agencia_dv'];


        if($codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL) {

            $headerArquivo->codigo_convenio = $configuracao['codigo_convenio'];
            $headerArquivo->codigo_carteira = $configuracao['codigo_carteira'];
            $headerArquivo->variacao_carteira = $configuracao['variacao_carteira'];
            $headerArquivo->conta = $configuracao['conta'];
            $headerArquivo->conta_dv = $configuracao['conta_dv'];

        }
        $headerArquivo->reservado_empresa = '00000000000000000000';
        $headerArquivo->reservado_banco = '00000000000000000000';

        if($codigo_banco == \Cnab\Banco::BRADESCO) {
            
            $headerArquivo->codigo_convenio = $configuracao['codigo_convenio'];
            $headerArquivo->codigo_carteira = $configuracao['codigo_carteira'];
            $headerArquivo->variacao_carteira = $configuracao['variacao_carteira'];
            $headerArquivo->conta = $configuracao['conta'];
            $headerArquivo->conta_dv = $configuracao['conta_dv'];
                    
        }
        if($codigo_banco == \Cnab\Banco::CEF) {
            $headerArquivo->codigo_convenio = $configuracao['codigo_convenio'];
            $headerArquivo->codigo_carteira = $configuracao['codigo_carteira'];
            $headerArquivo->variacao_carteira = $configuracao['variacao_carteira'];
            $headerArquivo->conta = $configuracao['conta'];
            $headerArquivo->conta_dv = $configuracao['conta_dv'];
        }

        $headerArquivo->nome_empresa = $configuracao['nome_empresa'];
        $headerArquivo->nome_banco = $banco['nome_do_banco'];
        $headerArquivo->codigo_remessa_retorno = 1;
        $headerArquivo->data_geracao = $configuracao['data_geracao'];
        $headerArquivo->hora_geracao = $configuracao['hora_geracao'];
        $headerArquivo->numero_sequencial_arquivo = $configuracao['numero_sequencial_arquivo'];      
        /*
        // BOLETO DA CAIXA
        if ($codigo_banco == \Cnab\Banco::CEF) {
            $headerArquivo->codigo_convenio = 0;
            if($layoutVersao === 'sigcb') {
            } else {
                $codigoConvenio = sprintf(
                    '%04d%03d%08d',
                    $params['agencia'],
                    $params['operacao'],
                    $params['conta']
                );
                $codigoConvenio .= $mod11($codigoConvenio);
                $headerArquivo->codigo_convenio = $codigoConvenio;
            }
        }
        */
        return $headerArquivo;
        
    }


}
