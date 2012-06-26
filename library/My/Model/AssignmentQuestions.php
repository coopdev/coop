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


   /*
    * @param $assignId - The assignments id
    * @param $opts - Additional data to specifiy a questions (e.g. class id)
    * 
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

   /*
    * Returns a single array of parent child questions in the order of 1st parent followed by
    * it's children, 2nd parent followed by it's children, etc.
    * Uses the stuEvalManagementData session values to specify the WHERE and ORDER criteria.
    * 
    * @param Optional associative array to use in WHERE clause. If not passes, then this function
    *        uses the session values.
    */
   public function getChildParentQuestions()
   {
       $coopSess = new Zend_Session_Namespace('coop');
       $args = func_get_args();
       // if optional array was passed.
       if (count($args > 0)) {
          $stuEvalData = $args[0];
       // else use session
       } else {
          $stuEvalData = $coopSess->stuEvalManagementData;
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
