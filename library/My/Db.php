<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Db
 *
 * @author joseph
 */
class My_Db extends Zend_Db_Adapter_Pdo_Mysql
{

   public function __construct()
   {
      $config = new Zend_Config_Ini(APPLICATION_PATH.
                                   '/configs/application.ini','production');
      parent::__construct($config->resources->db->params);

      //$this->link = Zend_Db::factory($config->resources->db);
   }

   public function getRowById($table, $id)

   {
      $query = $this->query("SELECT * FROM $table WHERE id = $id");
      $row = $query->fetch();
      return $row;
   }

   public function getRow($table, array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
     // die($col);
      $val = $where[$col];
      $query = $this->select()->from($table)->where("$col = ?", $val);
      $row = $this->fetchRow($query);
      return $row;
   }

   public function getRows($table, array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
     // die($col);
      $val = $where[$col];
      $query = $this->select()->from($table)->where("$col = ?", $val);
      $rows = $this->fetchAll($query);
      return $rows;
   }

   public function getId($table, array $where)
   {
      $keys = array_keys($where);
      $col = $keys[0];
     // die($col);
      $val = $where[$col];
      $query = $this->select()->from($table, array('id'))->where("$col = ?", $val);
      $id = $this->fetchOne($query);
      return $id;
   }

   public function getCol($table, $col, array $where)
   {
      $keys = array_keys($where);
      $whereCol = $keys[0];
      $whereVal = $where[$whereCol];
      //die(var_dump($whereCol, $whereVal, $col));
      $query = $this->select()->from($table, array($col))->where("$whereCol = ?", $whereVal);
      $col = $this->fetchOne($query);
      return $col;

   }

   public function rowExists($table, array $where)
   {
      $keys = array_keys($where);
      $whereCol = $keys[0];
      $whereVal = $where[$whereCol];

      $query = $this->select()->from($table)->where("$whereCol = ?", $whereVal);
      $row = $this->fetchRow($query);

      if (empty($row)) {
         return false;
      }

      return true;
   }
   
   /*
    * compares the data submitted from a form against the columns in a table
    * so that an associative array can be returned containing only the data that
    * should be involved in a database query. This associative array can then be 
    * used in, for example, the insert() method for easy inserts.
    * 
    * @param $data - the data submited from the form.
    * @param $table - the table which the data will be inserted to
    * 
    * @return - an associative array whose keys match the columns of the table
    *           that will be operated on. 
    */
   public function prepFormInserts($data, $table)
   {
      $cols = $this->describeTable($table);
      $cols = array_keys($cols);
      $vals = array();

      // compare names of posted form elements against table columns
      foreach ($data as $key => $val) {
         if (in_array($key, $cols)) {
            $vals[$key] = $val;
         }
      }

      return $vals;
   }


      
}

?>
