<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logins
 *
 * @author joseph
 */
class My_Model_Logins extends Zend_Db_Table_Abstract
{
   protected $_name = 'coop_logins';


   public function recordLogin($username)
   {
      date_default_timezone_set('US/Hawaii');

      $loginDate = date('Ymdhis');

      //die($loginDate);

      $vals = array('username' => $username, 'login_date' => $loginDate);

      $this->insert($vals);
   }

   public function getLogins($where, $order = "")
   {
      $user = new My_Model_User();

      $sel = $this->select()->setIntegrityCheck(false);

      $sel = $sel->from(array('u' => $user->info('name')))
                 ->join(array('l' => $this->info('name')), "u.username = l.username", 'login_date');

      $funcs = new My_Funcs();

      foreach ($where as $key => $val) {
         $val = trim($val);
         if (!empty($val)) {
            if ($key === 'startDate') {
               $val = $funcs->formatDateIn($val);
               $sel = $sel->where("l.login_date > $val");

            } else if ($key === 'endDate') {
               $val = $funcs->formatDateIn($val);
               $sel = $sel->where("l.login_date < $val");

            } else {
               $sel = $sel->where("u.$key = '$val'");

            }

         }
      }

      if (!empty($order)) {
         $sel = $sel->order($order);
      }

      $sql = $sel->assemble();
      //return $sql;

      $rows = $this->fetchAll($sel)->toArray();

      // format dates for output
      $temp = array();
      foreach ($rows as $r) {
         $date = $r['login_date'];

         $pieces = explode(' ', $date);
         $date = $pieces[0];
         $date = $funcs->formatDateOut($date);
         $r['login_date'] = $date . ' ' . $pieces[1];
         $temp[] = $r;
      }
      $rows = $temp;

      //die(var_dump($rows));

      return $rows;

   }
}

?>
