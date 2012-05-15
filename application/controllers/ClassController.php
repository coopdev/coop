<?php

class ClassController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function changeAction()
    {
       if ($this->getRequest()->isGet()) {
          $coopSess = new Zend_Session_Namespace('coop');
          $id = $this->getRequest()->getParam('classId');

          if (in_array($id, $coopSess->classIds)) {
             $coopSess->currentClassId = $id;
             $class = new My_Model_Class();
             $coopSess->currentClassName = $class->getName($id);
          }

          $action = $coopSess->prevAction;
          $controller = $coopSess->prevController;

          //die($coopSess->currentClassId);
          $this->_helper->redirector($action, $controller);
       }
    }


}



