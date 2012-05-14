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

          $funcs = new My_Funcs();

          // If user is not enrolled for the current semester, deny access
          if (!$funcs->isEnrolled($user)) {
             $coopSess->role = "notEnrolled";
             $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
             $redirector->gotoSimple('access-denied', 'pages');
          }

         $coopSess->classIds = $db->getCols('coop_users_semesters', 
                                   'classes_id',
                                   array('student'=>$user['username'], 
                                   'semesters_id' => $coopSess->currentSemId));

         if (!$user['active']) {
            $coopSess->role = "notActive";
         }
      }
   }

   public function isEnrolled($user)
   {
      $db = new My_Db();

      $coopSess = new Zend_Session_Namespace('coop');

      $id = $db->getCol('coop_users_semesters_view', 'id', 
              array('username' => $user['username'], 'current' => 1));

      if (empty($id)) {
         return false;
      }
      return true;
   }
}

?>
