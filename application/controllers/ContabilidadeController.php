<?php
class ContabilidadeController extends Zend_Controller_Action
{
    function init(){
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();         
    }
    public function indexAction()
    {
       $this->view->title = "Contabilidade";
       $this->render();
    }
    public function addAction(){
        $this->view->title = "addAction";
        $this->render();
    }
    public function editAction(){
        $this->view->title = "editAction";
        $this->render();
    }
    public function deleteAction(){
        $this->view->title = "deleteAction";
        $this->render();
    }


}