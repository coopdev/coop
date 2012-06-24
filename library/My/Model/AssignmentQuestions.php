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
      


}

?>
