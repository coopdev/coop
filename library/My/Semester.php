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
}

?>
