<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Assignment
 *
 * @author joseph
 */
class My_Model_Assignment extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_assignments';



   // Submits an assignment.
   public function submit(array $data)
   {
      $db = new My_Db();
      $sa = new My_Model_SubmittedAssignment();
      $sem = new My_Model_Semester();
      $data = $db->prepFormInserts($data, $sa);
      $data['semesters_id'] = $sem->getCurrentSemId();
      $data['date_submitted'] = date('Ymd');

      $chk['username'] = $data['username'];
      $chk['classes_id'] = $data['classes_id'];
      $chk['semesters_id'] = $data['semesters_id'];
      $chk['assignments_id'] = $data['assignments_id'];

      if ($this->isSubmitted($chk)) {
         return "submitted";
      }

      //$inserts['username'] = $data['username'];
      //$inserts['classes_id'] = $data['classes_id'];
      //$inserts['assignments_id'] = $data['assignments_id'];
      //$inserts['semesters_id'] = $sem->getCurrentSemId();
      //$inserts['date_submitted'] = date('Ymd');

      $sa->insert($data);

      return true;
   }

   public function edit($data)
   {
      $db = new My_Db();

      $id = $data['id'];
      unset($data['id']);

      $data = $db->prepFormInserts($data, $this);
      $funcs = new My_Funcs();
      $data['due_date'] = $funcs->formatDateIn($data['due_date']);

      $this->update($data, "id = $id");


   }

   public function getAll()
   {
      $rows = $this->fetchAll()->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;
   }


   // Returns assignments that are submitted offline
   public function getOffLine()
   {
      $rows = $this->fetchAll("online = 0")->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;
   }

   public function getAssignment($id)
   {
      $row = $this->fetchRow("id = $id")->toArray();

      if (empty($row)) {
         $row = array();
      }

      return $row;

   }

   public function getStuInfoId()
   {
      $id = $this->getId(array('assignment' => 'Student Information Sheet'));
      if (empty($id)) {
         $id = 0;
      }
      return $id;
   }

   public function getQuestions($id)
   {
      $aq = new My_Model_AssignmentQuestions();

      $rows = $aq->fetchAll("assignments_id = $id")->toArray();

      if (empty($row)) {
         $row = array();
      }

      return $rows;
      
   }


   // Checks if a specific assignment has already been submitted based on username, class,
   // semester, assignment.
   public function isSubmitted(array $data)
   {
      $sa = new My_Model_SubmittedAssignment();

      if ($sa->rowExists($data)) {
         return true;
      }
      
      return false;

   }



   /*
    * Populates a Zend_Form Student Information Sheet with either the current users 
    * information based on their username, or uses the passed in username in the $opts 
    * associative array (using the 'username' key).
    * 
    * @param $form - The Zend_Form to populate
    * @param $opts - Option to use the passed in username in the where clause. If no
    *                 username is passed, the current user's username is used.
    * @return A populated Zend_Form Student Information Sheet 
    */
   public function populateStuInfoSheet($form, array $opts = array())
   {
       $coopSess = new Zend_Session_Namespace('coop');

       // If $opts['username'] is set, then use that as the criteria for the users info.
       // $opts['username'] is used for coordinators when viewing different students' 
       // Student Info Sheet. Else, use the current user's username (used for students filling
       // out their own Student Info Sheet).
       if (isset($opts['username'])) {
          $username = $opts['username'];
       } else {
          $username = $coopSess->username;
       }
      
       $formVals = array();
       $db = new My_Db();
       $query = $db->select()->from('coop_users', 
                                       array('fname', 'lname', 'uuid', 'email'))
                                ->where("username = '" . $username . "'");
       $userVals = $db->fetchRow($query);
       if (!is_array($userVals)) {
          $userVals = array();
       }

       $query = $db->select()->from('coop_students')
                             ->where("username = '" . $username . "'");
       $stuVals = $db->fetchRow($query);
       if (!is_array($stuVals)) {
          $stuVals = array();
       }

       $query = $db->select()->from('coop_addresses', 
                                       array('address', 'city', 'state', 'zipcode'))
                                ->where("username = '" . $username . "'");
       $addrVals = $db->fetchRow($query);
       if (!is_array($addrVals)) {
          $addrVals = array();
       }

       /* Took out emp info because the student should be entering new employment info
        * each time they fill out this form.
        */

       //$query = $db->select()->from('coop_employmentinfo', 
       //                                array('current_job', 'start_date', 'end_date', 'rate_of_pay', 'job_address'))
       //                         ->where("username = '" . $username . "'");
       //$empVals = $db->fetchRow($query);
       //if (!is_array($empVals)) {
       //   $empVals = array();
       //}

       $query = $db->select()->from('coop_phonenumbers', 
                                    array('phonenumber'))
                             ->join('coop_phonetypes', "coop_phonenumbers.phonetypes_id = coop_phonetypes.id", array())
                             ->where("username = '" . $username . "'")
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
                             ->where("username = '" . $username . "'")
                             ->where("coop_phonetypes.type = 'mobile'");

       if ($mobilePhoneVals = $db->fetchRow($query)) {
          $mobilePhoneVals['mobile'] = $mobilePhoneVals['phonenumber'];
       } else {
          $mobilePhoneVals = array();
       }

       $currentClass = array();
       if ($coopSess->role === 'user') {
          $currentClass['wanted_class'] = $coopSess->currentClassName;
       }

       //die(var_dump($userVals, $addrVals, $empVals, $homePhoneVals, $mobilePhoneVals));

       //$formVals = $userVals + $addrVals + $empVals + $homePhoneVals + $mobilePhoneVals + $stuVals;
       $formVals = $userVals + $addrVals + $homePhoneVals + $mobilePhoneVals + $stuVals + $currentClass;

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

       //die(var_dump($formVals));

       $form->populate($formVals);
       return $form;
   }

   public function submitStuInfoSheet($data)
   {
       $coopSess = new Zend_Session_Namespace('coop');

       $db = new My_Db();

       //die(var_dump($data));
       
       // get only the submited form data that matches table fields in coop_users
       $userVals = $db->prepFormInserts($data, 'coop_users'); 
       //if ($userVals['uuid'] == "") {
       //   $userVals['uuid'] = null;
       //}
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
       if (empty($empVals['rate_of_pay'])) {
          $empVals['rate_of_pay'] = null;
       }
       $empVals['username'] = $coopSess->username;
       $empVals['classes_id'] = $coopSess->currentClassId;
       $empVals['semesters_id'] = $coopSess->currentSemId;

       //die(var_dump($empVals));

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

       //die(var_dump($data));
       // get only the submited form data that matches table fields in coop_students
       $stuVals = $db->prepFormInserts($data, 'coop_students');
       $stuVals['username'] = $coopSess->username;
       //die(var_dump($stuVals));

       /* PUT DATES INTO PROPER FORMAT FOR DATABASE. */

       // Set date to null if it is a blank string so that it appears as null
       // in the database.
       if ($stuVals['grad_date'] == "") {
          $stuVals['grad_date'] = null;
       } else {
          $tokens = explode('/',$stuVals['grad_date']);
          $stuVals['grad_date'] = $tokens[2] . $tokens[0] . $tokens[1];
       }

       if ($empVals['start_date'] == "") {
          $empVals['start_date'] = null;
       } else {
          $tokens = explode('/',$empVals['start_date']);
          $empVals['start_date'] = $tokens[2] . $tokens[0] . $tokens[1];
       }

       if ($empVals['end_date'] == "") {
          $empVals['end_date'] = null;
       } else {
          $tokens = explode('/',$empVals['end_date']);
          $empVals['end_date'] = $tokens[2] . $tokens[0] . $tokens[1];
       }

       $db->update('coop_users', $userVals, "username = '".$coopSess->username."'");


       if ($temp = $db->getId('coop_addresses', array('username' => $coopSess->username))) {
          $query = $db->update('coop_addresses', $addrVals, "username = '".$coopSess->username."'");
       } else {
          $db->insert('coop_addresses', $addrVals);
       }

       //if ($temp = $db->getId('coop_employmentinfo', array('username' => $coopSess->username))) {
       //   $db->update('coop_employmentinfo', $empVals, "username = '" . $coopSess->username . "'");
       //} else {
          $db->insert('coop_employmentinfo', $empVals);
       //}

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

       // Submit as an assignment
       $semester = new My_Model_Semester();
       $assignVals['semesters_id'] = $coopSess->currentSemId;
       $assignVals['classes_id'] = $coopSess->currentClassId;
       $assignVals['username'] = $coopSess->username;
       $assignVals['assignments_id'] = $this->getStuInfoId();
       $assignVals['date_submitted'] = date('Ymd');

       //die(var_dump($coopSess->currentClassId));
       //die(var_dump($assignVals));

       $subAs = new My_Model_SubmittedAssignment();
       // First check if the assignment has already been submitted
       if (!$subAs->isSubmitted($assignVals)) {
          $subAs->insert($assignVals);
       }

   }











   public function getRow(array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
      $val = $where[$col];
      $query = $this->select()->where("$col = ?", $val);
      $row = $this->fetchRow($query);
      $row = $row->toArray();
      return $row;
   }

   public function getRowById($id)
   {
      $row = $this->fetchRow("id = $id");
      $row = $row->toArray();
      return $row;
   }

   public function getRows(array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
     // die($col);
      $val = $where[$col];
      $result = $this->select()->where("$col = ?", $val);
      $rows = $this->fetchAll($result);
      $rows = $rows->toArray();
      return $rows;
   }

   public function getId(array $where)
   {
      //$keys = array_keys($where);
      //$col = $keys[0];
      //// die($col);
      //$val = $where[$col];
      $query = $this->select()->from($this, array('id'));//->where("$col = ?", $val);
      foreach ($where as $key => $val) {

         $query = $query->where("$key = ?", $val);

      }
      $row = $this->fetchRow($query);
      $row = $row->toArray();
      return $row['id'];
   }

   public function getCol($col, array $where)
   {
      $query = $this->select()->from($this, array($col));
      foreach ($where as $key => $val) {

         $query = $query->where("$key = ?", $val);

      }
      $row = $this->fetchRow($query);
      $row = $row->toArray();
      return $row["$col"];

   }

   public function getCols($col, array $where=array())
   {
      $query = $this->select()->from($this, $col);
      foreach ($where as $key => $val) {

         $query = $query->where("$key = ?", $val);

      }
      $result = $this->fetchAll($query);
      $rows = $result->toArray();

      foreach ($rows as $r) {
         $vals[] = $r[$col];
      }
      die(var_dump($vals));

      return $vals;
   }

   public function rowExists(array $where)
   {
      $query = $this->select();//->where("$whereCol = ?", $whereVal);
      foreach ($where as $key => $val) {

         $query = $query->where("$key = ?", $val);

      }
      $row = $this->fetchRow($query);

      if (empty($row)) {
         return false;
      }

      return true;
   }

}

?>
