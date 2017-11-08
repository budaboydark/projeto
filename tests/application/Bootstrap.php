<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    protected function _initNamespaces() {
        require_once __DIR__ . '/../library/cnabphp/vendor/autoload.php';
    }
    protected function _initCnab(){

        Cnab\Factory::setCnabFormatPath(dirname(__FILE__).'/../data/cnab_yaml');
        
    }
}
