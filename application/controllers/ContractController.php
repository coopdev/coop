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
           //die('hello');
           if ($this->getRequest()->getParam('invalid')) {
              
              $data = $this->getRequest()->getParams();
              
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
        
        $qry = $sel->from('coop_persons',array('fname','lname'))
                   ->where('uuid = '.$uuid);
        $stmt = $qry->query();
        $result = $stmt->fetch();
        $form = new Application_Form_Contract();
                
        $form->setAction($coopSess->baseUrl.'/contract/create');
        $form->populate($result);
        $this->view->form = $form;
              
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
//          $person = new Application_Model_DbTable_Person();
//          $person->addPerson($fname,$lname);
//          
//          $this->_helper->redirector('home','pages');
//       }
       
       $form = new Application_Form_Contract();
       if ($this->getRequest()->isPost()) {
          //die('helo');
          $data = $this->getRequest()->getPost();
          //die(var_dump($data));
          if ($form->isValid($data)) {
             $coopSess = new Zend_Session_Namespace('coop');
             $person = new Application_Model_DbTable_Person();
             $link = My_DbLink::connect();
             //$link = $link->getLink();
             $fname = $data['fname'];
             $lname = $data['lname'];
             
             /*
              * IF POST CAME FROM A NEW CONTRACT
              */
             if ($coopSess->prevAction == 'new' && $coopSess->inDb == false) {
                                         
               //Must also add users uuid
               $person->addPerson($fname, $lname);
               $coopSess->contractStatus = 'contractYes';
               $coopSess->inDb = true;
               
               
               /*
                * IF SOMEONE IS RENEWING CONTRACT
                */                       
             } else if ($coopSess->prevAction == 'renew' && $coopSess->inDb == true) {
                $person->update(array('fname'=>$fname,
                                      'lname'=>$lname,
                                      'agreedto_contract'=>1),
                                      'uuid = '.$coopSess->uhinfo['uhuuid']);
                $coopSess->contractStatus = 'contractYes';
             }
             // Get id of person just inserted
             $result = $link->query('SELECT id FROM coop_persons WHERE uuid = '
                        .$coopSess->uhinfo['uhuuid']);
             $result = $result->fetch();
             $id = $result['id'];
             //die($id);
             $this->_helper->redirector('home','pages');
          } else {
             $data['invalid'] = true;
             $this->_helper->redirector('new',null,null,$data);
          }
       }
       
    }


}





