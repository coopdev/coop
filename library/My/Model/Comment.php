<?php
class My_Model_Comment extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_comments";

   public function create($data)
   {
      date_default_timezone_set('US/Hawaii');
      $comment = $this->fetchNew();
      $comment->setFromArray($data);
      $comment->date = date('Y-m-d h:i:s');

      try {
         $comment->save();
         return true;
      } catch (Exception $e) {
         return false;
      }
   }

   public function fetch($where)
   {
      date_default_timezone_set('US/Hawaii');
      $db = new My_Db();

      $select = $this->select();
      $select = $db->buildSelectWhereClause($select, $where);

      $rows = $this->fetchAll($select);

      if (count($rows) < 1) {
         return array();
      }

      foreach ($rows as $r) {
         $r->date = date('m-d-Y h:i:s', strtotime($r->date));
      }

      return $rows;
   }

   public function updateComment($comment)
   {
      $cols['comment'] = $comment['comment'];
      $where = "id = " . $comment['id'];
      
      $this->update($cols, $where);

      return true;
   }

   public function destroy($comment)
   {
      $this->delete("id = $comment");

   }

}
?>
