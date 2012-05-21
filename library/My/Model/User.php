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

      if (empty($res)) {
         $res = array();
      }

      return $res;
      //return $this->fetchAll()->toArray();
   }


   public function getEmpInfo(array $data)
   {
      $username = $data['username'];
      $class = $data['classes_id'];
      $sem = $data['semesters_id'];

      $empInfo = new My_Model_EmpInfo();
      $empInfoTab = $empInfo->info('name');

      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from($empInfoTab)
                 ->where('username = ?', $username)
                 ->where('classes_id = ?', $class)
                 ->where('semesters_id = ?', $sem);

      $rows = $this->fetchAll($res)->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;

      $funcs = new My_Funcs();


   }

   // Queries for semester related information (i.e. Student, Semester, Class, Coordinator etc.)
   // based on submitted criteria from the search form.
   public function searchStudentRecs($criteria)
   {
      $data = $criteria['data'];

      $where['fname'] = trim($data['fname']);
      $where['lname'] = trim($data['lname']);
      $where['username'] = trim($data['username']);
      $where['classes_id'] = trim($data['classes_id']);
      $where['semesters_id'] = trim($data['semesters_id']);
      $where['coordinator'] = trim($data['coordinator']);

      $cols = array('fname', 'lname', 'semester', 'class', 'coordfname', 'coordlname', 'username', 'classes_id', 'semesters_id', 'coordinator');
      $sel = $this->select()->setIntegrityCheck(false);
      $query = $sel->from('coop_users_semesters_view', $cols);

      foreach ($where as $key => $val) {
         trim($where[$key]);
         if (!empty($where[$key])) {
            $query = $query->where("$key = ?", $val);
         }
      }

      $query = $query->order(array("class", "lname"));

      $rows = $this->fetchAll($query)->toArray();

      //die(var_dump($rows));

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;

   }

   // Queries for semester related information (i.e. Student, Semester, Class, Coordinator etc.)
   public function getSemesterInfo(array $data)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $query = $sel->from('coop_users_semesters_view');

      foreach($data as $key => $val) {
         $query = $query->where("$key = ?", $val);
      }

      $rows = $this->fetchAll($query)->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;

   }

   public function getAllCoords()
   {
      $role = new My_Model_Role();
      $sel = $this->select()->setIntegrityCheck(false);
      $res = $sel->from(array('u' => $this->_name))
                 ->join(array('r' => 'coop_roles'), "u.roles_id = r.id", array())
                 ->where("r.role = 'coordinator'");

      $rows = $this->fetchAll($res)->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;

   }

   public function getCoordInfo(array $where = array())
   {
      $pnType = new My_Model_PhoneTypes();
      
      $pnTypeId = $pnType->getHomeId();

      $pn = new My_Model_PhoneNumbers();
      $pnName = $pn->info('name');

      $role = new My_Model_Role();
      $row = $role->fetchRow("role = 'coordinator'")->toArray();
      $coordId = $row['id'];

      $sel = $this->select()->setIntegrityCheck(false);

      $query = $sel->from(array('u' => $this->_name))
                   ->joinLeft(array('pn' => $pnName), "u.username = pn.username AND pn.phonetypes_id = $pnTypeId", array('phonenumber'))
                   ->where('roles_id = ?', $coordId);

      foreach ($where as $key =>$val) {
         $query = $query->where("u.$key = ?", $val);
      }

      $rows = $this->fetchAll($query)->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      //die(var_dump($rows));

      return $rows;

   }


   public function deleteCoord($coord)
   {
      //die(var_dump($coord));
      if ($this->delete("username = '$coord'")) {
         return true;
      }

      return false;

   }

   public function addCoord($data)
   {
      $db = new My_Db();

      $data = $db->prepFormInserts($data, $this);

      $role = new My_Model_Role();
      $roleId = $role->getCoordId();

      $data['roles_id'] = $roleId;

      if ($this->rowExists(array('username' => $data['username']))) {
         return "exists";
      }

      if ($this->insert($data)) {
         return true;
      }

      return false;

   }

   public function editCoord($username, $data)
   {
      $db = new My_Db();

      // prepare coop_users updates
      $userVals = $db->prepFormInserts($data, $this);

      // update coop_users table
      $this->update($userVals, "username = '$username'");


      $pt = new My_Model_PhoneTypes();
      // get id for home phone type
      $ptId = $pt->getHomeId();

      // prepare coop_phonenumbers updates
      $phoneVals['phonenumber'] = $data['phonenumber'];
      $phoneVals['date_mod'] = date('Ymdhis');

      $pn = new My_Model_PhoneNumbers();

      // if coordinator already has a home phone record, do an update
      if ($pn->rowExists(array('username' => $data['username'], 'phonetypes_id' => $ptId))) {
         if ($pn->update($phoneVals, "username = '".$data['username']."' AND phonetypes_id = $ptId")) {
            return true;
         }
      // if not, insert
      } else {
         $phoneVals['username'] = $data['username'];
         $phoneVals['phonetypes_id'] = $pt->getHomeId();
         if ($pn->insert($phoneVals)) {
            return true;
         }
      }

      return false;


      //$data = $userVals + $phoneVals;

      //die(var_dump($data));

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

      $query = $this->select();

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
