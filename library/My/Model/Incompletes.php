<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Incompletes
 *
 * @author joseph
 */
class My_Model_Incompletes 
{


   public function fetchAll()
   {
      $db = new My_Db();

      $sql = "SELECT us.id AS users_sem_id, u.fname, u.lname, u.username, c.name AS class, s.semester 
               FROM coop_users_semesters AS us 
               JOIN coop_users AS u ON us.student = u.username 
               JOIN coop_classes AS c ON us.classes_id = c.id 
               JOIN coop_semesters AS s ON us.semesters_id = s.id 
               WHERE us.status = 'Incomplete' ORDER BY class, u.lname";

      $result = $db->fetchAll($sql);

      return $result;
   }


   public function removeMultipleIncompleteSatuses($incompletes)
   {
      $UsersSem = new My_Model_UsersSemester();

      $ids = "";
      foreach ($incompletes as $i) {
         $ids .= $i .","; 
      }
      $ids = substr($ids, 0, -1);


      $where = $UsersSem->select()->where("id IN(?)", explode(",", $ids));
      $result = $UsersSem->fetchAll($where);

      foreach ($result as $r) {
         $r->status = "";
         $r->save();
      }

      //die(var_dump($sel->assemble()));




   }
}

?>
