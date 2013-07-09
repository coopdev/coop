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
   public $year = null;
   public $reportPeriod = "Report Period: ";


   public function __construct($criteria=array()) 
   {
      if (!empty($criteria)) {
         $this->by = $criteria['by'];

         if ($this->by === "semester") {
            $this->semId = $criteria['semesters_id'];
            $Semester = new My_Model_Semester();
            $this->reportPeriod .= $Semester->fetchRow("id = " . $this->semId)->semester;
         } else {
            $this->year = $criteria['year'];
            $this->reportPeriod .= $this->year;
         }
      } else {
         throw new Exception("Array can't be empty");
      }
      
   }


   public function assignments()
   {
      $db = new My_Db();
      $sql = "SELECT u.username, u.lname, u.fname, us.semesters_id, c.name AS class_name, c.id AS class_id, sa.assignments_id, sa.date_submitted
                 FROM coop_users u
                 JOIN coop_users_semesters us ON u.username = us.student
                 JOIN coop_classes c ON us.classes_id = c.id
                 LEFT JOIN coop_submittedassignments sa 
                    ON (u.username = sa.username AND c.id = sa.classes_id AND us.semesters_id = sa.semesters_id AND sa.is_final = 1 )";
                 
      $sql = $this->addConditionsTo($sql);

      $sql .= " ORDER BY class_name, lname;";

      $rows = $db->fetchAll($sql);
      return $rows;
   }

   public function employerSatisfaction()
   {
      $db = new My_Db();
      $Assignment = new My_Model_Assignment();
      $supervEvalId = $Assignment->getSupervisorEvalId();

      // Replace sa.assignments_id with actual ID of supervisor eval assignment.
      $sql = "SELECT u.username, u.lname, u.fname, us.semesters_id, c.name AS class_name, 
                     c.id AS class_id, sa.assignments_id, aa.answer_text, aa.static_question 
              FROM coop_users u 
              JOIN coop_users_semesters us ON u.username = us.student 
              JOIN coop_classes c ON us.classes_id = c.id 
              LEFT JOIN coop_submittedassignments sa ON 
                 (u.username = sa.username 
                  AND c.id = sa.classes_id 
                  AND us.semesters_id = sa.semesters_id 
                  AND sa.is_final = 1 
                  AND sa.assignments_id = $supervEvalId) 
              LEFT JOIN coop_assignmentanswers aa ON
                 (sa.id = aa.submittedassignments_id 
                  AND aa.static_question IN('supervisor', 'comments', 'overall_eval')) ";

      $sql = $this->addConditionsTo($sql);

      $sql .= " ORDER BY class_name, lname;";

      $rows = $db->fetchAll($sql);
      return $rows;

   }


   public function completionRateForAllMajors()
   {
      $db = new My_Db();

      $sql = "SELECT count(*) AS count FROM coop_users_semesters AS us"; 
      $sql = $this->addConditionsTo($sql);
      $result = $db->fetchRow($sql);
      $totalCount = (int) $result['count'];

      $sql .= " AND status != 'Incomplete'";
      $result = $db->fetchRow($sql);
      $completionCount = (int) $result['count'];

      return array("totalCount" => $totalCount, "completionCount" => $completionCount);

      //$percent = round( ($completionCount / $totalCount) * 100 );
      //$percent .= "%";

   }


   private function addConditionsTo($sql, $opts = array())
   {
      if ($this->by === "semester") {
         $semId = $this->semId;
         $sql .= " WHERE us.semesters_id = $semId";
      } elseif ($this->by === "year") {
         $year = $this->year;
         $Semester = new My_Model_Semester();
         $select = $Semester->select()->where("year = ?", $year);
         $sems = $Semester->fetchAll($select);
         $semIds = array();
         foreach ($sems as $s) {
            $semIds[] = $s->id;
         }
         $semIds = implode($semIds, ', ');
         $sql .= " WHERE us.semesters_id IN ($semIds)";
      }


      return $sql;

   }
}

?>
