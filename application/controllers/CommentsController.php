<?php

class CommentsController extends Zend_Controller_Action
{

   private $session;
   

   public function init()
   {
      $this->session = new Zend_Session_Namespace('coop');
   }

   public function newAction()
   {
      $searchForm = new Application_Form_StudentRecSearch();
      $searchForm->setDecorators( array('ViewHelper', 'Errors'));
      $searchForm->removeElement('semesters_id');
      $searchForm->removeElement('classes_id');
      $searchForm->removeElement('coordinator');
      $this->view->searchForm = $searchForm;

      $commentForm = new Application_Form_Comment();
      $this->view->commentForm = $commentForm;

      if ($this->getRequest()->isPost()) {
         $data = $_POST;
         //die(var_dump($data));

         if ($commentForm->isValid($data)) {
            $Comment = new My_Model_Comment();
            $data['coordinator'] = $this->session->username;
            $Comment->create($data);
         }

      }

   }



}

