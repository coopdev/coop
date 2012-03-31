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

    public function newAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        $form = new Application_Form_Contract();
        $form->setAction($coopSess->baseUrl.'/contract/create');
        $this->view->form = $form;
        if ($this->getRequest()->isGet()) {
           $data = $this->getRequest()->getParams();
           if ($this->getRequest()->getParam('invalid')) {
              
              $data = $this->getRequest()->getParams();
              $form->isValid($data);
              
              // Form automatically gets repopulated if invalid so don't  need
              // below line to populate the fom. 
              $form->populate($data);
           } else if (isset($data['agreement']) && $data['agreement'] == 'disagree') {
              $this->view->message = 'Must agree before continuing';
              $form->populate($data);
           }
        }

    }

    public function renewAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        $uuid = $coopSess->uhinfo['uhuuid'];
        
        $link = My_DbLink::connect();
        //$link = $link->getLink();
        $sel = $link->select();
        
        $qry = $sel->from('coop_users',array('fname','lname'))
                   ->where('uuid = '.$uuid);
        $stmt = $qry->query();
        $result = $stmt->fetch();
        $form = new Application_Form_Contract();
                
        $form->setAction($coopSess->baseUrl.'/contract/create');
        $form->populate($result);
        $this->view->form = $form;
        
        if ($this->getRequest()->isGet()) {
           $data = $this->getRequest()->getParams();
           if ($this->getRequest()->getParam('invalid')) {
              
              $data = $this->getRequest()->getParams();
              $form->isValid($data);
              
             // Form automatically gets repopulated if invalid so don't need
             // below line to populate the form. 
             //$form->populate($data);
           } else if (isset($data['agreement']) && $data['agreement'] == 'disagree') {
              $this->view->message = 'Must agree before continuing';
              $form->populate($data);
           }
        }
              
    }
    
    public function createAction()
    {
//       if ($this->_request->isGet()) {
//          $data = $this->getRequest()->getParams();
//          
//          $fname = $data['fname'];
//          $lname = $data['lname'];
//          //die(var_dump($fname,$lname));
//          //$agree = $data['agreement'];
//          $user = new Application_Model_DbTable_User();
//          $user->addUser($fname,$lname);
//          
//          $this->_helper->redirector('home','pages');
//       }
       
       $form = new Application_Form_Contract();
       $coopSess = new Zend_Session_Namespace('coop');
       if ($this->getRequest()->isPost()) {
          //die('helo');
          $data = $this->getRequest()->getPost();
          //$data['fname'] = addslashes($data['fname']);
          //die(var_dump($data));
          if ($form->isValid($data)) {
             die('hi');
             // If user did not click agree
             if ($data['agreement'] != 'agree') {
                $this->_helper->redirector($coopSess->prevAction,null,null,$data);
             }
             
             $user = new Application_Model_DbTable_User();
             $link = My_DbLink::connect();
             //$link = $link->getLink();
             $fname = $data['fname'];
             $lname = $data['lname'];
             $sem = $data['semester'];
             //die(var_dump($coopSess->inDb));
             
             /*
              * IF POST CAME FROM A NEW CONTRACT
              */
             if ($coopSess->prevAction == 'new' && $coopSess->inDb == false) {
                                         
               //die('hi');
               // Must also add users uuid and find another way to assign a role
               // to an inserted user. Right now, any one filling out the new
               // contract gets a role of 'normal'.
               $user->addUser($fname, $lname, 4, $coopSess->uhinfo['uhuuid']);
               $coopSess->contractStatus = 'contractYes';
               $coopSess->role = 'normal';
               $coopSess->inDb = true;
               
               
               /*
                * IF SOMEONE IS RENEWING CONTRACT
                */                       
             } else if ($coopSess->prevAction == 'renew' && $coopSess->inDb == true) {
                $user->update(array('fname'=>$fname,
                                      'lname'=>$lname,
                                      'agreedto_contract'=>1),
                                      'uuid = '.$coopSess->uhinfo['uhuuid']);
                $coopSess->contractStatus = 'contractYes';
             }
             // Get id of user just inserted
             $result = $link->query('SELECT id FROM coop_users WHERE uuid = '
                        .$coopSess->uhinfo['uhuuid']);
             $result = $result->fetch();
             $id = $result['id'];
             //die($id);
             $this->_helper->redirector('home','pages');
          } else {
            
             $data['invalid'] = true;
             $this->_helper->redirector($coopSess->prevAction,null,null,$data);
          }
       }
       
    }


}





