<?php

class ContractController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function showAction()
    {
        $form = new Application_Form_Contract();
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
           $data = $this->getRequest()->getPost();
           if ($form->isValid($data)) {
              
           } else {
                $form->populate($data);
           }
        
        }
    }


}



