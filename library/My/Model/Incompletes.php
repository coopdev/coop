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

      $sql = $this->buildStudentQuery();

      $sql .= " WHERE us.status = 'Incomplete' ORDER BY class, u.lname";

      $result = $db->fetchAll($sql);

      return $result;
   }


   public function setMultipleIncompleteSatuses($students, $markIncomplete=true)
   {
      $UsersSem = new My_Model_UsersSemester();

      $ids = "";
      foreach ($students as $std) {
         $ids .= $std .","; 
      }
      $ids = substr($ids, 0, -1);


      $where = $UsersSem->select()->where("id IN(?)", explode(",", $ids));
      $result = $UsersSem->fetchAll($where);

      foreach ($result as $r) {
         $r->status = $markIncomplete ? "Incomplete" : "";
         //if ($markIncomplete) {
         //   $r->status = "Incomplete";
         //} else {
         //   $r->status = "";
         //}
         $r->save();
      }
      //die(var_dump($sel->assemble()));
   }


   public function searchCompletes($criteria)
   {
      $db = new My_Db();

      $sql = $this->buildStudentQuery();

      $sql .= " WHERE us.status != 'Incomplete'";
      foreach ($criteria as $col => $val) {
         if (trim($val) !== "") {
            $sql .= " AND $col = '$val'";
         }
      }
      $sql .= " ORDER BY class, u.lname";

      //die($sql);

      $result = $db->fetchAll($sql);
      return $result;
      
   }


   private function buildStudentQuery()
   {
      $sql = "SELECT us.id AS users_sem_id, u.fname, u.lname, u.username, c.name AS class, s.semester 
               FROM coop_users_semesters AS us 
               JOIN coop_users AS u ON us.student = u.username 
               JOIN coop_classes AS c ON us.classes_id = c.id 
               JOIN coop_semesters AS s ON us.semesters_id = s.id";

      return $sql;
   }
}

?>
