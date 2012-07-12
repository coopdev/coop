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
   /**
    * Sets initial user session.
    * 
    * 
    * @param array $user
    * @param Zend_Session_Namespace $coopSess 
    */
   public function setSessions(array $user, Zend_Session_Namespace $coopSess)
   {
      $db = new My_Db();

      $coopSess->inDb = true;
      $coopSess->userId = $user['id'];
      $coopSess->fname = $user['fname'];
      $coopSess->lname = $user['lname'];
      $coopSess->username = $user['username'];
      $coopSess->role = $db->getCol('coop_roles', 'role', array('id'=>$user['roles_id']));


       
      $semester = new My_Model_Semester();
      //$currentSem = $semester->getRealSem();
      //$coopSess->currentSemId = $db->getId('coop_semesters', array('semester' => $currentSem));
      $coopSess->currentSemId = $semester->getCurrentSemId();

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
                                   'semesters_id' => $coopSess->currentSemId
                                   ));

         if (empty($coopSess->classIds)) {
            $coopSess->classIds = array();
         }

         $coopSess->currentClassId = $coopSess->classIds[0];

         $class = new My_Model_Class();
         $coopSess->classNames = array();
         foreach ($coopSess->classIds as $cid) {
            if ($cid !== Null) {
               $name = $class->getName($cid);
               $coopSess->classNames[] = $name;
               if ($cid == $coopSess->currentClassId) {
                  $coopSess->currentClassName = $name;
               }

            }

         }
         //die(var_dump($coopSess->classNames));


         if (!$user['active']) {
            $coopSess->role = "notActive";
         }
      }
   }

   /**
    * Checks if a student is enrolled for the current semester. 
    * 
    * 
    * @param array $user User information.
    * @return boolean 
    */
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

   public function formatDateOut($date)
   {
      
      if (!empty($date)) {
         $dateTokens = explode("-", $date);
         $temp = $dateTokens[0];
         $dateTokens[0] = $dateTokens[1];
         $dateTokens[1] = $dateTokens[2];
         $dateTokens[2] = $temp;
         $date = implode("/", $dateTokens);

         return $date;
      }
   }

   public function formatDateIn($date)
   {
      if(!empty($date)) {
         $tokens = explode('/',$date);
         $date = $tokens[2] . $tokens[0] . $tokens[1];

         return $date;
      }

   }
}

?>
