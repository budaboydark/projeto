<?php
class Application_Model_RemessaBradesco extends Application_Model_Remessa
{
    public function __construct(){

    }

    public function geraremessa($params){

        $codigo_banco = Cnab\Banco::BRADESCO;
        $arquivo = new Cnab\Remessa\Cnab240\Arquivo($codigo_banco);
        $header = $params['header'];
        $detalhe = $params['detalhe'];

        $arquivo->configure(array(
            'data_geracao'  => new DateTime(), //definido de acordo com os dados vindos da loja
            'data_gravacao' => new DateTime(), //definido de acordo com os dados vindos da loja
            'hora_geracao' =>  date("His"), //definido de acordo com os dados vindos da loja
            'nome_empresa' => $header['nome_empresa'], // seu nome de empresa
            'razao_social'  => $header['razao_social'],  // sua razão social
            'numero_inscricao' => $header['numero_inscricao'],
            'banco'         => $codigo_banco, //código do banco
            'logradouro'    => $header['logradouro'],
            'bairro'        => $header['bairro'],
            'cidade'        => $header['cidade'],
            'uf'            => $header['uf'],
            'cep'           => $header['cep'],
            'agencia'       => $header['codigo_agencia'],
            'conta'         => $header['conta'], // número da conta
            'conta_dac'     => $header['conta_dac'], // digito da conta
            'codigo_convenio' => str_pad($header['codigo_convenio'],9,"0",STR_PAD_LEFT),
            'codigo_carteira' => $header['codigo_carteira'],
            'variacao_carteira' => $header['variacao_carteira'],
            'conta_dv' => $header['conta_dv'],
            'agencia_dv' => $header['agencia_dv'],
            'numero_sequencial_arquivo' => $header['numero_sequencial_arquivo']
        ));
        foreach($detalhe as $detalhe_boleto){

            $arquivo->insertDetalhe(array(
            'codigo_ocorrencia' => $detalhe_boleto['codigo_ocorrencia'], // 1 = Entrada de título, futuramente poderemos ter uma constante
            'nosso_numero' => $detalhe_boleto['nosso_numero'],
            'numero_documento' => $detalhe_boleto['numero_documento'],
            'carteira' => $header['codigo_carteira'],
            'codigo_carteira' => \Cnab\CodigoCarteira::COBRANCA_SIMPLES,
            'especie' => \Cnab\Especie::BRADESCO_DUPLICATA_MERCANTIL, // Você pode consultar as especies Cnab\Especie::CEF_OUTROS, futuramente poderemos ter uma tabela na documentação
            'aceite' => 'N', // "S" ou "N"
            'registrado' => true,
            'valor' => $detalhe_boleto['valor'], // Valor do boleto
            'instrucao1' => '', // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
            'instrucao2' => '', // preenchido com zeros
            'sacado_razao_social' => $detalhe_boleto['sacado_cpfcnpj'], // O Sacado é o cliente, preste atenção nos campos abaixo
            'sacado_nome' => $detalhe_boleto['sacado_nome'],
            'sacado_tipo_inscricao' => $detalhe_boleto['sacado_tipo'], //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
            'sacado_numero_inscricao' => $detalhe_boleto['sacado_cpfcnpj'],
            'sacado_logradouro' => $detalhe_boleto['sacado_logradouro'],
            'sacado_bairro' => $detalhe_boleto['sacado_bairro'],
            'sacado_cep' => $detalhe_boleto['sacado_cep'],
            'sacado_cidade' => $detalhe_boleto['sacado_cidade'],
            'sacado_uf' => $detalhe_boleto['sacado_uf'],
            'data_vencimento' => $detalhe_boleto['data_vencimento'],
            'data_cadastro' => $detalhe_boleto['data_cadastro'],
            'juros_de_um_dia' => $detalhe_boleto['juros_de_um_dia'], // Valor do juros de 1 dia'
            'data_desconto' => $detalhe_boleto['data_desconto'],
            'valor_desconto' => $detalhe_boleto['valor_desconto'], // Valor do desconto
            'prazo' => 10, // prazo de dias para o cliente pagar após o vencimento
            'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
            /*
            'data_multa' => $detalhe_boleto['data_multa'], // data da multa
            'valor_multa' => $detalhe_boleto['valor_multa'], // valor da multa
            */
            'baixar_apos_dias' => 30,
            'dias_iniciar_contagem_juros' => 1,
            ));
        }
        $texto = $arquivo->getText();
        return $texto;
    }
}