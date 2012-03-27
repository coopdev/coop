<?php

class Application_Model_DbTable_Person extends Zend_Db_Table_Abstract
{

    protected $_name = 'coop_persons';
    
    public function getPerson($uuid)
    {
       $uuid = (int)$uuid;
       $row = $this->fetchRow("uuid = $uuid");
       if (!$row) {
          return false;
       }
       return $row->toArray();
    }
    
    public function addPerson($fname, $lname, $roles_id, $uuid)
    {
       $data = array('fname'=>$fname,
                     'lname'=>$lname,
                     'roles_id'=>$roles_id,
                     'uuid'=>$uuid, 
                     'agreedto_contract'=>1);
       
       $this->insert($data);
    }
    
    


}

