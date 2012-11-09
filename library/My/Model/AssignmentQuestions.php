<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AssignmentQuestions
 *
 * @author joseph
 */
class My_Model_AssignmentQuestions extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_assignmentquestions";



   public function getQuestions($where)
   {

      $select = $this->select();
      $db = new My_Db();

      $select = $db->buildSelectWhereClause($select, $where);

      $questions = $this->fetchAll($select);

      return $questions;

   }


   /**
    * Retrieves the last question number for a specific assignment and additional optional WHERE criteria.
    * 
    * 
    * Mostly used when adding a question so that it gets the last question number.
    * 
    * @param string $assignId The assignment's id.
    * @param array $where The WHERE criteria.
    * @return string The last question number.
    */
   public function getLastQuestionNum($assignId, $where = array())
   {

      $sel = $this->select()->where("assignments_id = $assignId");

      foreach ($where as $key => $val) {
         if ($key === 'question_type') {
            $sel = $sel->where("$key = '$val'");
         } else {
            $sel = $sel->where("$key = $val");
         }
      }

      $sel = $sel->order("question_number DESC")->limit(1);

      $sql = $sel->assemble();

      $row = $this->fetchRow($sel);

      $num = 0;

      if (!is_null($row)) {
         $row = $row->toArray();
         $num = $row['question_number'];
      }

      return $num;
   }

   /**
    * Retrieves parent/header questions for an assignment that has parent/child questions.
    * 
    * 
    * @param array $where WHERE criteria.
    * @return type Parent questions.
    */
   public function getParentQuestions($where = array())
   {
      $sel = $this->select()->where("question_type = 'parent'");

      foreach ($where as $key => $val) {
         $sel = $sel->where("$key = $val");
      }
      $sel = $sel->order("question_number");
      $sql = $sel->assemble();
      //die($sql);

      $rows = $this->fetchAll($sel)->toArray();

      return $rows;

   }

   /**
    * Returns a single array of parent child questions in the order of 1st parent followed by
    * it's children, 2nd parent followed by it's children, etc.
    * Uses the stuEvalManagementData session values to specify the WHERE and ORDER criteria.
    * 
    * 
    * @param Optional associative array to use in WHERE clause. If not, then this function
    *        uses the session values.
    */
   public function getChildParentQuestions()
   {
       $coopSess = new Zend_Session_Namespace('coop');
       $args = func_get_args();
       // if optional array was passed.
       if (count($args) > 0) {
          $stuEvalData = $args[0];
       // else use session
       } else {
          $stuEvalData = $coopSess->stuEvalManagementData;
          //die(var_dump($stuEvalData));
       }

       $where = array('classes_id' => $stuEvalData['classId'], 'assignments_id' => $stuEvalData['assignId']);

       //die(var_dump($where));
       // get all parent questions for a specific assignment and class.
       $parents = $this->getParentQuestions($where);

       // prepare the paramenter for $as->getQuestions().
       $assignId = $where['assignments_id'];
       unset($where['assignments_id']);

       $as = new My_Model_Assignment();

       // array to hold parent and child questions.
       $combined = array();

       foreach ($parents as $p) {
          $combined[] = $p;
          $where['parent'] = $p['question_number'];
          $children = $as->getQuestions($assignId, $where, 'question_number');

          foreach ($children as $c) {
             $combined[] = $c;
          }
       }

       return $combined;
   }

   /**
    * Checks if a parent question exists for an assignment.
    * 
    * 
    * @param array $where WHERE criteria.
    * @return boolean 
    */
   public function chkParentExistence($where = array())
   {
      $sel = $this->select()->where("question_type = 'parent'");
      
      foreach ($where as $key => $val) {
         if ($key === 'question_number') {
            $sel = $sel->where("$key = '$val'");
         } else {
            $sel = $sel->where("$key = $val");
         }
      }

      $res = $this->fetchRow();

      if (!$res) {
         return false;
      }
      $row = $res->toArray();

      return $row;
   }

}

?>
