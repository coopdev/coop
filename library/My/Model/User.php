<?php

/**
 * Handles actions for the different users of the application.
 *
 * @author joseph
 */
class My_Model_User extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_users';

   

   public function getAllStudents()
   {
      $role = new My_Model_Role();
      $sel = $this->select()->setIntegrityCheck(false);
      $query = $sel->from($this)
                   ->join("coop_roles", "coop_users.roles_id = coop_roles.id", array())
                   ->where("coop_roles.role = 'user'");

      $res = $this->fetchAll($query)->toArray();
      return $res;
      //return $this->fetchAll()->toArray();
   }

   // Queries for student records based on submitted criteria from the search form.
   // Returns semester related information.
   public function searchStudentRecs($criteria)
   {
      $data = $criteria['data'];

      $where['fname'] = trim($data['fname']);
      $where['lname'] = trim($data['lname']);
      $where['username'] = trim($data['username']);
      $where['classes_id'] = trim($data['classes_id']);
      $where['semesters_id'] = trim($data['semesters_id']);
      $where['coordinator'] = trim($data['coordinator']);

      $cols = array('fname', 'lname', 'semester', 'class', 'coordfname', 'coordlname', 'username');
      $sel = $this->select()->setIntegrityCheck(false);
      $query = $sel->from('coop_users_semesters_view', $cols);

      foreach ($where as $key => $val) {
         trim($where[$key]);
         if (!empty($where[$key])) {
            $query = $query->where("$key = ?", $val);
         }
      }

      $rows = $this->fetchAll($query)->toArray();

      return $rows;

   }

   public function getAllCoords()
   {
      $role = new My_Model_Role();
      $sel = $this->select()->setIntegrityCheck(false);
      $res = $sel->from(array('u' => $this->_name), array('fname', 'lname', 'u.username'))
                 ->join(array('r' => 'coop_roles'), "u.roles_id = r.id", array())
                 ->where("r.role = 'coordinator'");

      $rows = $this->fetchAll($res)->toArray();

      return $rows;

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
