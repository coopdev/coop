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


       $form = new Application_Form_StudentInfo();
       //$form->setIsArray(true);
       
       $coopSess = new Zend_Session_Namespace('coop');
       $form->setAction($coopSess->baseUrl.'/form/student-info-show');

       
       if ($this->_request->isPost()) {
          $data = $_POST;

          if ($form->isValid($data)) {
             $coopSess->validData = $data;
             $this->_helper->redirector('student-info-submit');
          }  

       } else {
          $formVals = array();
          $db = new My_Db();
          $query = $db->select()->from('coop_users', 
                                          array('fname', 'lname', 'uuid', 'email'))
                                   ->where("username = '" . $coopSess->username . "'");
          $userVals = $db->fetchRow($query);

          $query = $db->select()->from('coop_addresses', 
                                          array('address', 'city', 'state', 'zipcode'))
                                   ->where("username = '" . $coopSess->username . "'");
          $addrVals = $db->fetchRow($query);
          if (!is_array($addrVals)) {
             $addrVals = array();
          }

          $query = $db->select()->from('coop_employmentinfo', 
                                          array('current_job', 'start_date', 'end_date', 'rate_of_pay', 'job_address'))
                                   ->where("username = '" . $coopSess->username . "'");
          $empVals = $db->fetchRow($query);
          if (!is_array($empVals)) {
             $empVals = array();
          }

          $query = $db->select()->from('coop_phonenumbers', 
                                       array('phonenumber'))
                                ->join('coop_phonetypes', "coop_phonenumbers.phonetypes_id = coop_phonetypes.id", array())
                                ->where("username = '" . $coopSess->username . "'")
                                ->where("coop_phonetypes.type = 'home'");

          if ($homePhoneVals = $db->fetchRow($query)) {
             $homePhoneVals['phone'] = $homePhoneVals['phonenumber'];
          } else {
             $homePhoneVals = array();
          }

          //die(var_dump($homePhoneVals));

          $query = $db->select()->from('coop_phonenumbers', 
                                       array('phonenumber'))
                                ->join('coop_phonetypes', "coop_phonenumbers.phonetypes_id = coop_phonetypes.id", array())
                                ->where("username = '" . $coopSess->username . "'")
                                ->where("coop_phonetypes.type = 'mobile'");

          if ($mobilePhoneVals = $db->fetchRow($query)) {
             $mobilePhoneVals['mobile'] = $mobilePhoneVals['phonenumber'];
          } else {
             $mobilePhoneVals = array();
          }

          //die(var_dump($userVals, $addrVals, $empVals, $homePhoneVals, $mobilePhoneVals));

          $formVals = $userVals + $addrVals + $empVals + $homePhoneVals + $mobilePhoneVals;

          if (!empty($formVals['start_date'])) {
             $dateTokens = explode("-", $formVals['start_date']);
             $temp = $dateTokens[0];
             $dateTokens[0] = $dateTokens[1];
             $dateTokens[1] = $dateTokens[2];
             $dateTokens[2] = $temp;
             $formVals['start_date'] = implode("/", $dateTokens);

          }

          
          if (!empty($formVals['end_date'])) { 
             $dateTokens = explode("-", $formVals['end_date']);
             $temp = $dateTokens[0];
             $dateTokens[0] = $dateTokens[1];
             $dateTokens[1] = $dateTokens[2];
             $dateTokens[2] = $temp;
             $formVals['end_date'] = implode("/", $dateTokens);

          }

          if (!empty($formVals['grad_date'])) { 
             $dateTokens = explode("-", $formVals['grad_date']);
             $temp = $dateTokens[0];
             $dateTokens[0] = $dateTokens[1];
             $dateTokens[1] = $dateTokens[2];
             $dateTokens[2] = $temp;
             $formVals['grad_date'] = implode("/", $dateTokens);

          }

          $form->populate($formVals);
          

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
          $subf1 = $data['subf1'];
          $subf2 = $data["empinfo"];
          $subf2 = $subf2[0];
          $data = $subf1 + $subf2;

          unset($coopSess->validData);
          
          // create student //
          //$link = My_DbLink::connect();
          $db = new My_Db();

          //die(var_dump($data));
          
          // get only the submited form data that matches table fields in coop_users
          $userVals = $db->prepFormInserts($data, 'coop_users'); 
          //die(var_dump($userVals));
          $userVals['username'] = $coopSess->username;

          // get only the submited form data that matches table fields in coop_addresses
          $addrVals = $db->prepFormInserts($data, 'coop_addresses');
          //die(var_dump($addrVals));
          $addrVals['username'] = $coopSess->username;
          $addrVals['date_mod'] = date('Ymdhis');

          // get only the submited form data that matches table fields in coop_employmentinfo
          $empVals = $db->prepFormInserts($data, 'coop_employmentinfo');
          //die(var_dump($empVals));
          $empVals['username'] = $coopSess->username;

          // get only the submited form data that matches table fields in coop_phonenumbers
          $homePhoneVals = $db->prepFormInserts($data, 'coop_phonenumbers');
          //die(var_dump($homePhoneVals));
          //die(var_dump($data));
          $homePhoneVals['phonenumber'] = $data['phone'];
          $homePhoneVals['phonetypes_id'] = $db->getId('coop_phonetypes', array('type' => 'home'));
          $homePhoneVals['username'] = $coopSess->username;
          $homePhoneVals['date_mod'] = date('Ymdhis');

          // get only the submited form data that matches table fields in coop_phonenumbers (for mobile #)
          $mobilePhoneVals = $db->prepFormInserts($data, 'coop_phonenumbers');
          $mobilePhoneVals['phonenumber'] = $data['mobile'];
          $mobilePhoneVals['phonetypes_id'] = $db->getId('coop_phonetypes', array('type' => 'mobile'));
          $mobilePhoneVals['username'] = $coopSess->username;
          $mobilePhoneVals['date_mod'] = date('Ymdhis');

          // get only the submited form data that matches table fields in coop_students
          $stuVals = $db->prepFormInserts($data, 'coop_students');
          $stuVals['username'] = $coopSess->username;

          // Put dates into proper format for database.
          $tokens = explode('/',$stuVals['grad_date']);
          $stuVals['grad_date'] = $tokens[2] . $tokens[0] . $tokens[1];

          $tokens = explode('/',$empVals['start_date']);
          $empVals['start_date'] = $tokens[2] . $tokens[0] . $tokens[1];

          $tokens = explode('/',$empVals['end_date']);
          $empVals['end_date'] = $tokens[2] . $tokens[0] . $tokens[1];

          $db->update('coop_users', $userVals, "username = '".$coopSess->username."'");

          if ($temp = $db->getId('coop_addresses', array('username' => $coopSess->username))) {
             $query = $db->update('coop_addresses', $addrVals, "username = '".$coopSess->username."'");
          } else {
             $db->insert('coop_addresses', $addrVals);
          }

          if ($temp = $db->getId('coop_employmentinfo', array('username' => $coopSess->username))) {
             $db->update('coop_employmentinfo', $empVals, "username = '" . $coopSess->username . "'");
          } else {
             $db->insert('coop_employmentinfo', $empVals);
          }

          if ($temp = $db->getId('coop_students', array('username' => $coopSess->username))) {
             $db->update('coop_students', $stuVals, "username = '" . $coopSess->username . "'");
          } else {
             $db->insert('coop_students', $stuVals);
          }

          $phoneType = $db->getId('coop_phonetypes', array('type' => 'home'));
          if ($temp = $db->getCol('coop_phonenumbers', 'id', array('username' => $coopSess->username, 'phonetypes_id' => $phoneType))) {
             $db->update('coop_phonenumbers', $homePhoneVals, array("username = '".$coopSess->username."'", "phonetypes_id = $phoneType"));
          } else {
             $db->insert('coop_phonenumbers', $homePhoneVals);
          }

          $phoneType = $db->getId('coop_phonetypes', array('type' => 'mobile'));
          if ($temp = $db->getCol('coop_phonenumbers', 'id', array('username' => $coopSess->username, 'phonetypes_id' => $phoneType))) {
             $db->update('coop_phonenumbers', $mobilePhoneVals, array("username = '".$coopSess->username."'", "phonetypes_id = $phoneType"));
          } else {
             $db->insert('coop_phonenumbers', $mobilePhoneVals);
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





