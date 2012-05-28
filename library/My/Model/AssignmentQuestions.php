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



   public function getLastQuestionNum($assignId)
   {
      $sel = $this->select()->where("assignments_id = $assignId")->order("question_number DESC")->limit(1);

      $row = $this->fetchRow($sel);

      $num = 0;

      if (!is_null($row)) {
         $row = $row->toArray();
         $num = $row['question_number'];
      }

      return $num;
   }


}

?>
