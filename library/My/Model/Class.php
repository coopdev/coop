<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Class
 *
 * @author joseph
 */
class My_Model_Class extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_classes";




   public function getAll()
   {
      return $this->fetchAll()->toArray();
   }

   // Returns the name of the class specified by the passed in id
   public function getName($id)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from($this, array('name'))
                  ->where("id = $id");

      $row = $this->fetchRow($res)->toArray();

      return $row['name'];
   }


   // Gets all the students enrolled in a specified class for the current semester
   public function getRollForCurrentSem($id)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from('coop_users_semesters_view', array('fname','lname','username'))
          ->where("classes_id = $id")
          ->where("current = 1");

      $recs = $this->fetchAll($res)->toArray();

      return $recs;
   }

}
?>
