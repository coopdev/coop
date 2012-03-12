<?php

class Application_Model_DbTable_Role extends Zend_Db_Table_Abstract
{

    protected $_name = 'coop_roles';
    
    public function getRole($id)
    {
       $id = (int)$id;
       $row = $this->fetchRow("id = $id");
       if (!$row) {
          return false;
       }
       return $row->toArray();
    }


}

