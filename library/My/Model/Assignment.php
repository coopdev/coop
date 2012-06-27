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

   /*
    * Assignment numbers for each assignment (Used to identify an assignment 
    * rather than using the name, since the name can change):
    * 
    * Student info sheet = 1,
    * Midterm report = 2,
    * Coop Agreement = 3,
    * Learning Outcome = 4,
    * Student eval = 5,
    */



   /* Submits an assignment.
    * 
    * @param $data - assoc array containing: username, classes_id, assignments_id
    */
   public function submit(array $data)
   {
      date_default_timezone_set('US/Hawaii');
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

      //die(var_dump($chk));

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

   public function populateMidTermReport($form, $data)
   {
      unset($data['coordinator']);

      $aa = new My_Model_AssignmentAnswers();

      $answers = $aa->getRows($data);

      //die(var_dump($answers));

      $formVals = array();

      foreach ($answers as $a) {
         $formVals[$a['assignmentquestions_id']] = $a['answer_text'];
      }

      $form->populate($formVals);

      return $form;

      //die(var_dump($formVals));

   }

   public function submitMidtermReport($data)
   {
      $coopSess = new Zend_Session_Namespace('coop');

      $submit['username'] = $coopSess->username;
      $submit['classes_id'] = $coopSess->currentClassId;
      $submit['assignments_id'] = $this->getMidtermId();

      // Submit assignment into coop_submittedassignments
      $res = $this->submit($submit);

      // If already submitted.
      if ($res === 'submitted') {
         return "submitted";
      }

      // current semester
      $submit['semesters_id'] = $coopSess->currentSemId;

      $aa = new My_Model_AssignmentAnswers();

      foreach ($data as $key => $val) {
         $submit['assignmentquestions_id'] = $key;
         $submit['answer_text'] = $val;

         try {
            // insert into coop_assignmentanswers
            $aa->insert($submit);
         } catch(Exception $e) {
            return false;
         }

      }

      return true;

      //die(var_dump($submit));
   }

   /*
    * @param $form Zend_Form - The form to populate
    * 
    * @param - optional parameter may be passed as the criteria to populate the form (use func_get_arg()).
    *          if no optional parameter is passed, use session data as the criteria.
    * 
    * @return The populated form.
    */
   public function populateLearningOutcome($form)
   {
      $coopSess = new Zend_Session_Namespace('coop');

      // get number of arguments passed to this method
      $argNum = func_num_args();
      // if the second optional parameter was passed
      if ($argNum > 1) {
         // use the second parameter is the "where" criteria
         $where = func_get_arg(1);
         unset($where['coordinator']);
         if (!is_array($where)) {
            $where = array();
         }
      } else {
         $where['username'] = $coopSess->username;
         $where['classes_id'] = $coopSess->currentClassId;
         $where['semesters_id'] = $coopSess->currentSemId;
         $where['assignments_id'] = $this->getLearningOutcomeId();
      }


      $aa = new My_Model_AssignmentAnswers();

      $report = $aa->getRows($where);
      if (!empty($report)) {
         $report = $report[0];
         $form->populate(array('report' => $report['answer_text']));
      }

      //die(var_dump($report));


      return $form;
   }

   public function populateStudentEval(Zend_Form $form, $data)
   {
      unset($data['coordinator']);

      $aa = new My_Model_AssignmentAnswers();

      $aaSel = $aa->select();
      foreach ($data as $key => $val) {
         if ($key === 'username') {
            $aaSel->where("$key = '$val'");
         } else {
            $aaSel->where("$key = $val");
         }
      }

      $rows = $aa->fetchAll($aaSel)->toArray(); 

      $answers = array();
      foreach ($rows as $r) {
         // assignmentquestions_id is required to populate the form, since the form uses the question id as it's name.
         $aqid = $r['assignmentquestions_id']; 
         $atext = $r['answer_text'];
         $answers[$aqid] = $atext;
      }

      $form->populate($answers);

      return $form;
      //return $rows;
   }

   public function submitLearningOutcome($data)
   {
      date_default_timezone_set('US/Hawaii');
      $isFinal = false;
      if (isset($data['Submit'])) {
         $isFinal = true;
      }


      $coopSess = new Zend_Session_Namespace('coop');

      $sub['username'] = $coopSess->username;
      $sub['classes_id'] = $coopSess->currentClassId;
      $sub['semesters_id'] = $coopSess->currentSemId;
      $sub['assignments_id'] = $this->getLearningOutcomeId();
      //$sub['is_final'] = $isFinal;

      $sa = new My_Model_SubmittedAssignment();
      $aa = new My_Model_AssignmentAnswers();

      // select the specified record
      $sel = $sa->select();
      foreach ($sub as $key => $val) {
         if ($key === 'username') {
            $val = $this->_db->quote($val);
         }
         $sel = $sel->where("$key = $val");
      }

      $res = $this->fetchRow($sel);

      // if a record was found
      if ($res) {
         $row = $res->toArray();

         // if this record has already been submitted as final.
         if ($row['is_final']) {
            return "submitted";

         // else if it was submitted as save only, do updates
         } else {
            $where = array();
            //unset($sub['is_final']);

            foreach ($sub as $key => $val) {
               if ($key === 'username') {
                  $val = $this->_db->quote($val);
               } 

               $where[]= "$key = $val";
            }

            $sub['answer_text'] = $data['report'];

            try {
               $aa->update($sub, $where);

               // if the current submission is final update the is_final field to true
               if ($isFinal) {
                  $sa->update(array('is_final' => 1, 'date_submitted' => date('Ymd')), $where);
               }
               return true;
            } catch(Exception $e) {
               return false;
            }
         }

      // else if no record was found, submit the assignment
      } else {
         $funcs = new My_Funcs();
         $sub['date_submitted'] = date('Ymd');
         $sub['is_final'] = $isFinal;

         $sa->insert($sub);

         unset($sub['is_final']);
         unset($sub['date_submitted']);

         $sub['answer_text'] = $data['report'];
         $aa->insert($sub);
         return true;

      }

   }

   public function submitStudentEval($data)
   {
      unset($data['Submit']);

      $db = new My_Db();
      $aa = new My_Model_AssignmentAnswers();
      $as = new My_Model_Assignment();
      $assignId = $as->getStudentEvalId();

      $coopSess = new Zend_Session_Namespace('coop');

      $insertVals = array('classes_id' => $coopSess->currentClassId, 
                          'semesters_id' => $coopSess->currentSemId, 
                          'username' => $coopSess->username, 
                          'assignments_id' => $assignId);

      // BEGIN TRANSACTION
      $as->getAdapter()->beginTransaction();

      // submit the assignment
      $res = $this->submit($insertVals);

      if ($res === 'submitted') {
         return 'submitted';
      }

      foreach ($data as $key => $val) {
         $insertVals['assignmentquestions_id'] = $key;
         $insertVals['answer_text'] = $val;
         
         try {
            $aa->insert($insertVals);
         } catch(Exception $e) {
            $as->getAdapter()->rollBack(); // ROLL BACK IF ERROR OCCURED
            return false;
         }
      }
      // COMMIT TRANSACTION
      $as->getAdapter()->commit();

      return true;

   }

   public function updateDuedates($data)
   {
      //$db = new My_Db();

      //$id = $data['id'];
      //unset($data['id']);

      //$data = $db->prepFormInserts($data, $this);
      //$funcs = new My_Funcs();
      //$data['due_date'] = $funcs->formatDateIn($data['due_date']);

      //$this->update($data, "id = $id");

      unset($data['Submit']);

      //die(var_dump($data));

      $funcs = new My_Funcs();
      foreach ($data as $id => $dueDate) {

         //die(var_dump($dueDate));
         $dueDate['due_date'] = $funcs->formatDateIn($dueDate['due_date']); 
         //die(var_dump($dueDate['due_date']));
         try {
            $res = $this->update($dueDate, "id = $id");
         } catch(Exception $e) {
            return false;
         }
      }
      return true;
   }

   public function extendDuedate($data)
   {
      if (isset($data['Submit'])) {
         unset($data['Submit']);
      }
      $coopSess = new Zend_Session_Namespace('coop');
      $sem = new My_Model_Semester();
      $funcs = new My_Funcs();
      $data['semesters_id'] = $coopSess->currentSemId;
      $data['due_date'] = $funcs->formatDateIn($data['due_date']);

      $ext = new My_Model_ExtendedDuedates();
      $uniqueness = $data; // unique fields to match when checking if record exists and when updating
      unset($uniqueness['due_date']); // don't want due date for this.

      // if record already exists, do an update instead of insert.
      if ($ext->rowExists($uniqueness)) {
         $where = array(); // array of where clauses (column = value string).
         $updateVals['due_date'] = $data['due_date']; // column to update

         foreach ($uniqueness as $key => $val) {

            // must single quote the value of 'username' key since it's a string
            if ($key === 'username') {
               //$where[] = $ext->_db->quoteInto("where $key = ?", $val);
               $where[] = "$key = '$val'";
            // rest are INTs or DATES so don't quote.
            } else {
               $where[] = "$key = $val";
            }
         }

         // UPDATE
         try {
            $ext->update($updateVals, $where);
         } catch(Exception $e) {
            return false;
         }

      } else {

         // INSERT
         try {
            $ext->insert($data);
         } catch(Exception $e) {
            return false;
         }
      }

      return true;
   }


   public function updateQuestions($data)
   {
      unset($data['submit']);
      $assignId = $data['assignId'];
      unset($data['assignId']);

      $aq = new My_Model_AssignmentQuestions();

      foreach ($data as $qNum => $vals) {
         try {
            $res = $aq->update($vals, "assignments_id = $assignId AND question_number = $qNum");
         } catch(Exception $e) {
            return false;
         }
      }

      return true;

   }

   public function updateQuestionsStuEval($data)
   {
      unset($data['Submit']);

      $aq = new My_Model_AssignmentQuestions();

      foreach ($data as $id => $vals) {
         //die(var_dump($vals));
         try {
            $aq->update($vals, "id = $id");
         } catch(Exception $e) {
            return false;
         }
         
      }
      return true;
   }

   public function addQuestion($data)
   {
      unset($data['Add']); // unset the submit button.
      $assignId = $data['assignId'];
      unset($data['assignId']);

      $aq = new My_Model_AssignmentQuestions();

      //if (isset($data['type'])) {
      //   $opts = array('classes_id')
      //   $qNum = 
      //}

      $qNum = $aq->getLastQuestionNum($assignId);

      $data['question_number'] = $qNum+1; // make sure it gets the last question number
      $data['assignments_id'] = $assignId;

      try {
         $aq->insert($data);
      } catch(Exception $e) {
         return false;
      }

      return true;

   }

   public function addQuestionStudentEval($data)
   {
      if (isset($data['Add'])) {
         unset($data['Add']); // unset the submit button.
      }
      $assignId = $data['assignId'];
      unset($data['assignId']);

      $aq = new My_Model_AssignmentQuestions();

      //if (isset($data['type'])) {
      //   $opts = array('classes_id')
      //   $qNum = 
      //}

      $coopSess = new Zend_Session_Namespace('coop');
      $stuEvalData = $coopSess->stuEvalManagementData;
      $where['classes_id'] = $stuEvalData['classId'];

      // if a parent is being added
      if ($data['question_type'] === 'parent') {
         $where['question_type'] = 'parent';
         unset($data['parent']); // unset because only child questions need this field set
      // if a child is being added
      } else {
         // if a parent was chosen for the child question
         if (isset($data['parent'])) {
            $where['parent'] = $data['parent'];
         // if no parent was chosen, then that means there were no parents to begin with 
         // since the dropdown would automatically have a parent selected if at least one existed.
         } else {
            $where['parent'] = 1;
            $data['parent'] = 1; // make the child belong to the first parent if it was created before the parent.
            // the parent might not exist at this point
            //$parentExists = $aq->chkParentExistence(array('assignments_id' => $assignId, 'classes_id' => $stuEvaldata['classId'], 'question_number' => $data['parent']));

            // insert the parent since none exists yet, and a child must have a parent.
            $aq->insert(array('assignments_id' => $assignId, 'classes_id' => $stuEvalData['classId'], 'question_type' => 'parent', 'question_number' => 1));

         }

      }

      $qNum = $aq->getLastQuestionNum($assignId, $where);

      $data['question_number'] = $qNum+1; // make sure it gets the last question number
      $data['assignments_id'] = $assignId;
      $data['classes_id'] = $stuEvalData['classId'];

      //die(var_dump($data));

      try {
         $aq->insert($data);
      } catch(Exception $e) {
         return false;
      }

      return true;

   }

   /*
    * Tables referenced - coop_assignmentquestions
    */
   public function deleteQuestion($questions, $assignId)
   {
      $aq = new My_Model_AssignmentQuestions();

      //die(var_dump($questions));
      // count how many questions were deleted to know how much to decrement the other question numbers
      // (used in update below).
      $count = 0; 
      foreach ($questions as $q) {
         try {
            $aq->delete("question_number = $q AND assignments_id = $assignId");
            $count++;
            $qNum = $q; // $qNum ends up with the value of the last question number deleted (used in update below).
         } catch(Exception $e) {

            return false;

         }
      }

      $exp = new Zend_Db_Expr("question_number-$count");

      // update the table so that question numbers greater than the last one deleted will
      // be adjusted so there is no gap between numbers after deletion.
      $aq->update(array('question_number' => $exp), "assignments_id = $assignId AND question_number > $qNum");

      return true;

   }

   public function deleteQuestionsStudentEval($data)
   {
      unset($data['Submit']);

      $coopSess = new Zend_Session_Namespace('coop');
      $stuEvalData = $coopSess->stuEvalManagementData;
      $assignId = $stuEvalData['assignId'];
      $classId = $stuEvalData['classId'];

      $questions = $data['questions'];

      $aq = new My_Model_AssignmentQuestions();
      foreach ($questions as $qid) {
         $question = $aq->fetchRow("id = $qid");
         //die(var_dump($question->question_type));
         $qType = $question->question_type;
         if ($qType === 'parent') {
            //die(var_dump($q['question_text']));
            $children = $aq->fetchAll(array("assignments_id = $assignId", 
                                            "classes_id = $classId", 
                                            "question_type = 'child'", 
                                            "parent = '" . $question['question_number'] . "'"));
            $rows = $children->toArray();
            //die(var_dump($rows));

            foreach ($children as $c) {
               $c->parent = 1;

               $lastQNum = $aq->getLastQuestionNum($assignId, 
                                                   array("assignments_id" => $assignId, 
                                                         "classes_id" => $classId, 
                                                         "parent" => 1));
               //die(var_dump($lastQNum));

               $c->question_number = $lastQNum+1;

               $c->save();

               //die(var_dump($c));
            }
         }

         try {
            $aq->delete("id = $qid");
         } catch(Exceptionn $e) {
            return false;
         }
      }
      
      return true;

   }


   public function getAll()
   {
      $rows = $this->fetchAll()->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;
   }

   // Gets non-submitted assignments for a student, class, semester
   public function getNonSubmitted()
   {
      $coopSess = new Zend_Session_Namespace('coop');
      $assigns = $this->getAll();

      $chk['semesters_id'] =  $coopSess->currentSemId;
      $chk['classes_id'] =  $coopSess->currentClassId;
      $chk['username'] =  $coopSess->username;

      $nonSubmitted = array();
      foreach ($assigns as $a) {
         $chk['assignments_id'] = $a['id'];

         if ($this->isSubmitted($chk) === false) {
            $nonSubmitted[] = $a;
         }

      }
      //die('hi');


      return $nonSubmitted;
   }

   // Gets submitted assignments for a student, class, semester
   public function getSubmitted()
   {
      $coopSess = new Zend_Session_Namespace('coop');
      $assigns = $this->getAll();

      $chk['semesters_id'] =  $coopSess->currentSemId;
      $chk['classes_id'] =  $coopSess->currentClassId;
      $chk['username'] =  $coopSess->username;

      $submitted = array();
      foreach ($assigns as $a) {
         $chk['assignments_id'] = $a['id'];

         if ($this->isSubmitted($chk) === true) {
            $submitted[] = $a;
         }

      }

      return $submitted;

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
      $id = $this->getId(array('assignment_num' => 1));
      //$id = $this->getId(array('assignment' => "Student Information Sheet"));

      if (empty($id)) {
         $id = 0;
      }
      return $id;
   }

   public function getMidtermId()
   {
      $id = $this->getId(array('assignment_num' => 2));
      //$id = $this->getId(array('assignment' => "Midterm Report"));
      if (empty($id)) {
         $id = 0;
      }
      return $id;

   }

   public function getCoopAgreementId()
   {
      $id = $this->getId(array('assignment_num' => 3));
      if (empty($id)) {
         $id = 0;
      }
      return $id;

   }

   public function getLearningOutcomeId()
   {
      $id = $this->getId(array('assignment_num' => 4));
      if (empty($id)) {
         $id = 0;
      }
      return $id;

   }

   public function getStudentEvalId()
   {
      $id = $this->getId(array('assignment_num' => 5));
      if (empty($id)) {
         $id = 0;
      }
      return $id;

   }

   public function getSupervisorEvalId()
   {
      $id = $this->getId(array('assignment_num' => 6));
      if (empty($id)) {
         $id = 0;
      }
      return $id;

   }

   /*
    * Gets all questions for a specific assignment based on assignment id
    * 
    * @param $id - the assignment ID to search on
    * @param optional $where - the where parameters.
    * @param optional $order - the order clause.
    * 
    * Tables referenced - coop_assignmentquestions
    */
   public function getQuestions($id)
   {
      $aq = new My_Model_AssignmentQuestions();

      $sel = $aq->select()->where("assignments_id = $id");

      $args = func_get_args();

      if (count($args) > 1) {
         $where = $args['1'];
         foreach ($where as $key => $val) {
            if ($key === 'question_type') {
               $sel = $sel->where("$key = '$val'");
            } else {
               $sel = $sel->where("$key = $val");
            }

         }
      }


      if (count($args) > 2) {
         $order = $args['2'];
         $sel->order($order);
      }

      $sql = $sel->assemble();
      //die($sql);
      
      $rows = $aq->fetchAll($sel)->toArray();

      if (empty($rows)) {
         $rows = array();
      }
      //die(var_dump($rows));

      return $rows;
      
   }


   /*
    * Checks if a specific assignment has already been submitted based on username, class,
    * semester, assignment.
    * 
    * 
    * @param $data associative array with the keys 'username', 'classes_id', 'semesters_id',
    *              'assignments_id'
    */
   public function isSubmitted(array $data)
   {
      $sa = new My_Model_SubmittedAssignment();

      $db = new My_Db();
      $data['is_final'] = true;
      $data = $db->prepFormInserts($data, $sa);

      if ($sa->rowExists($data)) {
         //die(var_dump($data));
         return true;
      }
      //die('hi');
      
      return false;

   }

   public function isDue($assignId)
   {
      date_default_timezone_set('US/Hawaii');
      $coopSess = new Zend_Session_Namespace('coop');
      $ext = new My_Model_ExtendedDuedates();
      // uniqueness to get due_date from coop_extended_duedates.
      $extWhere = array( 'semesters_id' => $coopSess->currentSemId, 
                         'classes_id' => $coopSess->currentClassId,
                         'assignments_id' => $assignId,
                         'username' => $coopSess->username);  

      // if there is an extended due date matching the uniqueness, use that due date.
      if ($extDuedate = $ext->getDuedate($extWhere)) {
         $dueDate = $extDuedate;
      // otherwise, use the default due date in coop_assignments.
      } else {
         $res = $this->select()->where("id = $assignId");

         $row = $this->fetchRow($res);

         if (is_null($row)) {
            return false;
         }
         $row = $row->toArray();
         $dueDate = $row['due_date'];
      }

      $dueDate = strtotime($dueDate);

      $curDate = strtotime(date('Ymd'));

      if ($curDate > $dueDate) {
         return true;
      }

      return false;

      //die(var_dump($curDate));
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
      $sel = $this->select();
      foreach ($where as $key => $val) {
         $sel = $sel->where("$key = ?", $val);
      }
      $rows = $this->fetchAll($sel)->toArray();

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
