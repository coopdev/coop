<?php

class CommentsController extends Zend_Controller_Action
{

   private $session;
   

   public function init()
   {
      $this->session = new Zend_Session_Namespace('coop');
   }
   
   
   /*
    *  Async.
    */
   public function listAction()
   {
       $this->_helper->getHelper('layout')->disableLayout();
       //$this->_helper->viewRenderer->setNoRender();

       if ($this->getRequest()->isGet()) {
          $student = $_GET['student'];
          //die(var_dump($student));

          $Comment = new My_Model_Comment();
          $comments = $Comment->fetch(array('student' => $student));

          //die(var_dump($comments->toArray()));

          $this->view->comments = $comments;
       }
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

   public function updateAction()
   {
      $this->_helper->getHelper('layout')->disableLayout();
      $this->_helper->viewRenderer->setNoRender();

      if ($this->getRequest()->isPost()) {
         $comment = $_POST['comment'];
         //$id = $_POST['id'];
         //die(var_dump($comment));

         $Comment = new My_Model_Comment();
         $Comment->updateComment($comment);
      }
      
   }




}

