<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhoneNumbers
 *
 * @author joseph
 */
class My_Model_PhoneNumbers extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_phonenumbers';



   public function rowExists(array $where)
   {

      $query = $this->select();

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
