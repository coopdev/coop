<?php

/*
 * Controller for the agreement forms students must fill out
 */
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
        
        $formHandler = new My_InvalidFormHandler();
        $formHandler->handle($form);
        $formHandler->chkAgreement($form, $this->view);
        //$this->handleInvalidForm($form);
    }

    public function renewAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        $uuid = $coopSess->uhinfo['uhuuid'];
        
        $link = My_DbLink::connect();
        
        $sel = $link->select();
        
        $qry = $sel->from('coop_users',array('fname','lname'))
                   ->where('uuid = '.$uuid);
        $stmt = $qry->query();
        $result = $stmt->fetch();
        $form = new Application_Form_Contract();
                
        $form->setAction($coopSess->baseUrl.'/contract/create');
        $form->populate($result);
        $this->view->form = $form;
        
        $formHandler = new My_InvalidFormHandler();
        $formHandler->handle($form);
        $formHandler->chkAgreement($form, $this->view);
        //$this->handleInvalidForm($form);
           
              
    }
    
    public function createAction()
    {
       
       $form = new Application_Form_Contract();
       $coopSess = new Zend_Session_Namespace('coop');
       if ($this->getRequest()->isPost()) {
          
          $data = $this->getRequest()->getPost();
                              
          if ($form->isValid($data)) {
             
             // If user did not click agree
             if ($data['agreement'] != 'agree') {
                // Setting $coopSess->invalidData indicates the form is invalid
                // or the user clicked disagree.
                $coopSess->invalidData = $data;
                $this->_helper->redirector($coopSess->prevAction);
             }
             
             $user = new Application_Model_DbTable_User();
             $link = My_DbLink::connect();
             
             // Set values
             $fname = $data['fname'];
             $lname = $data['lname'];
             $uuid = $coopSess->uhinfo['uhuuid'];
             $sem = $data['semester'];
                          
             /*
              * IF POST CAME FROM A NEW CONTRACT
              */
             if ($coopSess->prevAction == 'new' && $coopSess->inDb == false) {
                             
               // Find a better way to assign the user's role.
               // When inserting a date, make sure to use the STR_TO_DATE mysql
               // function to convert to the proper format.
               $link->query("INSERT INTO coop_users (fname,lname,roles_id,uuid,
                             agreedto_contract) VALUES ($fname,$lname,4,$uuid)");
               //$user->addUser($fname, $lname, 4, $coopSess->uhinfo['uhuuid']);
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
                                    "uuid = $uuid");
                $coopSess->contractStatus = 'contractYes';
             }
             // Get id of user just inserted or updated
             $id = $link->fetchOne("SELECT id FROM coop_users 
                                    WHERE uuid = $uuid");
             
             // Insert contract name into coop_contracts and insert IDs into
             // the join table.
             
             //die($id);
             $this->_helper->redirector('home','pages');
          } else {
                        
             $data['invalid'] = true;
             $coopSess->invalidData = $data;
             $this->_helper->redirector($coopSess->prevAction);
             
          }
       }
       
    }
    
    
    private function handleInvalidForm($form)
    {  
       
       $coopSess = new Zend_Session_Namespace('coop');
       
       // If form was invalid or user clicked disagree, $coopSess->invalidData
       // will be set.
       if (isset($coopSess->invalidData)) {
           //die('hi');
           $data = $coopSess->invalidData;
           if (isset($data['invalid'])) {
              
              $form->isValid($data);
              
              // If the above line is true, it seems to populate the form
              // and provide the errors automatically.
                                          
           } else if (isset($data['agreement']) && $data['agreement'] == 'disagree') {
              $form->populate($data);
              $this->view->message = 'Must agree before continuing';
           }
           //$form->populate($data);
           unset($coopSess->invalidData);
        }    
    }


}





