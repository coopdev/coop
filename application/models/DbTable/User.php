<?php

class Application_Model_DbTable_User extends Zend_Db_Table_Abstract
{

    protected $_name = 'coop_users';
    
    public function getUser($uuid)
    {
       $uuid = (int)$uuid;
       $row = $this->fetchRow("uuid = $uuid");
       if (!$row) {
          return false;
       }
       return $row->toArray();
    }
    
    public function addUser($fname, $lname, $roles_id, $uuid)
    {
       $data = array('fname'=>$fname,
                     'lname'=>$lname,
                     'roles_id'=>$roles_id,
                     'uuid'=>$uuid, 
                     'agreedto_contract'=>1);
       
       $this->insert($data);
    }
    
    


}

