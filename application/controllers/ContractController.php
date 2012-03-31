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
        
        $this->handleInvalidForm($form);
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
        
        $this->handleInvalidForm($form);
           
              
    }
    
    public function createAction()
    {
       
       $form = new Application_Form_Contract();
       $coopSess = new Zend_Session_Namespace('coop');
       if ($this->getRequest()->isPost()) {
          
          $data = $this->getRequest()->getPost();
                              
          if ($form->isValid($data)) {
             die('hi');
             // If user did not click agree
             if ($data['agreement'] != 'agree') {
                // Setting $coopSess->formData indicates the form is invalid
                // or the user clicked disagree.
                $coopSess->formData = $data;
                $this->_helper->redirector($coopSess->prevAction);
             }
             
             $user = new Application_Model_DbTable_User();
             $link = My_DbLink::connect();
             
             $fname = $data['fname'];
             $lname = $data['lname'];
             $sem = $data['semester'];
                          
             /*
              * IF POST CAME FROM A NEW CONTRACT
              */
             if ($coopSess->prevAction == 'new' && $coopSess->inDb == false) {
                             
               // Use My_DbLink::connect to insert user instead of below method.
               // Also must find a better way to assign the user's role.
               $user->addUser($fname, $lname, 4, $coopSess->uhinfo['uhuuid']);
               $coopSess->contractStatus = 'contractYes';
               $coopSess->role = 'normal';
               $coopSess->inDb = true;
               
               
               /*
                * IF SOMEONE IS RENEWING CONTRACT
                */                       
             } else if ($coopSess->prevAction == 'renew' && $coopSess->inDb == true) {
                
                // Use My_DbLink::connect to update user instead of below method.
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
             $coopSess->formData = $data;
             $this->_helper->redirector($coopSess->prevAction);
             
          }
       }
       
    }
    
    
    private function handleInvalidForm($form)
    {  
       
       $coopSess = new Zend_Session_Namespace('coop');
       
       // If form was invalid or user clicked disagree, $coopSess->formData
       // will be set.
       if (isset($coopSess->formData)) {
           //die('hi');
           $data = $coopSess->formData;
           if (isset($data['invalid'])) {
              
              $form->isValid($data);
              
              // If the above line is true, it seems to populate the form
              // and provide the errors automatically .
                                          
           } else if (isset($data['agreement']) && $data['agreement'] == 'disagree') {
              $this->view->message = 'Must agree before continuing';
           }
           //$form->populate($data);
           unset($coopSess->formData);
        }    
    }


}





