<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Class
 *
 * @author joseph
 */
class My_Model_Class extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_classes";




   /** 
    * Retrieves all class names.
    * 
    * 
    * @return array All class names
    */
   public function getAll()
   {
      return $this->fetchAll(null, "name")->toArray();
   }

   public function getMajors()
   {
      $db = $this->getAdapter();

      $rows = $db->query("CALL get_majors()")->fetchAll();

      $majors = array();
      foreach ($rows as $r) {
         $majors[] = $r['major'];
      }

      return $majors;
   }

   public function getClassInfo($where)
   {
      $db = new My_Db();
      $select = $this->select()->setIntegrityCheck(false);
      $select->from('coop_classinfo_view');
      $select = $db->buildSelectWhereClause($select, $where);

      $rows = $this->fetchRow($select);

      if (count($rows) < 1) {
         $rows = array();
      }
      //die(var_dump($rows));
      return $rows;
   }

   /**
    * Retrieves the class name given a specific ID.
    * 
    * 
    * @param int|string $id
    * @return string Class name 
    */
   public function getName($id)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from($this, array('name'))
                 ->where("id = $id");

      $row = $this->fetchRow($res)->toArray();

      return $row['name'];
   }

   /**
    * Retrieves full record for one class
    * 
    * 
    * @param int|string $id
    * @return array Class record  
    */
   public function getClass($id)
   {

      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from($this)
                  ->where("id = $id");

      $row = $this->fetchRow($res)->toArray();

      return $row;

   }


   /**
    *  Edits a classes information, i.e name, coordinator
    * 
    * 
    *  @param int|string $id ID of class to edit.
    *  @param array $data Data to be updated.
    */
   public function edit($id, $data)
   {
      $vals['name'] = $data['name'];
      $vals['major'] = $data['major'];
      $vals['coordinator'] = $data['coordinator'];
      $vals['level'] = $data['level'];
      if (empty($vals['coordinator'])) {
         $vals['coordinator'] = null;
      }

      // This check was causing problems when the name of the class wasn't changed (because 
      // the name already exists if it wasn't changed...).
      //if ($this->rowExists(array('name' => $vals['name']))) {
      //   return "exists";
      //}

      if ($this->update($vals, "id = $id")) {
         return true;
      }

   }

   /**
    * Adds a new class to the database.
    * 
    * 
    * @param array $data Data for the class to be added.
    * @return string|boolean The string 'exists' if the class already exists, True on 
    *                        success, false on failure.
    */
   public function create($data)
   {
      if (empty($data['coordinator'])) {
         $data['coordinator'] = null;
      }

      if ($this->rowExists(array('name' => $data['name']))) {
         return "exists";
      }

      // Make class name upper case.
      $data['name'] = strtoupper($data['name']);

      $id = $this->insert($data);

      $syl = new My_Model_Syllabus();
      $finalId = $syl->addFinal($id);
      $draftId = $syl->addDraft($id);

      if ($id != false && $finalId === true && $draftId === true) {
         return true;
      } else {
         return false;
      }

   }

   /* 
    * Determines if a class is 193 or above or not.
    */
   public function is100AndAbove($classId)
   {
       $classRow = $this->getClass($classId);
       // Add learning outcomes.
       if ($classRow['level'] === 'upper') {
          return true;
       }

       return false;
      
   }

   public function isFire193V($id) 
   {
       $row = $this->fetchRow("id = $id");

       if ($row->name === "FIRE 193V") {
           return true;
       }

       return false;
   }


   /**
    * Gets all the students enrolled in a specified class for the current semester
    * 
    * 
    * @param int|string $id  The class id
    * @param Optional ORDER clause
    */
   public function getRollForCurrentSem($classes_id, $semesters_id)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from('coop_users_semesters_view')
          ->where("classes_id = $classes_id")
          //->where("current = 1");
          ->where("semesters_id = $semesters_id");

      // if optional ORDER clause was passed
      $args = func_get_args();
      if (count($args) > 1) {
         $order = $args[1];
         $res = $res->order($order);
      }

      $recs = $this->fetchAll($res)->toArray();

      if (!is_array($recs)) {
         $recs = array();
      }

      return $recs;
   }

   /**
    * Deletes a student for a specific class for the current semester. Does not delete the
    * student from the database.
    * 
    * 
    * @param array $where WHERE criteria.
    * @return boolean 
    */
   public function dropStudent(array $where)
   {
      $us = new My_Model_UsersSemester();

      //die(var_dump($where));

      $whereStrs = array();
      foreach ($where as $key => $val) {
         if ($key === 'student') {
            $whereStrs[] = "$key = '$val'";
         } else {
            $whereStrs[] = "$key = $val";
         }
      }
      //die(var_dump($whereStrs));

      try {
         $us->delete($whereStrs);
      } catch(Exception $e) {
         return false;
      }
      return true;
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
