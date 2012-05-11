<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Assignment
 *
 * @author joseph
 */
class My_Model_Assignment extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_assignments';




   public function submit(array $data)
   {
      $db = new My_Db();
      $sa = new My_Model_SubmittedAssignment();
      $sem = new My_Model_Semester();
      $data['semesters_id'] = $sem->getCurrentSemId();
      $data['date_submitted'] = date('Ymd');
      $data = $db->prepFormInserts($data, $sa);

      $chk['username'] = $data['username'];
      $chk['classes_id'] = $data['classes_id'];
      $chk['semesters_id'] = $data['semesters_id'];
      $chk['assignments_id'] = $data['assignments_id'];

      if ($this->isSubmitted($chk)) {
         return "submitted";
      }

      $sa->insert($data);

      return true;
   }

   public function isSubmitted(array $data)
   {
      $sa = new My_Model_SubmittedAssignment();

      if ($sa->rowExists($data)) {
         return true;
      }
      
      return false;

   }

   public function getAll()
   {
      $arr = $this->fetchAll()->toArray();
      return $arr;
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

   public function getCols($col, array $where=array())
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

         $query = $query->where("$key = ?", $val);

      }
      $row = $this->fetchRow($query);

      if (empty($row)) {
         return false;
      }

      return true;
   }

}

?>
