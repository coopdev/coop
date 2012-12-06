<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Handles actions related to assignments
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
    * Supervisor eval = 6,
    * Student Time Sheet = 7
    */


/*********************************** SUBMISSION TYPE METHODS ****************************/

   /**
    *  Inserts an assignment into the coop_submittedassignments table.
    * 
    * @param array $data  Associative array containing: username, classes_id, assignments_id
    * @return string|boolean The string 'submitted' if the assignment is already submitted,
    *                        True on success.
    */
   public function submit(array $data, $submitType="", $allowMultiSubmit=false)
   {
      date_default_timezone_set('US/Hawaii');
      $db = new My_Db();
      $sa = new My_Model_SubmittedAssignment();
      $sem = new My_Model_Semester();

      // Filter out array keys which are not column names in submitted_assignments table.
      $data = $db->prepFormInserts($data, $sa);

      // Add other required data.
      //$data['semesters_id'] = $sem->getCurrentSemId();
      $data['date_submitted'] = date('Ymd');

      // Create data to check if assignment is already submitted.
      $chk['username'] = $data['username'];
      $chk['classes_id'] = $data['classes_id'];
      $chk['semesters_id'] = $data['semesters_id'];
      $chk['assignments_id'] = $data['assignments_id'];

      //die(var_dump($chk));

      // If the assignment has already been submitted as final.
      if ($allowMultiSubmit === false && $this->isSubmitted($chk)) {
         return "submittedFinal";
      } 

      // If the assignment has already been submitted as save only. 
      if ($this->isSaveOnly($chk)) {

         // If assignment is currently being submitted as final.
         if ($submitType === 'finalSubmit') {
            $where = $db->buildArrayWhereClause($chk);
            $saRow = $sa->fetchRow($where);
            $saRow->is_final = 1;
            $saRow->date_submitted = date('Ymd');
            $saRow->save();
         } 

         return "submittedSaveOnly";
      }


      // If the assignment is currently being submitted as save only.
      if ($submitType === 'saveOnly') {
         // The is_final column defaults to zero, so do not need to explicitly set it.
         $sa->insert($data);
         return true;
      }

      // Since $submitType is not 'Save Only', set is_final to 1 when inserting.
      $data['is_final'] = 1;
      $sa->insert($data);

      return true;
   }



   public function undoSubmit($id)
   {
      $subAssign = new My_Model_SubmittedAssignment();
      $adapter = $this->getAdapter();

      $subAssign->delete("id = $id");


   }

   public function fetchSubmittedAssignments($where)
   {
      $db = new My_Db();
      $sa = new My_Model_SubmittedAssignment();

      $select = $sa->select();
      $select = $db->buildSelectWhereClause($select, $where);

      $rows = $sa->fetchAll($select);

      return $rows;
   }

   public function fetchSubmittedAssignment($where)
   {
      $row = $this->fetchSubmittedAssignments($where);

      return $row->current();

   }
   
   
   /*
    * Inserts a submitted Midterm Report's answer's into the database.
    * 
    * 
    * @param array $answers The Midterm Report's submitted answers.
    * @return string|boolean The string 'submitted' if the assignment has already been submitted,
    *                        True on success, False on failure.
    */
   public function submitMidtermReport($answers, $opts=array())
   {
      $coopSess = new Zend_Session_Namespace('coop');
      $sa = new My_Model_SubmittedAssignment();
      $db = new My_Db();

      // userData represents the students semesters_id, classes_id, username, etc.
      if (isset($opts['userData'])) {
         $submitVals = $opts['userData'];

      // If $opts['userData'] isn't set, default to the currently logged in user data.
      } else {
         $submitVals['username'] = $coopSess->username;
         $submitVals['classes_id'] = $coopSess->currentClassId;
         $submitVals['semesters_id'] = $coopSess->currentSemId;
      }
      $submitVals['assignments_id'] = $this->getMidtermId();



      // Check what type of submit it is; save only or final.
      if (array_key_exists('saveOnly', $answers)) {
         $submitType = 'saveOnly';
         unset($answers['saveOnly']);
      } else if (array_key_exists('finalSubmit', $answers)) {
         $submitType = 'finalSubmit';
         unset($answers['finalSubmit']);
      }


      
      // BEGIN TRANSACTION
      $this->getAdapter()->beginTransaction();

      // Submit assignment into coop_submittedassignments
      $submitResult = $this->submit($submitVals, $submitType);

      // If already submitted.
      if ($submitResult === 'submittedFinal') {
         return "submitted";
      }

      // Fetch the submitted assignment.
      $submittedAssign = $sa->fetchRow($db->buildArrayWhereClause($submitVals));
      
      // If this assignment has already been submitted as save only.
      if ($submitResult === 'submittedSaveOnly') {
         $where['submittedassignments_id'] = $submittedAssign->id;
         $res = $this->updateAnswers($answers, $where);


      // If this assignment has never been submitted yet, even as save only.
      } else if ($submitResult === true) {
         $foreignKeys['submittedassignments_id'] = $submittedAssign->id;
         $res = $this->insertAnswers($answers, $foreignKeys);
         
      }

      // If either insertAnswers() or updateAnswers() returned 'exception' due to an Exception
      // being caught.
      if ($res === 'exception') {
         $this->getAdapter()->rollBack();
         return false;
      }

      $this->getAdapter()->commit();

      return true;

   }

   /**
    *
    * @param array $data The Learning Outcome text written by the student.
    * @return string|boolean 
    */
   public function submitLearningOutcome($answers)
   {
      $coopSess = new Zend_Session_Namespace('coop');
      $sa = new My_Model_SubmittedAssignment();
      $db = new My_Db();

      // userData represents the students semesters_id, classes_id, username, etc.
      if (isset($opts['userData'])) {
         $submitVals = $opts['userData'];

      // If $opts['userData'] isn't set, default to the currently logged in user data.
      } else {
         $submitVals['username'] = $coopSess->username;
         $submitVals['classes_id'] = $coopSess->currentClassId;
         $submitVals['semesters_id'] = $coopSess->currentSemId;
      }
      $submitVals['assignments_id'] = $this->getLearningOutcomeId();



      // Check what type of submit it is; save only or final.
      if (array_key_exists('saveOnly', $answers)) {
         $submitType = 'saveOnly';
         unset($answers['saveOnly']);
      } else if (array_key_exists('finalSubmit', $answers)) {
         $submitType = 'finalSubmit';
         unset($answers['finalSubmit']);
      }


      
      // BEGIN TRANSACTION
      $this->getAdapter()->beginTransaction();

      // Submit assignment into coop_submittedassignments
      $submitResult = $this->submit($submitVals, $submitType);

      // If already submitted.
      if ($submitResult === 'submittedFinal') {
         return "submitted";
      }

      // Fetch the submitted assignment.
      $submittedAssign = $sa->fetchRow($db->buildArrayWhereClause($submitVals));
      
      // If this assignment has already been submitted as save only.
      if ($submitResult === 'submittedSaveOnly') {
         $where['submittedassignments_id'] = $submittedAssign->id;
         $res = $this->updateAnswers($answers, $where, array('static' => true));


      // If this assignment has never been submitted yet, even as save only.
      } else if ($submitResult === true) {
         $foreignKeys['submittedassignments_id'] = $submittedAssign->id;
         $res = $this->insertAnswers($answers, $foreignKeys, array('static' => true));
         
      }

      // If either insertAnswers() or updateAnswers() returned 'exception' due to an Exception
      // being caught.
      if ($res === 'exception') {
         $this->getAdapter()->rollBack();
         return false;
      }

      $this->getAdapter()->commit();

      return true;

   }
   
   public function submitAgreementForm($form)
   {
      $db = new My_Db();
      $sa = new My_Model_SubmittedAssignment();
      
      $coopSess = new Zend_Session_Namespace('coop');
      
      
      $userData['username'] = $form->getUsername();
      $userData['classes_id'] = $form->getClassId();
      $userData['semesters_id'] = $form->getSemId();
      
      
      $userData['assignments_id'] = $form->getAssignId();

      $submitType = 'finalSubmit';


      // Answers to static questions.
      $statics = $form->static_tasks->getValues();
      $statics = $statics['static_tasks'];

      // Answers to dynamic questions.
      $dynamics = $form->dynamic_tasks->getValues();
      $dynamics = $dynamics['dynamic_tasks'];
      //die(var_dump($dynamics));
      
      
      // BEGIN TRANSACTION
      $this->getAdapter()->beginTransaction();

      // Attempt to submit the assignment
      $submitResult = $this->submit($userData, $submitType, true);
      $foreignKeys['submittedassignments_id'] = $sa->getAdapter()->lastInsertId();



      // If this assignment has never been submitted yet, even as save only.
      $res1 = $this->insertAnswers($statics, $foreignKeys, array('static' => true));
      $res2 = $this->insertAnswers($dynamics, $foreignKeys);
         

      // If either insertAnswers() or updateAnswers() returned 'exception' due to an Exception
      // being caught.
      if ($res1 === 'exception' || $res2 === 'exception' ) {
         $this->getAdapter()->rollBack();
         return false;
      }

      $this->getAdapter()->commit();

      return true;


   }
   
   
   public function submitStudentEval($form)
   {
      $db = new My_Db();
      $sa = new My_Model_SubmittedAssignment();
      $coopSess = new Zend_Session_Namespace('coop');

      
      $userData['username'] = $form->getUsername();
      $userData['classes_id'] = $form->getClassId();
      $userData['semesters_id'] = $form->getSemId();
      $userData['assignments_id'] = $form->getAssignId();
      

      if ($form->saveOnly->isChecked()) {
         $submitType = 'saveOnly';
      } else if ($form->finalSubmit->isChecked()) {
         $submitType = 'finalSubmit';
      }


      // Answers to static questions.
      if (isset($form->static_tasks)) {
         $statics = $form->static_tasks->getValues();
         $statics = $statics['static_tasks'];
      } else {
         $statics = array();
      }

      // Answers to dynamic questions.
      if (isset($form->dynamic_tasks)) {
         $dynamics = $form->dynamic_tasks->getValues();
         $dynamics = $dynamics['dynamic_tasks'];
      } else {
         $dynamics = array();
      }
      //die(var_dump($dynamics));
      

      // BEGIN TRANSACTION
      $this->getAdapter()->beginTransaction();

      // Attempt to submit the assignment
      $submitResult = $this->submit($userData, $submitType);

      // If assignment has already been submitted as final.
      if ($submitResult === 'submittedFinal') {
         return 'submitted';
      }


      // Fetch the submitted assignment.
      $submittedAssign = $sa->fetchRow($db->buildArrayWhereClause($userData));
      
      // If this assignment has already been submitted as save only.
      if ($submitResult === 'submittedSaveOnly') {
         $where['submittedassignments_id'] = $submittedAssign->id;
         $res1 = $this->updateAnswers($statics, $where, array('static' => true));
         $res2 = $this->updateAnswers($dynamics, $where);


      // If this assignment has never been submitted yet, even as save only.
      } else if ($submitResult === true) {
         $foreignKeys['submittedassignments_id'] = $submittedAssign->id;
         $res1 = $this->insertAnswers($statics, $foreignKeys, array('static' => true));
         $res2 = $this->insertAnswers($dynamics, $foreignKeys);
         
      }

      // If either insertAnswers() or updateAnswers() returned 'exception' due to an Exception
      // being caught.
      if ($res1 === 'exception' || $res2 === 'exception' ) {
         $this->getAdapter()->rollBack();
         return false;
      }

      $this->getAdapter()->commit();

      return true;

   }
   
/*************************** END SUBMISSION TYPE METHODS ********************************/
   
   
   
/*************************** FORM POPULATION TYPE METHODS *******************************/

   /**
    * Populates the Midterm Report with answers for a specific student.
    * 
    * 
    * @param Zend_Form $form The Midterm Report Zend_Form to be populated.
    * @param array $data Criteria to get the Midterm Report's answers from coop_assignmentsanswers 
    *                    (username, semesters_id, classes_id, assignments_id).
    * @return Zend_Form The populated Midterm Report. 
    */
   public function populateMidTermReport($form, $data)
   {
      unset($data['coordinator']);

      $db = new My_Db();
      
      $select = $this->select()->setIntegrityCheck(false);
      $select = $select->from('submittedassignment_answers_view');
      $select = $db->buildSelectWhereClause($select, $data);
      
      $answers = $this->fetchAll($select);

      $formData = array();

      foreach ($answers as $a) {
         
         // Field to identify the question (either assignmentquestions_id or static_question) 
         // which the answer belongs to is required to populate the form, since the form uses 
         // the field value as it's name.
         if (!is_null($a['static_question'])) {
            $question = $a->static_question; 
         } else {
            $question = $a->assignmentquestions_id; 
         }
         $formData[$question] = $a->answer_text;
         
      }

      $form->populate($formData);
      return $form;

   }

   /**
    * Populates the Learning Outcome Report for a specific student.
    * 
    * 
    * @param Zend_Form $form The Learning Outcome Zend_Form to be populated.
    * @param array Optional parameter may be passed as the criteria to populate the form (use func_get_arg()).
    *              if no optional parameter is passed, use session data as the criteria.
    * @return Zend_Form The populated Learning Outcome Report. 
    */
   public function populateLearningOutcome($form, $where)
   {

      $db = new My_Db();

      $where['assignments_id'] = $this->getLearningOutcomeId();
      
      $select = $this->select()->setIntegrityCheck(false);
      $select = $select->from('submittedassignment_answers_view');
      $select = $db->buildSelectWhereClause($select, $where);
      
      $answers = $this->fetchAll($select);

      $formData = array();

      foreach ($answers as $a) {
         
         // Field to identify the question (either assignmentquestions_id or static_question) 
         // which the answer belongs to is required to populate the form, since the form uses 
         // the field value as it's name.
         if (!is_null($a['static_question'])) {
            $question = $a->static_question; 
         } else {
            $question = $a->assignmentquestions_id; 
         }
         $formData[$question] = $a->answer_text;
         
      }

      $form->populate($formData);
      return $form;

   }

   /**
    * Populates the Student Evaluation Form with a student's answers based on the student's
    * username, current class id, current semester id.
    * 
    * @param Zend_Form $form The Student Evaluation Form to populate.
    * @param array $data Associative array containing username, classes_id, semesters_id.
    * @return \Zend_Form  The populated Student Eval Form.
    */

   public function populateStudentEval($form, $data)
   {
      unset($data['coordinator']);
      
      $db = new My_Db();
      
      $select = $this->select()->setIntegrityCheck(false);
      $select = $select->from('submittedassignment_answers_view');
      $select = $db->buildSelectWhereClause($select, $data);
      
      $answers = $this->fetchAll($select);

      $formData = array();

      foreach ($answers as $a) {
         
         // Field to identify the question (either assignmentquestions_id or static_question) 
         // which the answer belongs to is required to populate the form, since the form uses 
         // the field value as it's name.
         if (!is_null($a['static_question'])) {
            $question = $a->static_question; 
         } else {
            $question = $a->assignmentquestions_id; 
         }
         $formData[$question] = $a->answer_text;
         
      }

      $form->populate($formData);
      return $form;

   }

/*********************** END FORM POPULATION TYPE METHODS *******************************/


/************************************ ANSWER TYPE METHODS *******************************/

   public function fetchAnswers($where)
   {
      $AsnmtAnswers = new My_Model_AssignmentAnswers();
      $db = new My_Db();

      $select = $AsnmtAnswers->select();
      $select = $db->buildSelectWhereClause($select, $where);


      $answers = $this->fetchAll($select);

      // If no results, return empty array.
      if (count($answers) < 1) {
         return array();
      }

      return $answers;
   }

   public function fetchAnswersForLastSubmitted($where)
   {
      $SubmittedAsnmt = new My_Model_SubmittedAssignment();
      $db = new My_Db();

      $select = $SubmittedAsnmt->select();

      $select = $db->buildSelectWhereClause($select, $where);


      $subAsnmts = $this->fetchAll($select);
      //die(var_dump(count($subAsnmts)));

      if (count($subAsnmts) > 0) {
         
         // get last subbmitted assignment.
         $last = $subAsnmts->getRow(count($subAsnmts) - 1);
         $subAsnmtId = $last->id;
         //die(var_dump($subAsnmtId));

         $answers = $this->fetchAnswers(array('submittedassignments_id' => $subAsnmtId));

         return $answers;

      }

      return array();
   }



   /*
    * Updates answers for certain assignments (the ones that have questions and answers).
    * Used to update eval type assignments and midterm report.
    */
   public function updateAnswers($answers, $where, $opts=array())
   {
      $aa = new My_Model_AssignmentAnswers();
      $db = new My_Db();

      $where = $db->buildArrayWhereClause($where);

      foreach ($answers as $key => $val) {

         // If updating answers to static questions.
         if (isset($opts['static']) && $opts['static'] === true) {
            $where[] = "static_question = '$key'";
         // If updating answers to dynamic questions.
         } else {
            $where[] = "assignmentquestions_id = '$key'";
         }

         //die(var_dump($where));
         $row = $aa->fetchRow($where);

         // After using $where, get rid of the question id so a new one can be added on 
         // the next loop.
         array_pop($where);

         $row->answer_text = $val;
         try {
            $row->save();
         } catch(Exception $e) {
            return 'exception';
         }
      }

      return true;

   }

   /*
    * Inserts answers for certain assignments (the ones that have questions and answers).
    * Used to insert eval type assignments and midterm report.
    */
   public function insertAnswers($answers, $foreignKeys, $opts=array())
   {
      $aa = new My_Model_AssignmentAnswers();
      $db = new My_Db();
      
      
      $insertVals = $foreignKeys;


      foreach ($answers as $key => $val) {

         // If inserting answers to static questions.
         if (isset($opts['static']) && $opts['static'] === true) {
            $insertVals['static_question'] = $key;

         // If inserting answers to dynamic questions.
         } else {
            $insertVals['assignmentquestions_id'] = $key;
         }

         $insertVals['answer_text'] = $val;


         try {
            $aa->insert($insertVals);
         } catch(Exception $e) {
            return 'exception';
         }

         // After using $where, get rid of the question id so a new one can be added on 
         // the next loop.
         array_pop($insertVals);

      }

      return true;

   }
/****************************** END ANSWER TYPE METHODS *********************************/


   /**
    * Updates due dates for assignments.
    * 
    * 
    * @param array $data The updated due dates for each assignment.
    * @return boolean 
    */
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

   /**
    * Extends a due date for a specific student and assignment.
    * 
    * 
    * @param array $data Associative array containing the student's username, and assignment id.
    * @return boolean 
    */
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


/*********************** Methods For Managing Assignment Questions **********************/

   
   /**
    * Updates questions to assignments.
    * 
    * 
    * @param array $data The updated questions and the assignment id.
    * @return boolean 
    */
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

   /**
    * Updates questions for the Student Evaluation.
    * 
    * 
    * @param array $data The updated questions from the form.
    * @return boolean 
    */
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

   /**
    * Adds a single question to a specific assignment.
    * 
    * 
    * @param array $data The question along with it's max answer length to add from the form.
    * @return boolean 
    */
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

   public function addRatedQuestion($data)
   {
      if (isset($data['Submit'])) {
         unset($data['Submit']);
      }

      //die(var_dump($data));
      $aq = new My_Model_AssignmentQuestions();

      try {
         $aq->insert($data);
      } catch (Exception $e) {
         return false;
      }

      return true;

   }
   
   public function updateRatedQuestions($data, $where)
   {
      $db = new My_Db();

      $where = $db->buildArrayWhereClause($where);

      $aq = new My_Model_AssignmentQuestions();

      foreach ($data as $qid => $val) {

         try {
            $where[] = "id = $qid";
            $res = $aq->update( array('question_text' => $val), $where);
            array_pop($where);

         } catch(Exception $e) {

            return false;

         }
      }

      return true;

   }

   public function deleteRatedQuestions($questionIds)
   {
      $where = "id IN (";
      foreach ($questionIds as $qid) {
         $where .=  "$qid,";
      }
      $where = substr_replace($where, '', strlen($where) - 1); 
      
      $where .= ")";

      $AssignQuestion = new My_Model_AssignmentQuestions();

      try {
         $AssignQuestion->delete($where);
         return true;
      } catch (Exception $e) {
         return false;
      }


   }

   /**
    * Adds a header or child question to the student eval form.
    * 
    * 
    * @param array $data The question to add from the form.
    * @return boolean 
    */
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

   /**
    * Deletes a question from an assignment.
    * 
    * 
    * Uses $questions combined with $assignId to delete the specific assignment's questions.
    * After deleting a question, re-order the questions so that they are in sequence 
    * according to question num again.
    * 
    * @param array $questions The set of question numbers used to delete each question.
    * @param string $assignId The assignment's id.
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

   /**
    * Deletes questions for the student eval.
    * 
    * 
    * If one of the questions being deleted is a parent, first move all of it's child
    * questions to the first parent question and assign them the appropriate question
    * numbers. If not a parent, no special handling needed.
    * 
    * @param array $data The ID's of each question being deleted.
    * @return boolean 
    */
   public function deleteQuestionsStudentEval($data)
   {
      unset($data['Submit']);

      $coopSess = new Zend_Session_Namespace('coop');
      $stuEvalData = $coopSess->stuEvalManagementData;
      $assignId = $stuEvalData['assignId'];
      $classId = $stuEvalData['classId'];

      // array of question IDs.
      $questions = $data['questions'];

      $aq = new My_Model_AssignmentQuestions();
      foreach ($questions as $qid) {
         $question = $aq->fetchRow("id = $qid");
         $qType = $question->question_type;

         // if a parent is being deleted.
         if ($qType === 'parent') {

            // get all of that parent's children.
            $children = $aq->fetchAll(array("assignments_id = $assignId", 
                                            "classes_id = $classId", 
                                            "question_type = 'child'", 
                                            "parent = '" . $question['question_number'] . "'"));
            $rows = $children->toArray();

            foreach ($children as $c) {

               // assign each orphan child to the first parent before deleting the original parent.
               $c->parent = 1;

               // get last question num of children under first parent so that the new child 
               // can get the next number.
               $lastQNum = $aq->getLastQuestionNum($assignId, 
                                                   array("assignments_id" => $assignId, 
                                                         "classes_id" => $classId, 
                                                         "parent" => 1));

               // assign new child the next question num.
               $c->question_number = $lastQNum+1;

               // insert the child question.
               $c->save();
            }
         }

         // delete the question.
         try {
            $aq->delete("id = $qid");
         } catch(Exceptionn $e) {
            return false;
         }
      }
      
      return true;

   }

/********************** End Methods For Managing Assignment Questions *******************/


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
      $row = $this->fetchRow("id = $id");

      if (!empty($row)) {
         $row = $row->toArray();
      } else {
         $row = array();
      }

      return $row;

   }

   public function getAssignmentByNum($num)
   {
      $row = $this->fetchRow("assignment_num = $num");

      if (!empty($row)) {
         return $row;
      } else {
         return array();
      }

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
   
   public function getTimeSheetId()
   {
      $id = $this->getId(array('assignment_num' => 7));
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

      //$sql = $sel->assemble();
      //die($sql);
      
      $rows = $aq->fetchAll($sel)->toArray();

      if (empty($rows)) {
         $rows = array();
      }
      //die(var_dump($rows));

      return $rows;
      
   }


   /*
   public function getAnswers($where)
   {
      $aa = new My_Model_AssignmentAnswers();

      $select = $aa->select();

      foreach ($where as $key => $val) {
         //$select->
      }
   }
    */


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
      $data['is_final'] = 1;
      $data = $db->prepFormInserts($data, $sa);

      if ($sa->rowExists($data)) {
         //die(var_dump($data));
         return true;
      }
      
      return false;

   }

   public function isSaveOnly(array $data)
   {
      $sa = new My_Model_SubmittedAssignment();

      $db = new My_Db();
      $data['is_final'] = 0;
      $data = $db->prepFormInserts($data, $sa);


      //die(var_dump($data));
      if ($sa->rowExists($data)) {
         return true;
      }
      
      return false;

   }

   public function isSubmittedOrSaved(array $where)
   {
      $sa = new My_Model_SubmittedAssignment();
      $db = new My_Db();
      $data = $db->prepFormInserts($data, $sa);

      if ($sa->rowExists($where)) {
         return true;
      }

      return false;
   }

   /**
    * Checks if an assignment is due.
    * 
    * 
    * If the due date for the assignment was extended for this student, use the extended
    * due date for the check. Otherwise use the default due date for the assignment.
    * 
    * @param int|string $assignId
    * @return boolean 
    */
   public function isDue($assignId)
   {
      date_default_timezone_set('US/Hawaii');
      $coopSess = new Zend_Session_Namespace('coop');
      $ext = new My_Model_ExtendedDuedates();

      // uniqueness to get due_date from coop_extended_duedates.
      $extWhere = array( 'semesters_id' => $coopSess->currentSemId, 
                         //'classes_id' => $coopSess->currentClassId,
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

/********************* STUDENT INFO SHEET METHODS ***************************************/

   /**
    * Populates a Zend_Form Student Information Sheet with either the current users 
    * information based on their username, or uses the passed in username in the $opts 
    * associative array (using the 'username' key).
    * 
    * @param Zend_Form $form The Zend_Form to populate
    * @param array $opts Option to use the passed in username in the where clause. If no
    *                    username is passed, the current user's username is used.
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
       $dbExpr = new Zend_Db_Expr("AES_DECRYPT(uuid, 'alqpwoifjch') AS uuid");
       $query = $db->select()->from('coop_users', 
                                       array('fname', 'lname', $dbExpr, 'email'))
                                ->where("username = '" . $username . "'");
       $sql = $query->assemble();
       //return $sql;
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

       $userSem = new My_Model_UsersSemester();
       if (isset($opts['semesters_id'])) {
          $semId = $opts['semesters_id'];
       } else {
          $semId = $coopSess->currentSemId;
       }

       $row = $userSem->fetchRow(array("student = '$username'", "semesters_id = $semId"));
       if (!empty($row)) {
          $userSemVals['credits'] = $row['credits'];
       }

       //die(var_dump($userVals, $addrVals, $empVals, $homePhoneVals, $mobilePhoneVals));

       //$formVals = $userVals + $addrVals + $empVals + $homePhoneVals + $mobilePhoneVals + $stuVals;
       $formVals = $userVals + $addrVals + $homePhoneVals + $mobilePhoneVals + $stuVals + $currentClass + $userSemVals;

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

   public function submitStuInfoSheet($form)
   {
       $session = new Zend_Session_Namespace('coop');
       $db = new My_Db();
       $SubAssign = new My_Model_SubmittedAssignment();

       $userData['username'] = $form->getUsername();
       $userData['classes_id'] = $form->getClassId();
       $userData['semesters_id'] = $form->getSemId();
       $userData['assignments_id'] = $form->getAssignId();
      

       if ($form->saveOnly->isChecked()) {
          $submitType = 'saveOnly';
       } else if ($form->finalSubmit->isChecked()) {
          $submitType = 'finalSubmit';
       }

       $persInfo = $form->personalInfo->getValues();
       $persInfo = $persInfo['personalInfo'];
       $uuid = $session->uhinfo['uhuuid'];
       //$persInfo['uuid'] = new Zend_Db_Expr("AES_ENCRYPT('$uuid', 'alqpwoifjch')");
       $persInfo['uuid'] = $uuid;
       
       $eduInfo = $form->eduInfo->getValues();
       $eduInfo = $eduInfo['eduInfo'];
       unset($eduInfo['classes_id']);
       
       $empInfo = $form->empInfo->getValues();
       $empInfo = $empInfo['empInfo'];
       $empInfo['username'] = $form->getUsername();
       $empInfo['classes_id'] = $form->getClassId();
       $empInfo['semesters_id'] = $form->getSemId();
       $empInfo['start_date'] = date('Ymd', strtotime($empInfo['start_date']));
       $empInfo['end_date'] = date('Ymd', strtotime($empInfo['end_date']));
       //die(var_dump($empInfo['end_date']));
       
       // BEGIN TRANSACTION
       $this->getAdapter()->beginTransaction();

       // Attempt to submit the assignment
       $submitResult = $this->submit($userData, $submitType);

       $User = new My_Model_User();
       $User->update($persInfo, "username = '" . $form->getUsername() . "'");

       $Student = new My_Model_Student();
       $Student->update($eduInfo, 
                        array("username = '" . $form->getUsername() . "'", 
                              "semesters_id = " . $form->getSemId() 
                        )) ;

       $EmpInfo = new My_Model_EmpInfo();
       $EmpInfo->insert($empInfo);

       $this->getAdapter()->commit();
       

   }
/********************* END STUDENT INFO SHEET METHODS ***********************************/


/******************************* SURVEY METHODS *****************************************/

   /* Sets the amount of options for one of the survey 
    * type assignments such as Student Eval Form
    * 
    * @param $data array Array containing the assignment id and option amount.
    * 
    */
   public function setSurveyGlobalOptionAmount(array $data)
   {
      if (!isset($data['assignments_id'])) {
         return "Assignment ID not set.";
      }
      if (!isset($data['option_amount'])) {
         return "Options amount not set.";
      }
      if (isset($data['Submit'])) {
         unset($data['Submit']);
      }

      $assignId = $data['assignments_id'];
      $optionAmount = $data['option_amount'];

      //$assignment = new My_Model_Assignment();

      //die(var_dump($assignId, $optionAmount));

      $this->update(array('option_amount' => $optionAmount), "id = $assignId");

      return true;
   }


   public function getSurveySpecs($opts = array())
   {
      $where = array();
      if (isset($opts['where'])) {
         $where = $opts['where'];
      }

      $survSpec = new My_Model_SurveySpecifics();
      $select = $survSpec->select();
      foreach ($where as $key => $val) {
         $select->where("$key = ?", $val);
      }

      $rows = $survSpec->fetchAll($select);

      return $rows;

   }


   /*
    * Sets the amount of options for a specific class's survey type assignment.
    */
   public function setSurveyClassOptionAmount(array $data)
   {
      $assignId = $data['assignments_id'];
      $classId = $data['classes_id'];
      $optionAmount = $data['option_amount'];
      $useGlobal = $data['use_global'];
      

      $survSpecs = new My_Model_SurveySpecifics();

      $sel = $survSpecs->select()->where("assignments_id = $assignId")
                                 ->where("classes_id = $classId");

      $row = $survSpecs->fetchRow($sel);

      try {
         // if empty, insert.
         if (empty($row)) {
            $insertRow = $survSpecs->fetchNew();
            $insertRow->setFromArray($data)
                      ->save();
         // else update.
         } else {
            $row->option_amount = $optionAmount;
            $row->use_global = $useGlobal;
            $row->save();
         }

      } catch(Exception $e) {
         return false;
      }

      return true;

   }

   public function getSurveyOptionAmount($data = array())
   {
      $classId = $data['classes_id'];
      $assignId = $data['assignments_id'];

      $select = $this->select()->setIntegrityCheck(false);

      $select = $select->from('coop_survey_option_amount_view')
                       ->where("classes_id = ?", $classId)
                       ->where("assignments_id = ?", $assignId);

      $row = $this->fetchAll($select)->current();

      if (empty($row)) {
         return 0;
      }

      if ($row->use_global === '0') {
         return $row->specific_amount;
      } else if ($row->use_global === '1') {
         return $row->global_amount;
      }

   }

/*************************** END SURVEY METHODS *****************************************/








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
      die(var_dump($query->assemble()));
      $row = $this->fetchRow($query);

      if (empty($row)) {
         return false;
      }

      return true;
   }

}

?>
