<?php

/**
 * Description of Semester
 *
 * @author joseph
 */
class My_Model_Semester extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_semesters";
   private $curSem = null;
   
   // Gets the real life current semester.
   public function getRealSem()
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

   // Sets the proper semester in the database to current when the real life semester changes.
   public function setCurrentSem()
   {
      // Get currest semester.
       $curSemester = $this->getRealSem();
       
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

   public function getCurrentSemId()
   {
      return $this->getId(array('current' => 1));
   }

   // Gets semester in database from first to current
   public function getUpToCurrent()
   {
      $sems = $this->getAll();

      $c = 0;

      foreach ($sems as $s) {
         $c++;

         if ($s['current']) {
            break;
         }
      }
      $db = new My_Db();

      $rows = $db->fetchAll("SELECT s.semester, s.id, s.current FROM 
                            (SELECT * FROM coop_semesters LIMIT $c) AS s 
                            ORDER BY SUBSTRING_INDEX(semester, ' ', -1) DESC, 
                            SUBSTRING_INDEX(semester, ' ', 1)");
      return $rows;
   }

   public function getAll()
   {
      return $this->fetchAll()->toArray();
   }







   public function getRow(array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
      $val = $where[$col];
      $query = $this->select()->where("$col = ?", $val);
      $row = $this->fetchRow($query);
      $row = $row->toArray();
      return $row;
   }

   public function getRowById($id)
   {
      $row = $this->fetchRow("id = $id");
      $row = $row->toArray();
      return $row;
   }

   public function getRows(array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
     // die($col);
      $val = $where[$col];
      $result = $this->select()->where("$col = ?", $val);
      $rows = $this->fetchAll($result);
      $rows = $rows->toArray();
      return $rows;
   }

   public function getId(array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
     // die($col);
      $val = $where[$col];
      $query = $this->select()->from($this, array('id'))->where("$col = ?", $val);
      $row = $this->fetchRow($query);
      //die(var_dump($row));
      $row = $row->toArray();
      return $row['id'];
   }

   public function getCol($col, array $where)
   {
      $query = $this->select()->from($this, array($col));
      foreach ($where as $key => $val) {

         $query = $query->where("$key = ?", $val);

      }
      $row = $this->fetchRow($query);
      $row = $row->toArray();
      return $row["$col"];

   }

   public function getCols($col, array $where)
   {
      $query = $this->select()->from($this, $col);
      foreach ($where as $key => $val) {

         $query = $query->where("$key = ?", $val);

      }
      $result = $this->fetchAll($query);
      $rows = $result->toArray();

      foreach ($rows as $r) {
         $vals[] = $r[$col];
      }
      die(var_dump($vals));

      return $vals;
   }

   public function rowExists(array $where)
   {
      $keys = array_keys($where);
      $whereCol = $keys[0];
      $whereVal = $where[$whereCol];

      $query = $this->select()->where("$whereCol = ?", $whereVal);
      $row = $this->fetchRow($query);

      if (empty($row)) {
         return false;
      }

      return true;
   }
}

?>
