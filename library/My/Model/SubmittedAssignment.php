<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SubmittedAssignment
 *
 * @author joseph
 */
class My_Model_SubmittedAssignment extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_submittedassignments";




   /**
    * Queries for assignment status for student, semester, class combination. If not submitted,
    * the left joined table fields will show up as null.
    * 
    * 
    * @param array $data WHERE criteria.
    */
   public function getSubmissionRec(array $data)
   {
      //die(var_dump($data));
      $uname = $data['username'];
      $classes_id = $data['classes_id'];
      $semesters_id = $data['semesters_id'];
      
      $Assignment = new My_Model_Assignment();
      $due_date_column = $Assignment->getDuedateColumn();


      $db = new My_Db();
      $res = $db->fetchAll("SELECT s.username, s.fname, s.lname, 
               sub.id AS submittedassignments_id, sub.semesters_id, sub.classes_id, sub.assignments_id, sub.date_submitted, sub.is_final, 
               a.assignment, a.$due_date_column, a.assignment_num 
               FROM (SELECT fname, lname, username FROM coop_users WHERE username = '$uname') AS s 
               LEFT JOIN coop_submittedassignments AS sub 
                 ON s.username = sub.username and sub.classes_id = $classes_id and sub.semesters_id = $semesters_id and sub.is_final = 1
               RIGHT JOIN coop_assignments AS a 
                 ON sub.assignments_id = a.id 
               ORDER BY a.position_submitted");

      // Format dates for output
      $funcs = new My_Funcs();
      for ($i = 0; $i < count($res); $i++) {
         if (!empty($res[$i]['date_submitted'])) {
            $res[$i]['date_submitted'] = $funcs->formatDateOut($res[$i]['date_submitted']);
            //die($res[$i]['date_submitted']);
         }
         $res[$i][$due_date_column] = $funcs->formatDateOut($res[$i][$due_date_column]);
      }

      //die(var_dump($res));

      return $res;
      
   }

   /**
    * Gets the assignment status for all students in a particular class.
    * 
    * 
    * @param int|string $classId
    * @return string  
    */
   public function getAssignmentStatusByClass($classId, $semId)
   {
      $sem = new My_Model_Semester();

      $class = new My_Model_Class();
      $students = $class->getRollForCurrentSem($classId, $semId);
      if(empty($students)) {
         return "emptyClass";
      }
      //die(var_dump($students));

      $user = new My_Model_User();
      $sel = $user->select()->setIntegrityCheck(false);
      $userTablename = $user->info('name');
      $sel = $sel->from(array('u' => $userTablename))
                 ->joinLeft(array('sa' => $this->_name), 
                      "u.username = sa.username AND sa.semesters_id = $semId AND sa.classes_id = $classId AND sa.is_final = 1",
                      array('assignments_id', 'is_final'));

      foreach($students as $s) {
         $sel = $sel->orWhere('u.username = ?', $s['username']);
         //$sel = $sel->orWhere('u.username = ?', 'hi');
      }
      $sel = $sel->order('u.lname');

      //$sql = $sel->assemble();
      //die(var_dump($sql));

      $rows = $this->fetchAll($sel)->toArray();

      return $rows;

      //die(var_dump($rows));
   }

   /**
    * Checks if an assignment is submitted
    * 
    * 
    * @param array $data
    * @return boolean  
    */
   public function isSubmitted(array $data)
   {
      $where['classes_id'] = $data['classes_id'];
      $where['semesters_id'] = $data['semesters_id'];
      $where['assignments_id'] = $data['assignments_id'];
      $where['username'] = $data['username'];

      if ($this->rowExists($where)) {
         return true;
      }

      return false;

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
      //$keys = array_keys($where);
      //$col = $keys[0];
      //// die($col);
      //$val = $where[$col];
      $query = $this->select()->from($this, array('id'));//->where("$col = ?", $val);
      foreach ($where as $key => $val) {

         $query = $query->where("$key = ?", $val);

      }
      $row = $this->fetchRow($query);
      if ($row instanceof Zend_Db_Table_Row) {
         $row = $row->toArray();
         return $row['id'];
      }
      return false;
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
      $query = $this->select();//->where("$whereCol = ?", $whereVal);
      foreach ($where as $key => $val) {
         if ($key === 'username') {
            $query = $query->where("$key = ?", $val);
         } else {
            $query = $query->where("$key = $val");
         }


      }
      //$sql = $query->assemble();
      //die($sql);
      $row = $this->fetchRow($query);


      if (empty($row)) {
         return false;
      }

      return true;
   }

}

?>
