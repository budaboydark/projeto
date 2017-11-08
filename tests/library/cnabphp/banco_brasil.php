<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'vendor/autoload.php';

$codigo_banco = Cnab\Banco::BANCO_DO_BRASIL;
//echo $codigo_banco;
//exit;

$arquivo = new Cnab\Remessa\Cnab240\Arquivo($codigo_banco);

$arquivo->configure(array(
    'data_geracao'  => new DateTime(),
    'data_gravacao' => new DateTime(),
    'nome_fantasia' => 'Empresa Teste', // seu nome de empresa
    'razao_social'  => 'Empresa Teste',  // sua razão social
    'cnpj'          => '05005501000184', // seu cnpj completo
    'banco'         => $codigo_banco, //código do banco
    'logradouro'    => 'Logradouro da Sua empresa teste',
    'numero'        => '1187',
    'bairro'        => 'Cristo Redentor',
    'cidade'        => 'Porto Alegre',
    'uf'            => 'RS',
    'cep'           => '9999999',
    'agencia'       => '1111',
    'conta'         => '22222', // número da conta
    'conta_dac'     => '2', // digito da conta
    'codigo_convenio' => '9999999',
    'codigo_carteira' => 17,
    'variacao_carteira' => '2',
    'conta_dv' => '0',
    'agencia_dv' => '1',
    'operacao' => '1',
    'numero_sequencial_arquivo' => '1'
));

// você pode adicionar vários boletos em uma remessa

$arquivo->insertDetalhe(array(
    'codigo_ocorrencia' => 3, // 1 = Entrada de título, futuramente poderemos ter uma constante
    'nosso_numero' => '12345',
    'numero_documento' => '12345678',
    'carteira' => '11',
    'codigo_carteira' => \Cnab\CodigoCarteira::COBRANCA_SIMPLES,
    'especie' => \Cnab\Especie::BB_DUPLICATA_MERCANTIL, // Você pode consultar as especies Cnab\Especie::CEF_OUTROS, futuramente poderemos ter uma tabela na documentação
    'aceite' => 'N', // "S" ou "N"
    'registrado' => true,
    'valor' => 100.39, // Valor do boleto
    'instrucao1' => '', // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
    'instrucao2' => '', // preenchido com zeros
    'sacado_razao_social' => 'Nome do cliente', // O Sacado é o cliente, preste atenção nos campos abaixo
    'sacado_tipo' => 'cnpj', //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
    'sacado_cnpj' => '21.222.333.4444-55',
    'sacado_logradouro' => 'Logradouro do cliente',
    'sacado_bairro' => 'Bairro do cliente',
    'sacado_cep' => '00000-111',
    'sacado_cidade' => 'Cidade do cliente',
    'sacado_uf' => 'BA',
    'data_vencimento' => new \DateTime('2015-02-03'),
    'data_cadastro' => new \DateTime('2015-01-14'),
    'juros_de_um_dia' => 0.10, // Valor do juros de 1 dia'
    'data_desconto' => new \DateTime('2015-02-09'),
    'valor_desconto' => 10.0, // Valor do desconto
    'prazo' => 10, // prazo de dias para o cliente pagar após o vencimento
    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
    'mensagem' => 'Descrição do boleto',
    'data_multa' => new \DateTime('2015-02-07'), // data da multa
    'valor_multa' => 11.2, // valor da multa
    'baixar_apos_dias' => 30,
    'dias_iniciar_contagem_juros' => 1,
));

$arquivo->insertDetalhe(array(
    'codigo_ocorrencia' => 3, // 1 = Entrada de título, futuramente poderemos ter uma constante
    'nosso_numero' => '12347',
    'numero_documento' => '12345678',
    'carteira' => '11',
    'codigo_carteira' => \Cnab\CodigoCarteira::COBRANCA_SIMPLES,
    'especie' => \Cnab\Especie::BB_DUPLICATA_MERCANTIL, // Você pode consultar as especies Cnab\Especie::CEF_OUTROS, futuramente poderemos ter uma tabela na documentação
    'aceite' => 'N', // "S" ou "N"
    'registrado' => true,
    'valor' => 200.00, // Valor do boleto
    'instrucao1' => '', // 1 = Protestar com (Prazo) dias, 2 = Devolver após (Prazo) dias, futuramente poderemos ter uma constante
    'instrucao2' => '', // preenchido com zeros
    'sacado_razao_social' => 'Nome do cliente', // O Sacado é o cliente, preste atenção nos campos abaixo
    'sacado_tipo' => 'cnpj', //campo fixo, escreva 'cpf' (sim as letras cpf) se for pessoa fisica, cnpj se for pessoa juridica
    'sacado_cnpj' => '21.222.333.4444-84',
    'sacado_logradouro' => 'Logradouro do cliente',
    'sacado_bairro' => 'Bairro do cliente',
    'sacado_cep' => '00000-111',
    'sacado_cidade' => 'Cidade do cliente',
    'sacado_uf' => 'BA',
    'data_vencimento' => new \DateTime('2015-02-03'),
    'data_cadastro' => new \DateTime('2015-01-14'),
    'juros_de_um_dia' => 0.10, // Valor do juros de 1 dia'
    'data_desconto' => new \DateTime('2015-02-09'),
    'valor_desconto' => 10.0, // Valor do desconto
    'prazo' => 10, // prazo de dias para o cliente pagar após o vencimento
    'taxa_de_permanencia' => '0', //00 = Acata Comissão por Dia (recomendável), 51 Acata Condições de Cadastramento na CAIXA
    'mensagem' => 'Descrição do boleto',
    'data_multa' => new \DateTime('2015-02-07'), // data da multa
    'valor_multa' => 11.2, // valor da multa
    'baixar_apos_dias' => 30,
    'dias_iniciar_contagem_juros' => 1,
));

// para salvar
$arquivo->save('bb001.txt');
echo "arquivo gerado banco do brasil";
