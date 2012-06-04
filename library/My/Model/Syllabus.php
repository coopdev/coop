<?php


/**
 * Description of Syllabus
 *
 * @author joseph
 */
class My_Model_Syllabus extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_syllabuses";


   public function addFinal($classId)
   {
      $vals = array('classes_id' => $classId, 'final' => true);

      try {
         $this->insert($vals);
      } catch(Exception $e) {
         return "insertFailed";
      }

      return true;

   }

   
   public function addDraft($classId)
   {
      $vals = array('classes_id' => $classId, 'final' => false);

      try {
         $this->insert($vals);
      } catch(Exception $e) {
         return "insertFailed";
      }

      return true;

   }

   public function getDraft($classId)
   {
      $res = $this->fetchRow("classes_id = $classId AND final = 0");

      //die(var_dump($res));

      if (empty($res)) {
         //die('hi');
         return false;
      }

      $row = $res->toArray();
      $syl = $row['syllabus'];
      return $syl;

   }

   public function getFinal($classId)
   {
      $res = $this->fetchRow("classes_id = $classId AND final = 1");

      //die(var_dump($res));

      if (empty($res)) {
         //die('hi');
         return false;
      }

      $row = $res->toArray();
      $syl = $row['syllabus'];
      return $syl;
   }

   public function editFinal($data)
   {
      $vals['syllabus'] = $data['syllabus'];
      $classId = $data['classId'];

      try {

         $this->update($vals, "classes_id = $classId AND final = 1");

      } catch(Exception $e) {

         return false;

      }

   }

   public function editDraft($data)
   {
      $vals['syllabus'] = $data['syllabus'];
      $classId = $data['classId'];

      try {

         $this->update($vals, "classes_id = $classId AND final = 0");

      } catch(Exception $e) {

         return false;

      }
   }

}

?>
