<?php
namespace Cnab\Remessa\Cnab240;

class SegmentosCarrega extends \Cnab\Format\Linha
{
    
    public function carregaSegmentoP($segmento,$boleto,$dados){
        $dateVencimento = $boleto['data_vencimento'] instanceof \DateTime ? $boleto['data_vencimento'] : new \DateTime($boleto['data_vencimento']);
        $dateCadastro = $boleto['data_cadastro'] instanceof \DateTime ? $boleto['data_cadastro'] : new \DateTime($boleto['data_cadastro']);
        $dateJurosMora = clone $dateVencimento;
            
        $segmento->segmento_p->codigo_banco = $dados->headerArquivo->codigo_banco;
        $segmento->segmento_p->lote_servico = $dados->headerLote->lote_servico;
        $segmento->segmento_p->agencia = $dados->headerArquivo->agencia;
        $segmento->segmento_p->agencia_dv = $dados->headerArquivo->agencia_dv;
        $segmento->segmento_p->forma_cadastramento = $boleto['registrado'] ? 1 : 2; // 1 = Com, 2 = Sem Registro
        $segmento->segmento_p->numero_documento = $boleto['numero_documento'];
        $segmento->segmento_p->vencimento = $dateVencimento;
        $segmento->segmento_p->valor_titulo = $boleto['valor'];
        $segmento->segmento_p->especie = $boleto['especie']; // 4 = Duplicata serviço
        $segmento->segmento_p->aceite = $boleto['aceite'];
        $segmento->segmento_p->data_emissao = $dateCadastro;
        $segmento->segmento_p->codigo_juros_mora = 1; // 1 = Por dia
        if (!empty($boleto['dias_iniciar_contagem_juros']) && is_numeric($boleto['dias_iniciar_contagem_juros'])) {
            $dateJurosMora->modify("+{$boleto['dias_iniciar_contagem_juros']} days");
        } else {
            $dateJurosMora->modify('+1 day');
        }
        
        $segmento->segmento_p->data_juros_mora = $dateJurosMora;

        $segmento->segmento_p->valor_juros_mora = $boleto['juros_de_um_dia'];
        if ($boleto['valor_desconto'] > 0) {
            $segmento->segmento_p->codigo_desconto_1 = 1; // valor fixo
            $segmento->segmento_p->data_desconto_1 = $boleto['data_desconto'];
            $segmento->segmento_p->valor_desconto_1 = $boleto['valor_desconto'];
        } else {
            $segmento->segmento_p->codigo_desconto_1 = 0; // sem desconto
            $segmento->segmento_p->data_desconto_1 = 0;
            $segmento->segmento_p->valor_desconto_1 = 0;
        }
        $segmento->segmento_p->valor_abatimento = 0;
        $segmento->segmento_p->uso_empresa = $boleto['numero_documento'];
        
        if (!empty($boleto['codigo_protesto']) && !empty($boleto['prazo_protesto'])) {
            $segmento->segmento_p->codigo_protesto = $boleto['codigo_protesto'];
            $segmento->segmento_p->prazo_protesto = $boleto['prazo_protesto'];
        } else {
            $segmento->segmento_p->codigo_protesto = 3; // 3 = Não protestar
            $segmento->segmento_p->prazo_protesto = 0;
        }
        
        if(!($dados->codigo_banco == \Cnab\Banco::BANCO_DO_BRASIL)){
            if(isset($boleto['baixar_apos_dias'])) {
                if($boleto['baixar_apos_dias'] === false) {
                    // não baixar / devolver
                    $segmento->segmento_p->codigo_baixa = 2;
                    $segmento->segmento_p->prazo_baixa = 0;
                } else {
                    // baixa automática
                    $segmento->segmento_p->codigo_baixa = 1;
                    $segmento->segmento_p->prazo_baixa = $boleto['baixar_apos_dias'];
                }
            } else {
                $segmento->segmento_p->codigo_baixa = 0;
                $segmento->segmento_p->prazo_baixa = 0;
            }                                                   
        
        }
        
        if (array_key_exists('identificacao_distribuicao', $boleto)) {
            $segmento->segmento_p->identificacao_distribuicao = $boleto['identificacao_distribuicao'];
        }
        
        if ($boleto['tipo'] == 'remessa') {
            $segmento->segmento_p->codigo_ocorrencia = 1;
        } elseif ($boleto['tipo'] == 'baixa') {
            $segmento->segmento_p->codigo_ocorrencia = 2;
        } else {
            throw new \Exception('Tipo de detalhe inválido: '.$boleto['tipo']);
        }       
        
        return $segmento->segmento_p;


    }

    public function carregaSegmentoPEspecial($segmento,$boleto,$dados){
    
        switch($dados->codigo_banco){
            case \Cnab\Banco::BANCO_DO_BRASIL:
                $segmento->conta = $dados->headerArquivo->conta;
                $segmento->conta_dv = $dados->headerArquivo->conta_dv;
                // Informar 1 – para carteira 11/12 na modalidade Simples; 2 ou 3 – para carteira 11/17 modalidade
                // Vinculada/Caucionada e carteira 31; 4 – para carteira 11/17 modalidade Descontada e carteira 51; e 7 – para
                // carteira 17 modalidade Simples.
                if($boleto['carteira'] == 17 && $boleto['codigo_carteira'] == \Cnab\CodigoCarteira::COBRANCA_SIMPLES) {
                    $segmento->codigo_carteira = 7;
                } else {
                    $segmento->codigo_carteira = $boleto['codigo_carteira'];
                }              
                // Campo não tratado pelo sistema. Informar 'zeros'.
                // O sistema considera a informação que foi cadastrada na
                // sua carteira junto ao Banco do Brasil.
                $segmento->codigo_baixa = 0;
                $segmento->prazo_baixa = 0;
                
                
            break;

            case \Cnab\Banco::BRADESCO:
            $segmento->conta = $dados->headerArquivo->conta;
            $segmento->conta_dv = $dados->headerArquivo->conta_dv;
            // Informar 1 – para carteira 11/12 na modalidade Simples; 2 ou 3 – para carteira 11/17 modalidade
            // Vinculada/Caucionada e carteira 31; 4 – para carteira 11/17 modalidade Descontada e carteira 51; e 7 – para
            // carteira 17 modalidade Simples.
            if($boleto['carteira'] == 17 && $boleto['codigo_carteira'] == \Cnab\CodigoCarteira::COBRANCA_SIMPLES) {
                $segmento->codigo_carteira = 7;
            } else {
                $segmento->codigo_carteira = $boleto['codigo_carteira'];
            }              
            // Campo não tratado pelo sistema. Informar 'zeros'.
            // O sistema considera a informação que foi cadastrada na
            // sua carteira junto ao Banco do Brasil.
            $segmento->codigo_baixa = 0;
            $segmento->prazo_baixa = 0;
            break;

            case \Cnab\Banco::CEF:
                $segmento->conta = $dados->headerArquivo->conta;
                $segmento->conta_dv = $dados->headerArquivo->conta_dv;
                if ($dados->codigo_banco == \Cnab\Banco::CEF) {
                    $segmento->codigo_carteira = 1; // 1 = Cobrança Simples
                }
                $segmento->codigo_baixa = 0;
                $segmento->prazo_baixa = 0;
                
            break;
        }
        return $segmento;

    }

    public function carregaSegmentoQ($segmento,$boleto,$dados){
        
                // SEGMENTO Q -------------------------------
                $segmento->segmento_q->codigo_banco = $dados->headerArquivo->codigo_banco;
                $segmento->segmento_q->lote_servico = $dados->headerLote->lote_servico;
                // PAGADOR SEGMENTO Q
        
                if (@strlen($boleto['sacado_tipo']) == 2) {
                    $segmento->segmento_q->sacado_codigo_inscricao = 2;
                    $segmento->segmento_q->sacado_numero_inscricao = $boleto['sacado_numero_inscricao'];
                    $segmento->segmento_q->nome = $this->prepareText($boleto['sacado_razao_social']);
                } else {
                    $segmento->segmento_q->sacado_codigo_inscricao = 1;
                    $segmento->segmento_q->sacado_numero_inscricao = $boleto['sacado_numero_inscricao'];
                    $segmento->segmento_q->nome = $this->prepareText($boleto['sacado_nome']);
                }
                //AVALISTA SEGMENTO Q
                $segmento->segmento_q->sacador_codigo_inscricao = 2;
                $segmento->segmento_q->sacador_numero_inscricao = $dados->headerArquivo->numero_inscricao;
                $segmento->segmento_q->sacador_nome = $this->prepareText($dados->headerArquivo->nome_empresa);
        
                $segmento->segmento_q->logradouro = $this->prepareText($boleto['sacado_logradouro']);
                $segmento->segmento_q->bairro = $this->prepareText($boleto['sacado_bairro']);
                $segmento->segmento_q->cep = str_replace('-', '', $boleto['sacado_cep']);
                $segmento->segmento_q->cidade = $this->prepareText($boleto['sacado_cidade']);
                $segmento->segmento_q->estado = $boleto['sacado_uf'];
        
                return $segmento->segmento_q;
        
        
    }

    public function carregaSegmentoA($detalhe,$dados,$boleto){
        
                $detalhe->segmento_a->codigo_banco = $dados->headerArquivo->codigo_banco;
                $detalhe->segmento_a->lote_servico = $dados->headerLote->lote_servico;
                $detalhe->segmento_a->tipo_registro = 3;
                $detalhe->segmento_a->numero_sequencial_lote = 1;
                $detalhe->segmento_a->codigo_segmento = 'A';
                // FAVORECIDO
                $detalhe->segmento_a->camara = $boleto['camara'];
                $detalhe->segmento_a->banco = $dados->codigo_banco;
                $detalhe->segmento_a->agencia = $dados->headerArquivo->agencia;
                $detalhe->segmento_a->agencia_dv = $dados->headerArquivo->agencia_dv;
                $detalhe->segmento_a->conta = $dados->configuracao['conta'];
                $detalhe->segmento_a->conta_dv = $dados->configuracao['conta_dv'];
                $detalhe->segmento_a->nome = '';
                // Crédito
                $detalhe->segmento_a->numero_documento = '';
                $detalhe->segmento_a->data_pagamento = $boleto['data_pagamento'];
                $detalhe->segmento_a->moeda_tipo = '';
                $detalhe->segmento_a->moeda_quantidade = $boleto['moeda_quantidade'];
                $detalhe->segmento_a->valor_lancamento = $boleto['valor_lancamento'];
                $detalhe->segmento_a->nosso_numero = $boleto['nosso_numero'];
                $detalhe->segmento_a->data_real = $boleto['data_real'];
                $detalhe->segmento_a->valor_real = $boleto['valor_real'];
                $detalhe->segmento_a->informacao2 = $boleto['informacao2'];
                $detalhe->segmento_a->codigo_doc = '';
                $detalhe->segmento_a->codigo_ted = '';
                $detalhe->segmento_a->cnab = '';
                $detalhe->segmento_a->aviso = 1;
                $detalhe->segmento_a->ocorrencias = '';
        
                return $detalhe;
        
    }

    public function carregaSegmentoB($detalhe,$dados,$boleto){
        
        $detalhe->segmento_b->codigo_banco = $this->headerArquivo->codigo_banco;
        $detalhe->segmento_b->lote_servico = $this->headerLote->lote_servico;
        $detalhe->segmento_b->tipo_registro = 3;
        $detalhe->segmento_b->numero_sequencial_lote = 1;
        $detalhe->segmento_b->codigo_segmento = 'B';
        // FAVORECIDO
        $detalhe->segmento_b->inscricao_tipo = 1;
        $detalhe->segmento_b->inscricao_numero = 1;
        $detalhe->segmento_b->logradouro = '';
        $detalhe->segmento_b->numero = 00235;
        $detalhe->segmento_b->cep = 91040360;
        $detalhe->segmento_b->complemento = '';
        $detalhe->segmento_b->bairro = '';
        $detalhe->segmento_b->cidade = '';
        $detalhe->segmento_b->estado = '';
        // Pagto
        $detalhe->segmento_b->vencimento = $boleto['data_vencimento'];// data do vencimento
        $detalhe->segmento_b->valor_doc = $boleto['valor_real']; // valor do documento
        $detalhe->segmento_b->valor_abatimento = $boleto['valor_abatimento']; // valor do abatimento
        $detalhe->segmento_b->valor_desconto = $boleto['valor_desconto']; // valor do desconto
        $detalhe->segmento_b->valor_mora = $boleto['juros_de_um_dia']; // valor do juros
        $detalhe->segmento_b->valor_multa = $boleto['valor_multa']; // valor multa
        $detalhe->segmento_b->codigo_documento = $boleto['codigo_documento']; // codigo do documento
    
    }
    public function carregaSegmentoR($detalhe,$boleto,$dados){
        // SEGMENTO R -------------OPICIONAL PARA QUALQUER BANCO------------------
        $detalhe->segmento_r->codigo_banco = $detalhe->segmento_p->codigo_banco;
        $detalhe->segmento_r->lote_servico = $detalhe->segmento_p->lote_servico;
        $detalhe->segmento_r->codigo_ocorrencia = $detalhe->segmento_p->codigo_ocorrencia;
        if ($boleto['valor_multa'] > 0) {
            $detalhe->segmento_r->codigo_multa = 1;
            $detalhe->segmento_r->valor_multa = $boleto['valor_multa'];
            $detalhe->segmento_r->data_multa = $boleto['data_multa'];
        } else {
            $detalhe->segmento_r->codigo_multa = 0;
            $detalhe->segmento_r->valor_multa = 0;
            $detalhe->segmento_r->data_multa = 0;
        }
        return $detalhe;

    }
                    
    public function carregaTrailerArquivo($dados){
        $dados->trailerArquivo->codigo_banco = $dados->headerArquivo->codigo_banco;
        return $dados->trailerArquivo;
        
    }
        
    public function isUtf8($string){
        return preg_match('%^(?:
                    [\x09\x0A\x0D\x20-\x7E]
                | [\xC2-\xDF][\x80-\xBF]
                | \xE0[\xA0-\xBF][\x80-\xBF]
                | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}
                | \xED[\x80-\x9F][\x80-\xBF]
                | \xF0[\x90-\xBF][\x80-\xBF]{2}
                | [\xF1-\xF3][\x80-\xBF]{3}
                | \xF4[\x80-\x8F][\x80-\xBF]{2}
                )*$%xs',
                $string
        );
    }
    
    public function removeAccents($string){
        return preg_replace(
            array(
                    '/\xc3[\x80-\x85]/',
                    '/\xc3\x87/',
                    '/\xc3[\x88-\x8b]/',
                    '/\xc3[\x8c-\x8f]/',
                    '/\xc3([\x92-\x96]|\x98)/',
                    '/\xc3[\x99-\x9c]/',

                    '/\xc3[\xa0-\xa5]/',
                    '/\xc3\xa7/',
                    '/\xc3[\xa8-\xab]/',
                    '/\xc3[\xac-\xaf]/',
                    '/\xc3([\xb2-\xb6]|\xb8)/',
                    '/\xc3[\xb9-\xbc]/',
                    '/\xC2\xAA/',
                    '/\xC2\xBA/',
            ),
            str_split('ACEIOUaceiouao', 1),
            $this->isUtf8($string) ? $string : utf8_encode($string)
        );
    }

    public function prepareText($text, $remove = null)
    {
        $result = strtoupper($this->removeAccents(trim(html_entity_decode($text))));
        if ($remove) {
            $result = str_replace(str_split($remove), '', $result);
        }

        return $result;
    }
                    
}
