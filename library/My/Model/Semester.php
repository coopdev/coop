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
   
   /**
    * NOT BEING USED ANYMORE.
    * 
    * Gets the real life current semester by comparing the current date timestamp with 
    * other specific timestamps that separate the semesters.
    * 
    * 
    * @return string  The current real life semester
    */
   public function getRealSem()
   {
      date_default_timezone_set('US/Hawaii');
      $curDate = date('Y-m-d');
      $dateParts = explode('-',$curDate);
      $curYear = $dateParts[0];
      $curDate = strtotime(date('Ymd')); // current date timestamp
      $curMonth = $dateParts[1];


      $sprSummer = strtotime($curYear."0520");
      $sumFall = strtotime($curYear."0820");
      //die(var_dump($curDate));
      //die(var_dump($curDate > $sprSummer));

      if ($curDate < $sprSummer) {
         $this->curSem = "Spring";
      } else if ($curDate > $sumFall) {
         $this->curSem = 'Fall';
      } else {
         $this->curSem = 'Summer';
      }
      
      //if ($curMonth < 7) {
      //   $this->curSem = 'Spring';
      //} else {
      //   $this->curSem = 'Fall';
      //}
      
      $this->curSem .= ' ' . $curYear;
      //die($this->curSem);
      
      return $this->curSem;
   }

   /**
    * NOT BEING USED ANYMORE
    * 
    * Updates the database and sets the proper semester to the current one.
    */
   public function setCurrentSem()
   {
      // Get currest semester.
       $curSemester = $this->getRealSem();
       //die($curSemester);
       
       $db = new My_Db();

       // Get the semester that the database thinks is the current semester.
       $sem = $db->getCol('coop_semesters', 'semester', array('current'=>1));
       //die($sem);

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

   /**
    * Increments the current semester. 
    */
   public function nextSem()
   {
      $db = $this->getAdapter();

      try {
         $db->query("CALL next_sem()");
         $coopSess = new Zend_Session_Namespace('coop');
         $coopSess->currentSemId = $this->getCurrentSemId();
         return true;
      } catch(Exception $e) {
         return false;
      }

   }

   /**
    * Decrements the current semester. 
    */
   public function prevSem()
   {
      $db = $this->getAdapter();

      try {
         $db->query("CALL prev_sem()");
         $coopSess = new Zend_Session_Namespace('coop');
         $coopSess->currentSemId = $this->getCurrentSemId();
         return true;
      } catch(Exception $e) {
         return false;
      }
   }

   /**
    * Retrieves the current semesters id.
    * 
    * 
    * @return int The current semesters id. 
    */
   public function getCurrentSemId()
   {
      return $this->getId(array('current' => 1));
   }

   /**
    * Gets semester in database from first to current
    * 
    * @param $limit Optional limit.
    * 
    * @return type  
    */
   public function getUpToCurrent($limit = null)
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

      $select = "SELECT s.semester, s.id, s.current FROM 
                            (SELECT * FROM coop_semesters LIMIT $c) AS s 
                            ORDER BY SUBSTRING_INDEX(semester, ' ', -1) DESC, 
                            SUBSTRING_INDEX(semester, ' ', 1)";

      if (!is_null($limit)) {
         $select .= " LIMIT $limit ";
      }

      $rows = $db->fetchAll($select);

      $temp = array(); // hold record to be swapped.
      $tempPos = ""; // hold position for swapping.
      $ind = 0;
      // swap the order of Spring and Summer since we want summer to come first in this case.
      foreach ($rows as $r) {
         $tokens = explode(' ', $r['semester']);
         if ($tokens[0] === "Spring") {
            $temp = $r;
            $tempPos = $ind;
         } else if ($tokens[0] === "Summer") {
            $rows[$tempPos] = $r;
            $rows[$ind] = $temp;
         }
         $ind++;
      }
      //die(var_dump($rows));

      return $rows;
   }


   /*
    * Sets a student status such as incomplete.
    */
   public function setStudentStatus($status, $where)
   {
      $usersSem = new My_Model_UsersSemester();

      $db = new My_Db();

      $where['student'] = $where['username'];
      unset($where['username']);
      $where = $db->buildArrayWhereClause($where);
      //die(var_dump($status, $where));
      try {
         $usersSem->update(array('status' => $status), $where);
      } catch (Exception $e) {
         die(var_dump('error'));
      }

   }

   /*
    * Determines if a student has an incomplete status for a semester.
    *
    * If the student does have incompletes, this returns the semester
    * ID and class ID(s) which are incomplete.
    * 
    * USED IN: My_Funcs->setSessions()
    * 
    */
   public function incompleteData($where = array())
   {
      $usersSem = new My_Model_UsersSemester();

      $select = $usersSem->select();
      $db = new My_Db();
      //die(var_dump($where));
      $select = $db->buildSelectWhereClause($select, $where);
      $select->where("status = ?", "Incomplete");

      $rows = $usersSem->fetchAll($select)->toArray();

      if (empty($rows)) {
         return false;
      }

      $data['semId'] = $rows[0]['semesters_id'];
      $data['classIds'] = array();
      foreach ($rows as $r) {
         $data['classIds'][] = $r['classes_id'];
      }


      return $data;

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
