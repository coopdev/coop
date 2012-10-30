<?php
   require_once('../dbconn.php');
   
   $query = $link->prepare('INSERT INTO coop_semesters (semester) 
                  VALUES (?)');
   
   $year = 2008;
   
   for ($i = $year; $i < 2051; $i++) {
      $query->execute(array("Spring $i"));  
      $query->execute(array("Summer $i"));  
      $query->execute(array("Fall $i"));
   }
?>