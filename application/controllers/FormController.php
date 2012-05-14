<?php

class FormController extends Zend_Controller_Action
{


    public function init()
    {
        /* Initialize action controller here */
    }

    // Displays the form and receives and validates the posted data. Redirects to 
    // studentInfoSubmit if data is valid
    public function studentInfoShowAction()
    {

       // User $this->view->url() to refresh page when changing classes (it return the current url)
       //die(var_dump($this->view->url()));
       $form = new Application_Form_StudentInfo();
       //$form->setIsArray(true);
       
       $coopSess = new Zend_Session_Namespace('coop');
       $form->setAction($coopSess->baseUrl.'/form/student-info-show');

       
       if ($this->_request->isPost()) {
          $data = $_POST;
          //die(var_dump($data));

          if ($form->isValid($data)) {
             //die(var_dump($data));
             $coopSess->validData = $data;
             $this->_helper->redirector('student-info-submit');
          }  

       } else {
          $assignment = new My_Model_Assignment();
          $form = $assignment->populateStuInfoSheet($form);
          //die(var_dump($form));
       }

       $this->view->form = $form;
       

    }

    // Updates the database with the user information
    public function studentInfoSubmitAction()
    {
       date_default_timezone_set('US/Hawaii');
       $coopSess = new Zend_Session_Namespace('coop');
       if ( isset($coopSess->validData) ) {
          
          $data = $coopSess->validData;
          //die(var_dump($data));
          $subf1 = $data['subf1'];
          $subf2 = $data["empinfo"];
          $subf2 = $subf2[0];
          $data = $subf1 + $subf2;

          unset($coopSess->validData);

          $assignment = new My_Model_Assignment();
          $assignment->submitStuInfoSheet($data);
          
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





