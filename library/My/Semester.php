<?php

/**
 * Description of Semester
 *
 * @author joseph
 */
class My_Semester 
{
   private $curSem = null;
   
   public function getCurrentSem()
   {
      date_default_timezone_set('US/Hawaii');
      $curDate = date('Y-m-d');
      $dateParts = explode('-',$curDate);
      $curYear = $dateParts[0];
      $curMonth = $dateParts[1];
      
      if ($curMonth < 7) {
         $this->curSem = 'Spring';
      } else {
         $this->curSem = 'Fall';
      }
      
      $this->curSem .= ' ' . $curYear;
      
      return $this->curSem;
   }

   public function setCurrentSem()
   {
      // Get currest semester.
       $curSemester = $this->getCurrentSem();
       
       $db = new My_Db();

       // Get the semester that the database thinks is the current semester.
       $sem = $db->getCol('coop_semesters', 'semester', array('current'=>1));

       // If the current semester is not set as current in the database, it means the semester 
       // has changed and the database needs to update the current semester.
       if ($curSemester != $sem) {
          $db->update('coop_semesters', array('current'=>0), 'current = 1');

          $db->update('coop_semesters', array('current'=>1), "semester = '$curSemester'");

          // Because it is a new semester, deactivate users so they have to accept the 
          // disclaimer again.
          $db->update('coop_users', array('active'=>0));
       }

   }
}

?>
