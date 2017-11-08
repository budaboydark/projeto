<?php

class Application_Model_Remessa
{
    
    public function validaDados($dadosRemessa){
        //Valida os dados da requisição.
        $formHeader = new Application_Form_Remessa();
        $count = 0;
        $parametros = array();
        foreach($dadosRemessa as $p){
            if($count == 0){
                if (!$formHeader->isValid($p)){
                    $errors = $formHeader->getMessages();
                    var_dump($errors);
                    die();
                }else{
                    $parametros['header'] = $formHeader->getValues();
                    $count++;
                }
            }else{
                foreach($p as $detail=>$detail2){
                    $formDetalhe = new Application_Form_Detalhe();
                    if (!$formDetalhe->isValid($detail2)){
                        $errors = $formDetalhe->getMessages();
                        var_dump($errors);
                        die();
                    }else{
                        $parametros['detalhe'][] = $formDetalhe->getValues();
                        $count++;
                    }
                }
            }
            $count++;
        }
        return $parametros;

    }

    public function normalizaNomeBanco($nome){
        $boleto_nome = strtolower($nome);
        $boleto_nome = implode(' ',explode('_',$boleto_nome));
        $boleto_nome = str_replace(' ', '', ucwords($boleto_nome));
        return $boleto_nome;
    }

}
