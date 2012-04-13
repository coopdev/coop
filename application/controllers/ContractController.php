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
        $form->setAction($coopSess->baseUrl.'/contract/new');
        
        $this->view->form = $form;
        
        if ($this->_request->isPost()) {
           $data = $_POST;
           $valid = $this->handlePost($form, $data);
           if ($valid) {
             $this->_helper->redirector('create');
          }
        }
    }

    public function renewAction()
    {
        /* IMPORTANT: WHEN POPULATING THE FORM DATA WITH DATA FROM DATABASE, THE
         * FORM ELEMENT NAMES MUST MATCH THE TABLE FIELD NAMES.
         */
        $coopSess = new Zend_Session_Namespace('coop');
        $uuid = $coopSess->uhinfo['uhuuid'];
        
        $link = My_DbLink::connect();
        
        $sel = $link->select();
        
        $qry = $sel->from('coop_users',array('fname','lname'))
                   ->where('uuid = '.$uuid);
        //$stmt = $qry->query();
        $result = $link->fetchRow($qry);
        $form = new Application_Form_Contract();
                
        $form->setAction($coopSess->baseUrl.'/contract/renew');
        $form->populate($result);
        $this->view->form = $form;
        
        if ($this->_request->isPost()) {
           $data = $_POST;
           $valid = $this->handlePost($form, $data);
           if ($valid) {
             $this->_helper->redirector('create');
          }
        }
           
              
    }
    
    public function createAction()
    {
       
       $form = new Application_Form_Contract();
       $coopSess = new Zend_Session_Namespace('coop');
       if ( isset($coopSess->validData) ) {
          
          $data = $coopSess->validData;
          unset($coopSess->validData);
                       
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
          $coopSess->role = 'user';
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
           
       }
       
    }
    
    
    private function handlePost($form, $data)
    {
       $coopSess = new Zend_Session_Namespace('coop');
       if ($form->isValid($data)) {
          if ($data['agreement'] == 'agree') {
             $coopSess->validData = $data;
             return true;
          } else {
             $this->view->message = 'Must agree before continuing';
             $form->populate($data);
             return false;
          }
       } else {
          return false;
       }
    }


}





