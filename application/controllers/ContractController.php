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

    public function newAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        $form = new Application_Form_Contract();
        $form->setAction($coopSess->baseUrl.'/contract/new');
        
        $this->view->form = $form;
        
        if ($this->_request->isPost()) {
           $data = $_POST;

           if ($form->isValid($data)) {
              $coopSess->validData = $data;
              $this->_helper->redirector('create');
           }
           //$valid = $this->handlePost($form, $data);
           //if ($valid) {
           //  $this->_helper->redirector('create');
          //}
        }
    }

    public function createAction()
    {
       
       $form = new Application_Form_Contract();
       $coopSess = new Zend_Session_Namespace('coop');
       if ( isset($coopSess->validData) ) {
          
          $data = $coopSess->validData;
          unset($coopSess->validData);
                       
          $db = new My_Db();

          $userVals = $db->prepFormInserts($data, 'coop_users');

          $userSemVals = $db->prepFormInserts($data, 'coop_users_semesters');

          $db->update('coop_users', $userVals, 'id = '.$coopSess->userId);

          // Going to need different 'where' clauses for update query. One for when students submit form, 
          // and one for supervisors.
          // 
          //$db->update('coop_users_semesters', $userSemVals, )


          //die($id);
          $this->_helper->redirector('home','pages');
           
       }
       
    }

    // Not being used
    public function renewAction()
    {
        /* IMPORTANT: WHEN POPULATING THE FORM DATA WITH DATA FROM DATABASE, THE
         * FORM ELEMENT NAMES MUST MATCH THE TABLE FIELD NAMES.
         */
        $coopSess = new Zend_Session_Namespace('coop');
        $username = $coopSess->uhinfo['user'];
        
        $link = My_DbLink::connect();
        
        $sel = $link->select();
        
        $qry = $sel->from('coop_users',array('fname','lname'))
                   ->where('username = ?',$username);
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
    
    
    
    /* HELPERS */
    

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





