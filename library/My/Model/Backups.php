<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Backups
 *
 * @author joseph
 */
class My_Model_Backups extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_backups';

   protected $backupPath = '';
   protected $backupList = '';

   protected $dbParams = '';
   protected $dbPass = '';
   protected $dbUser = '';
   protected $dbName = '';

   public function init() 
   {
      // config used to connect to database using mysqldump.
      $config = new Zend_Config_Ini(APPLICATION_PATH.
                                   '/configs/application.ini','production');
      $this->dbParams = $config->resources->db->params;
      $this->dbPass = $this->dbParams->password;
      $this->dbUser = $this->dbParams->username;
      $this->dbName = $this->dbParams->dbname;

      $this->backupPath = APPLICATION_PATH . "/../backups";
      $this->backupList = "$this->backupPath/list.json";
   }

   //public function __construct() 
   //{
   //   $this->backupPath = APPLICATION_PATH . "/../backups";

   //}

   public function backup()
   {

      $backupPath = $this->backupPath;
      $backupName = date('Y-m-d_h:i:s');

      // Get count of total backups
      $count = $this->getCount();

      // Check the amount of backups before adding a new one. 
      // If there are 10 or more backups, then delete the last one to keep a max of 10.
      if ($count >= 10) {
         $all = $this->getAll();

         $lastBackup = $all[count($all)-1];

         $this->destroy(array($lastBackup->name));
      }

      $backups = (array)$this->getAll();
      //die(var_dump($backups));

      if (empty($backups)) {
         $backups = array();
      }

      array_unshift($backups, array('name' => $backupName, 'date' => date('Y-m-d h:i:s')));
      //die(var_dump($backups));

      $json = json_encode($backups);
      //die(var_dump($json));

      file_put_contents($this->backupList, $json);

      // The --routines and --add-drop-table options for mysqldump ensures preserving routines and views respectively.
      // Without --add-drop-tables, views will be restored as tables.
      exec("mysqldump -u $this->dbUser -p$this->dbPass $this->dbName | gzip > $backupPath/$backupName.gz");

   }

   public function restore($backupName)
   {

      $backupPath = $this->backupPath;

      exec("gunzip < $backupPath/$backupName.gz | mysql -u $this->dbUser -p$this->dbPass $this->dbName");
      //exec("mysql -u $this->dbUser -p$this->dbPass $this->dbName < $backupPath/$backupName");
         
   }

   /**
    *
    * @param array $names Indexed array with the names of the backups.
    */
   public function destroy($names)
   {
      $backupPath = $this->backupPath;

      $backups = $this->getAll();

      $i = 0;
      foreach ($backups as $b) {

         if (in_array($b->name, $names)) {

            unset($backups[$i]);

            exec("rm $backupPath/$b->name.gz");

            // array_splice doesn't work because it re-orders the indices and $i becomes unaligned
            //array_splice($backups, $i, 1);
         }

         $i++;
      }

      //die(var_dump($backups));

      // In order to get the json format that I want (which is a json array, not a json object),
      // the indices of the array must be sequential. But after using unset() on one or more
      // elements, the indices may be out of order, so using array_values() will reorder them.
      $json = json_encode(array_values($backups));

      //die(var_dump($json));
      file_put_contents($this->backupList, $json);


   }


   public function getAll($opts = array())
   {
      $json = file_get_contents("$this->backupList");

      if (empty($json)) {
         $json = "{}";
      }

      $backups = json_decode($json);
      return (array)$backups;
   }

   public function getCount()
   {
      $backups = $this->getAll();
      //die(var_dump(count($backups)));

      return count($backups);
   }

}

?>
