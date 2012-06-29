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




   public function getAll()
   {
      return $this->fetchAll(null, "name")->toArray();
   }

   // Returns the name of the class specified by the passed in id
   public function getName($id)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from($this, array('name'))
                 ->where("id = $id");

      $row = $this->fetchRow($res)->toArray();

      return $row['name'];
   }

   // Returns full record for one class
   public function getClass($id)
   {

      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from($this)
                  ->where("id = $id");

      $row = $this->fetchRow($res)->toArray();

      return $row;

   }


   // Edits a classes information, i.e name, coordinator
   public function edit($id, $data)
   {
      $vals['name'] = $data['name'];
      $vals['coordinator'] = $data['coordinator'];
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

   public function create($data)
   {
      if (empty($data['coordinator'])) {
         $data['coordinator'] = null;
      }

      if ($this->rowExists(array('name' => $data['name']))) {
         return "exists";
      }

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
    * Gets all the students enrolled in a specified class for the current semester
    * 
    * @param $id - the class id
    * @param Optional ORDER clause
    * 
    */

   public function getRollForCurrentSem($id)
   {
      $sel = $this->select()->setIntegrityCheck(false);

      $res = $sel->from('coop_users_semesters_view')
          ->where("classes_id = $id")
          ->where("current = 1");

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