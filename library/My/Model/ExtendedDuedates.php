<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExtendedDuedates
 *
 * @author joseph
 */
class My_Model_ExtendedDuedates extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_extended_duedates';



   public function getDuedate($where)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $sel = $sel->from($this->_name, 'due_date');

      foreach ($where as $key => $val) {
         if ($key === 'username') {
            $sel = $sel->where("$key = '$val'");
         } else {
            $sel = $sel->where("$key = $val");
         }

      }
      $sql = $sel->assemble();

      $rec = $this->fetchRow($sel);
      if (empty($rec)) {
         return false;
      }
      $rec = $rec->toArray();

      return  $rec['due_date'];

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
