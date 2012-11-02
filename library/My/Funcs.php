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
      $coopSess->currentSemId = $semester->getCurrentSemId();

      if ($coopSess->role === 'user') {

          $funcs = new My_Funcs();

          // Check if this user has an Incomplete status.
          $incompleteData = $semester->incompleteData(array('student' => $user['username']));
          //die(var_dump(empty($incompleteData)));

          // If user is not enrolled for the current semester. 
          if (!$funcs->isEnrolled($user)) {

             // If user does NOT have incomplete status, deny access.
             if (empty($incompleteData)) {
                $coopSess->role = "notEnrolled";
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('redirector');
                //die('hi');
                $redirector->gotoSimple('access-denied', 'pages');

             // If user DOES have incomplete status, set the semester ID to the incomplete
             // semester and set the class ids to the incomplete ones.
             } else {
               $coopSess->currentSemId = $incompleteData['semId'];
               $coopSess->classIds = $incompleteData['classIds'];
             }

          // Otherwise, the user is enrolled for the current semester so set the class ids.
          } else {

              // store enrolled class ids.
              $coopSess->classIds = $db->getCols('coop_users_semesters', 
                                       'classes_id',
                                       array('student'=>$user['username'], 
                                       'semesters_id' => $coopSess->currentSemId
                                       ));

              // If student also has incomplete status for other classes/semester.
              if (!empty($incompleteData)) {

                 // store incomplete class ids so we know which ones are incomplete status.
                 // This will be used when a student switches classes to check if they are
                 // switching to an incomplete class so the appropriate semester id can be set.
                 $coopSess->incompleteClassIds = $incompleteData['classIds'];

                 // append the incomplete class ids onto the classIds array.
                 $coopSess->classIds = array_merge($coopSess->classIds, $incompleteData['classIds']);

                 // store incomplete semester id. This will be used if a student is switching
                 // to an incomplete class. In that case, this id will overwrite the currentSemId 
                 // session variable.
                 $coopSess->incompleteSemId = $incompleteData['semId'];

              }
             
          }


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
