<?php
/**
 * Connection to database
 * 
 * USE My_Db Instead
 *
 * @author joseph
 */
class My_DbLink 
{
      
   public static function connect()
   {
      $config = new Zend_Config_Ini(APPLICATION_PATH.
                                   '/configs/application.ini','production');
      
      $link = Zend_Db::factory($config->resources->db);
      return $link;
   }
}
?>
