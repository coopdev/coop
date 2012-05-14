<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Major
 *
 * @author joseph
 */
class My_Model_Major extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_majors';


   public function getAll()
   {
      return $this->fetchAll()->toArray();
   }
}

?>
