<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Jobsites
 *
 * @author joseph
 */
class My_Model_Jobsites extends Zend_Db_Table_Abstract 
{
   protected $_name = 'coop_jobsites';




   public function fetch($where=array())
   {
      $db = new My_Db();
      
      $select = $this->select();
      $select = $db->buildSelectWhereClause($select, $where);
      //die(var_dump($select->assemble()));

      $rows = $this->fetchAll($select);

      return $rows;
   }

   public function fetchLast($where)
   {
      $rows = $this->fetch($where);

      //$rows = new Zend_Db_Table_Rowset();
      $last = $rows->getRow($rows->count() - 1);

      //die(var_dump($last->toArray()));

      return $last;
   }

   public function add($site)
   {
      $row = $this->fetchNew();

      // filter out array keys that don't match row columns.
      $row->setFromArray($site);
      //die(var_dump($row->toArray()));
      $row->save();
   }

   public function edit($values, $where)
   {
      
   }

}

?>
