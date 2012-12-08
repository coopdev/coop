<?php

/**
 * Handles actions for the different users of the application.
 *
 * @author joseph
 */
class My_Model_User extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_users';

   
   /**
    * Retrieves all students from the database
    * 
    * 
    * @return array Associative array of student records.
    */
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


   /* Returns role of current user.
    * 
    */
   public function getCurrentRole()
   {
      $coopSess = new Zend_Session_Namespace('coop');

   }


   /**
    * Retrieves a particular student's employment information from the database.
    * 
    *
    * @param array $data The criteria used in the where clause of the SQL query 
    *             (username, classes_id, semesters_id).
    * @return array Associative array of the student's employment records.
    */
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

   }

   /** 
    * Queries for semester related information (i.e. Student, Semester, Class, Coordinator etc.)
    * based on submitted criteria from the search form.
    * 
    *
    * @param array $criteria Criteria to use in the WHERE clause of the SQL
    * @return array Associative array of semester related records.
    */
   public function searchStudentRecs($criteria)
   {
      $data = $criteria['data'];

      $where['fname'] = trim($data['fname']);
      $where['lname'] = trim($data['lname']);
      $where['username'] = trim($data['username']);
      $where['classes_id'] = trim($data['classes_id']);
      $where['semesters_id'] = trim($data['semesters_id']);
      $where['coordinator'] = trim($data['coordinator']);
      //die($where['coordinator']);

      $cols = array('fname', 'lname', 'semester', 'class', 'coordfname', 'coordlname', 'username', 'classes_id', 'semesters_id', 'coordinator', 'sem_status');
      $sel = $this->select()->setIntegrityCheck(false);
      $query = $sel->from('coop_users_semesters_view', $cols);


      foreach ($where as $key => $val) {
         trim($where[$key]);
         if (!empty($where[$key])) {
            $query = $query->where("$key = ?", $val);
         }
      }

      $args = func_get_args();
      if (count($args) > 1 ) {
         $order = $args[1];
         $query = $query->order($order);
      } else {
         $query = $query->order(array("class", "lname"));
      }

      //die(var_dump($query->__toString()));

      //die($query);
      $rows = $this->fetchAll($query)->toArray();

      //die(var_dump($rows));

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;

   }

   /** 
    * Queries for semester related information (i.e. Student, Semester, Class, Coordinator etc.)
    * based on submitted criteria from the search form.
    * 
    *
    * @param array $criteria Criteria to use in the WHERE clause of the SQL
    * @return array Associative array of semester related records.
    */
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

   /**
    * Retrieves all coordinators from the database
    * 
    * 
    * @return array Associative array of coordinator records.
    */
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

   /**
    * Retrieves all student aids from the database
    * 
    * 
    * @return array Associative array of student aids records.
    */
   public function getAllStuAids()
   {
      $role = new My_Model_Role();
      $sel = $this->select()->setIntegrityCheck(false);
      $res = $sel->from(array('u' => $this->_name))
                 ->join(array('r' => 'coop_roles'), "u.roles_id = r.id", array())
                 ->where("r.role = 'studentAid'");

      $rows = $this->fetchAll($res)->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      return $rows;

   }

   /**
    * Retrieves information related to one or more coordinators (e.g. phonenumber, email etc).
    * 
    * 
    * @param array $where Optional criteria for WHERE clause. If none given, then information
    *                     for all coordinators is returned.
    * @return array  Coordinator information.
    */
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
         if ($key === 'username') {
            //$query = $query->where("u.$key = ?", '$val');
         }
         $query = $query->where("u.$key = ?", $val);
      }

      $rows = $this->fetchAll($query)->toArray();

      if (empty($rows)) {
         $rows = array();
      }

      //die(var_dump($rows));

      return $rows;

   }


   /*
    * Fetches all incomplete students for a given class.
    */
   public function getIncompletes($classId)
   {
      $select = $this->select()->setIntegrityCheck(false);

      $select->from('coop_incompletes_view');

      if (!empty($classId)) {
         $select->where('classes_id = ?', $classId);
      }

      $rows = $this->fetchAll($select)->toArray();

      return $rows;

   }



   /**
    * Deletes one coordinator.
    * 
    * 
    * @param string $coord The username of the coordinator to delete.
    * @return boolean True on success, False on failure.
    */
   public function deleteCoord($coord)
   {
      //die(var_dump($coord));
      if ($this->delete("username = " . $this->_db->quote($coord) )) {
         return true;
      }

      return false;

   }

   /**
    * Adds a coordinator to the database.
    *
    * 
    * @param array $data The data entered in to the form used to add the coordinator.
    * @return string|boolean The string 'exists' if the coordinator already exists (based on username).
    *                        True on success, False on failure.
    */
   public function addCoord($data)
   {
      $db = new My_Db();

      // filter fields for coop_users
      $userVals = $db->prepFormInserts($data, $this);

      $role = new My_Model_Role();
      // coordinator role id from coop_roles
      $roleId = $role->getCoordId();

      $userVals['roles_id'] = $roleId;
      // make user active
      $userVals['active'] = 1;

      //die(var_dump($userVals));
      // if user with specified username already exists
      if ($this->rowExists(array('username' => $userVals['username']))) {
         return "exists";
      }

      // if insert into coop_users fails
      if (!$this->insert($userVals)) {
         return false;
      }

      // if a phone number was entered through the form (it is optional)
      if (!empty($data['phonenumber'])) {
         //die('hi');
         $pn = new My_Model_PhoneNumbers();
         $pt = new My_Model_PhoneTypes();
         // just make it a home phone number for simplicity
         $homeId = $pt->getHomeId();
         $pnVals = array('username' => $data['username'], 'phonenumber' => $data['phonenumber'], 'phonetypes_id' => $homeId);

         // if insert fails
         if (!$pn->insert($pnVals)) {
            return false;
         }
      }

      return true;

   }

   /**
    * Updates a coordinator's information in the database.
    * 
    * 
    * @param string $username The coordinator's username.
    * @param array $data The coordinator's updated information passed in from the form.
    * @return boolean True on success, False on failure.
    */
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

   /**
    * Adds a student aid to the database.
    * 
    * 
    * @param array $data Associative array of the student aid's information.
    * @return string|boolean  The string 'exists' if the user already exists, True on success, 
    *                         False on failure.
    */
   public function addStudentAid($data)
   {
      $db = new My_Db();

      // filter fields for coop_users
      $userVals = $db->prepFormInserts($data, $this);

      $role = new My_Model_Role();
      // coordinator role id from coop_roles
      $roleId = $role->getStuAidId();

      $userVals['roles_id'] = $roleId;
      // make user active
      $userVals['active'] = 1;

      //die(var_dump($userVals));
      // if user with specified username already exists
      if ($this->rowExists(array('username' => $userVals['username']))) {
         return "exists";
      }

      // if insert into coop_users fails
      if (!$this->insert($userVals)) {
         return false;
      }

      // if a phone number was entered through the form (it is optional)
      if (!empty($data['phonenumber'])) {
         //die('hi');
         $pn = new My_Model_PhoneNumbers();
         $pt = new My_Model_PhoneTypes();
         // just make it a home phone number for simplicity
         $homeId = $pt->getHomeId();
         $pnVals = array('username' => $data['username'], 'phonenumber' => $data['phonenumber'], 'phonetypes_id' => $homeId);

         // if insert fails
         if (!$pn->insert($pnVals)) {
            return false;
         }
      }

      return true;

   }

   public function editStuAid($username, $data)
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

   
   public function deleteStuAid($stuAid)
   {
      //die(var_dump($coord));
      if ($this->delete("username = " . $this->_db->quote($stuAid) )) {
         return true;
      }

      return false;

   }



   public function getStuAidInfo(array $where = array())
   {
      $pnType = new My_Model_PhoneTypes();
      
      $pnTypeId = $pnType->getHomeId();

      $pn = new My_Model_PhoneNumbers();
      $pnName = $pn->info('name');

      $role = new My_Model_Role();
      $stuAidId = $role->getStuAidId();

      $sel = $this->select()->setIntegrityCheck(false);

      $query = $sel->from(array('u' => $this->_name))
                   ->joinLeft(array('pn' => $pnName), "u.username = pn.username AND pn.phonetypes_id = $pnTypeId", array('phonenumber'))
                   ->where('roles_id = ?', $stuAidId);

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

   public function getStudentInfo($where = array())
   {
      $db = new My_Db();
      $Student = new My_Model_Student();

      $select = $Student->select();

      $select = $db->buildSelectWhereClause($select, $where);

      $row = $Student->fetchRow($select);
      return $row;
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
