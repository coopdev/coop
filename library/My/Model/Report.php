<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Report
 *
 * @author joseph
 */
class My_Model_Report {
   public $by = "semester";
   public $semId = null;
   public $year = "";


   public function assignmentsReport()
   {
      $db = new My_Db();
      $sql = "SELECT u.username, u.lname, u.fname, c.name AS class_name, c.id AS class_id, sa.id AS submitted_assign_id, a.assignment
                 FROM coop_users u
                 JOIN coop_users_semesters us ON u.username = us.student
                 JOIN coop_classes c ON us.classes_id = c.id
                 LEFT JOIN coop_submittedassignments sa ON (u.username = sa.username AND c.id = sa.classes_id AND sa.is_final = 1)
                 LEFT JOIN coop_assignments a ON sa.assignments_id = a.id";

      if ($this->by === "semester") {
         $semId = $this->semId;
         $sql .= " WHERE us.semesters_id = $semId;";
      } elseif ($this->by === "year") {
         $year = $this->year;
         $Semester = new My_Model_Semester;
         $select = $Semester->select()->where("year = ?", $year);
         $sems = $Semester->fetchAll($select);
         $semIds = array();
         foreach ($sems as $s) {
            $semIds[] = $s->id;
         }
         $semIds = implode($semIds, ', ');
         $sql .= " WHERE us.semesters_id IN ($semIds);";
      }

      $rows = $db->fetchAll($sql);
      return $rows;
   }
}

?>
