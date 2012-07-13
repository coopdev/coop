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
         $lastBackup = $all->getRow($all->count()-1);
         //die(var_dump($lastBackup));

         $this->destroy(array($lastBackup->id));
      }

      //die(var_dump($backupPath, $backupName));

      $this->insert(array('name' => $backupName,
                          'date' => new Zend_Db_Expr("NOW()")));

      //exec("mysqldump coop | gzip > $backupPath/$backupName.gz");
      exec("mysqldump -u $this->dbUser -p$this->dbPass $this->dbName | gzip > $backupPath/$backupName.gz");

   }

   public function restore($backupName)
   {

      $backupPath = $this->backupPath;

      exec("gunzip < $backupPath/$backupName.gz | mysql -u $this->dbUser -p$this->dbPass $this->dbName");
      
         

   }

   /**
    *
    * @param array $ids Indexed array with the IDs of the backups.
    */
   public function destroy($ids)
   {
      $backupPath = $this->backupPath;
       
      // string to hold all the IDs used in the SQL IN() function.
      $in = 'IN(';

      // populate IN() function with IDs
      for ($i = 0; $i < count($ids); $i++) {
         $id = $ids[$i];
         $in .= "$id";
         if ($i !== count($ids)-1) {
            $in .= ",";
         } else {
            $in .= ")";
         }
      }


      // get backups so we can use the name to delete the actual backup file
      // from the filesystem.
      $backups = $this->fetchAll("id $in");
      foreach ($backups as $b) {
         $backupName = $b->name;
         exec("rm $backupPath/$backupName.gz");
      }

      // delete from database
      $this->delete("id $in");
   }


   public function getAll($opts = array())
   {
      $order = "date DESC";
      if (isset($opts['order'])) {
         $order = $opts['order'];
      }

      return $this->fetchAll(null, $order);
   }

   public function getCount()
   {
      $sel = $this->select()->setIntegrityCheck(false);
      $sel = $sel->from($this, array('count' => "COUNT(*)"));
      $row = $this->fetchRow($sel);

      return $row->count;

   }

}

?>
