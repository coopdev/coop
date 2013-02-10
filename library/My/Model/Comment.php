<?php
class My_Model_Comment extends Zend_Db_Table_Abstract
{
   protected $_name = "coop_comments";

   public function create($data)
   {
      $comment = $this->fetchNew();
      $comment->setFromArray($data);
      $comment->date = date('Y-m-d h:i:s');
      $comment->save();
   }
}
?>
