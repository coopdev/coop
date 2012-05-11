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
      $coopSess->username = $user['username'];
      $coopSess->role = $db->getCol('coop_roles', 'role', array('id'=>$user['roles_id']));
       
      $semester = new My_Semester();
      $currentSem = $semester->getRealSem();
      $coopSess->currentSemId = $db->getId('coop_semesters', array('semester' => $currentSem));

      if ($coopSess->role == 'user') {

         
         $coopSess->classIds = $db->getCols('coop_users_semesters', 
                                   'classes_id',
                                   array('student'=>$user['username'], 
                                      'semesters_id' => $coopSess->currentSemId));

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

   public function isEnrolled($user)
   {
      $db = new My_Db();

      $id = $db->getCol('coop_users_semesters_view', 'id', 
              array('username' => $user['username'], 'current' => 1));

      if (empty($id)) {
         return false;
      }
      return true;
   }
}

?>
