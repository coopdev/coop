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
       $form->setIsArray(true);
       
       $subf1 = $form->getSubForm('subf1');
       //$subf2 = $form->getSubForm('subf2');
       $coopSess = new Zend_Session_Namespace('coop');
       $form->setAction($coopSess->baseUrl.'/form/student-info-show');
       
       $this->view->form = $form;
       
       if ($this->_request->isPost()) {
          //$data = $form->getValues();
          $data = $_POST;
          //$subf1Data = $data['subf1'];
          //$subf2Data = $data['subf2'];
          //die(var_dump($data));

          //die(var_dump($form->isErrors()));

          if ($form->isValid($data)) {
             $coopSess->validData = $data;
             $this->_helper->redirector('student-info-submit');
          }  

          $subf1 = $form->getSubForm('subf1');
          //$subf1->

          //$subf1->populate($data);

          $errors = $form->getMessages();
          //$errors = $errors['subf1'];
          //$form->setErrors($errors);

          //die(var_dump($errors));
          //$form->populate($data);
          
          //$valid = $this->handlePost($form, $data);
          //if ($valid) {
          //   $this->_helper->redirector('create');
          //}
       }
       

    }

    // Updates the database with the user information
    public function studentInfoSubmitAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       if ( isset($coopSess->validData) ) {
          
          $data = $coopSess->validData;
          $subf1 = $data['subf1'];
          $subf2 = $data['subf2'];
          $data = $subf1 + $subf2;

          unset($coopSess->validData);
          
          // create student //
          //$link = My_DbLink::connect();
          $db = new My_Db();

          //die(var_dump($data));
          
          // get only the submited form data that matches table fields in coop_users
          $userVals = $db->prepFormInserts($data, 'coop_users'); 
          $userVals['username'] = $coopSess->username;

          $addrVals = $db->prepFormInserts($data, 'coop_addresses');
          $addrVals['username'] = $coopSess->username;
          $addrVals['date_mod'] = date('Ymdhis');

          $empVals = $db->prepFormInserts($data, 'coop_employmentinformation');
          $empVals['username'] = $coopSess->username;

          $homePhoneVals = $db->prepFormInserts($data, 'coop_phonenumbers');
          //die(var_dump($data));
          $homePhoneVals['phonenumber'] = $data['phone'];
          // Bottom line isn't returning a value at the  moment
          $homePhoneVals['phonetypes_id'] = $db->getId('coop_phonetypes', array('type' => 'home'));
          $homePhoneVals['username'] = $coopSess->username;
          $homePhoneVals['date_mod'] = date('Ymdhis');

          $mobilePhoneVals = $db->prepFormInserts($data, 'coop_phonenumbers');
          $mobilePhoneVals['phonenumber'] = $data['mobile'];
          // Bottom line isn't returning a value at the  moment
          $mobilePhoneVals['phonetypes_id'] = $db->getId('coop_phonetypes', array('type' => 'mobile'));
          $mobilePhoneVals['username'] = $coopSess->username;
          $mobilePhoneVals['date_mod'] = date('Ymdhis');

          $stuVals = $db->prepFormInserts($data, 'coop_students');
          $stuVals['username'] = $coopSess->username;
          //die(var_dump($stuVals));

          // get only the submited form data that matches table fields in coop_users_semesters
          //$userSemVals = $db->prepFormInserts($data, 'coop_users_semesters'); 

          $arr = array($userVals, $empVals, $addrVals, $homePhoneVals, $mobilePhoneVals, $stuVals, $userSemVals);
          foreach ($arr as $a) {
             echo var_dump($a) . "<br /><br />";
          }

          // get role for student
          //$result = $db->select()->from('coop_roles','id')->where("role = ?", "user");
          //$roleId = $db->fetchOne($result);
          //$userVals['roles_id'] = $roleId;

          // put dates into proper format for database.
          $tokens = explode('/',$stuVals['grad_date']);
          $stuVals['grad_date'] = $tokens[2] . $tokens[0] . $tokens[1];

          $tokens = explode('/',$empVals['start_date']);
          $empVals['start_date'] = $tokens[2] . $tokens[0] . $tokens[1];

          $tokens = explode('/',$empVals['end_date']);
          $empVals['end_date'] = $tokens[2] . $tokens[0] . $tokens[1];

          //die(var_dump($userVals));
          $db->update('coop_users', $userVals, array('username' => $coopSess->username));

          $db->insert('coop_addresses', $addrVals);

          $db->insert('coop_employmentinformation', $empVals);

          $db->update('coop_students', $stuVals, array('username' => $coopSess->username));

          $db->insert('coop_phonenumbers', $homePhoneVals);

          $db->insert('coop_phonenumbers', $mobilePhoneVals);

          // get id of user just inserted
          //$userSemVals['users_id'] = $db->lastInsertId('coop_users');

          //$db->insert('coop_users_semesters', $userSemVals);

          //$this->_helper->redirector('post-cas', 'auth');
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





