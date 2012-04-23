<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of My_Funcs
 *
 * @author joseph
 */
class My_Funcs 
{
   public function setSessions(array $user, Zend_Session_Namespace $coopSess)
   {
      $db = new My_Db();

      $coopSess->inDb = true;
      $coopSess->userId = $user['id'];
      $coopSess->fname = $user['fname'];
      $coopSess->lname = $user['lname'];
       
      if ($coopSess->role == 'user') {
         
         $coopSess->classId = $db->getCol('coop_users_semesters', 
                                   'classes_id',array('users_id'=>$user['id']));

         if ($user['agreedto_contract']) {
            $coopSess->contractStatus = 'contractYes';
         } else {
            $coopSess->contractStatus = 'contractNo';
         }

         if (!$user['active']) {
            $coopSess->role = "notActive";
         }
      }
   }
}

?>
