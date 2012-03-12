<?php

class PersonController extends Zend_Controller_Action
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
          $person = new Application_Model_DbTable_Person();
          $person->addPerson($fname,$lname);
          
          $this->_helper->redirector('home','pages');
       }
       
    }

    public function updateAction()
    {
        // action body
    }
    
    public function testAction()
    {
       $link = My_DbLink::connect();
       $data = array('fname'=>'chris','lname'=>'paul');
       //$link->query('INSERT INTO coop_persons (fname,lname) values(?,?)',array('chris','paul'));
       $link->insert('coop_persons',$data);
       //die('insert');
       $quoted = $link->quote(10);
       //die("$quoted");
       $link->insert('coop_persons',array('fname'=>'chris','lname'=>'paul'));
       $id = $link->lastInsertId();
       die($id);
    }


}





