<?php

class ClassController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    // Used when a student is enrolled in multiple classes and needs to switch between them
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
          $url = $coopSess->prevUrl;

          //die($coopSess->currentClassId);

          // This won't work right if the previous action, controller was one which was
          // meant for async posts
          $this->_helper->redirector($action, $controller);
       }
    }


    public function listallAction()
    {
       $class = new My_Model_Class();
       $classes = $class->getAll();

       $this->view->classes = $classes;

    }

    public function editAction()
    {
       if ($this->getRequest()->isGet() && isset($_GET['id'])) {

          $id = $_GET['id'];

          $class = new My_Model_Class();
          $classRec = $class->getClass($id);

          //$data = array('name' => $classRec['name'], 'coordinator' => $classRec['coordinator']);

          $form = new Application_Form_ClassEdit();

          $form->populate($classRec);

          $this->view->form = $form;

       } else if ($this->getRequest()->isPost()) {

          $data = $_POST;

          $class = new My_Model_Class();
          $class->edit($data['id'], $data);

          $this->_helper->redirector('listall');

       }

    }



}



