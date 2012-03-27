<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function createAction()
    {
       /*
        * Requests covered on page 74 of zend book
        */
       //$form = new Application_Form_Contract(); 
       //if ($this->getRequest()->isPost()) {
       //    $formData = $this->getRequest()->getPost();
       //    if ($form->isValid($formData)) {
       //       die('valid');
       //    } else {
       //       $form->populate($formData);
       //    }
       //}
       //$data = $this->_request->getQuery('fname');
       
       if ($this->_request->isGet()) {
          $data = $this->getRequest()->getParams();
          
          $fname = $data['fname'];
          $lname = $data['lname'];
          //die(var_dump($fname,$lname));
          //$agree = $data['agreement'];
          $user = new Application_Model_DbTable_User();
          $user->addUser($fname,$lname);
          
          $this->_helper->redirector('home','pages');
       }
       
    }

    public function updateAction()
    {
        // action body
    }
    
    public function testAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $link = My_DbLink::connect();
       //$statement = $link->query('SELECT * from coop_roles');
       $select = $link->select();
       
       //$statement = $select->from('coop_users_contracts');
       $statement = $select->from('coop_roles');
       $rows = $link->fetchAll($statement);
       //die(var_dump($rows));
                           
       
//       $paginator = Zend_Paginator::factory($statement);
//       $currentPage = 1;
//       $i = $this->getRequest()->getQuery('i');
//       
//       if (!empty($i)) {
//          $currentPage = $i;
//       }
//       
//       $paginator->setItemCountPerPage(1);
//       $paginator->setPageRange(2);
//       $paginator->setCurrentPageNumber($currentPage);
//       
//       
//       $this->view->paginator = $paginator;
       
       $coopSess = new Zend_Session_Namespace('coop');
       $form = new Application_Form_Contract();
       $form->setAction('testform');
       $this->view->form = $form;
       
       
       
       /* Testing performance for class instantiation and db queries. */
//       for ($i = 0; $i < 10; $i++) {
//          $link = My_DbLink::connect();
//          $users = $link->fetchAll('SELECT * FROM coop_users');
//          //$a = 'hi';
//       }
    }
    
    public function testformAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $form = new Application_Form_Contract();
       
       if ($this->getRequest()->isPost()) {
          if ($form->isValid($_POST)) {
             
          } else {
             $this->view->form = $form;
          }
       }
    }
    
    /*
     * This action is used for testing
     */
    public function semesterAction()
    {
//      $curDate = date('Y-m-d');
//      $dateParts = explode('-',$curDate);
//      $curYear = $dateParts[0];
//      $curMonth = $dateParts[1];
//      $curSem = '';
//      
//      if ($curMonth < 7) {
//         $curSem = 'Spring';
//      } else {
//         $curSem = 'Fall';
//      }
//      
//      $curSem .= ' ' . $curYear;
//      die($curSem);
       
        $semester = new My_Semester();
        $curSem = $semester->getCurrentSem();
        die($curSem);
    }


}





