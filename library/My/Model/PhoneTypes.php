<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhoneTypes
 *
 * @author joseph
 */
class My_Model_PhoneTypes extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_phonetypes';


   public function getHomeId()
   {
      $row = $this->fetchRow("type = 'home'")->toArray();

      return $row['id'];
   }




}

?>
