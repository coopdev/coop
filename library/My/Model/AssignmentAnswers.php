<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AssignmentQuestionAnswers
 *
 * @author joseph
 */
class My_Model_AssignmentAnswers extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_assignmentanswers';




   public function getRows(array $where)
   {
      $sel = $this->select();
      foreach ($where as $key => $val) {
         $sel = $sel->where("$key = ?", $val);
      }
      $rows = $this->fetchAll($sel)->toArray();

      return $rows;
   }
}

?>
