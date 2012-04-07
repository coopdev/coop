<?php

/**
 * Description of InvalidFormHandler
 *
 * @author joseph
 */
class My_InvalidFormHandler 
{
   public function handle($form)
   {
      $coopSess = new Zend_Session_Namespace('coop');
       
      // If form was invalid, $coopSess->invalidData will be set.
      if (isset($coopSess->invalidData['invalid'])) {
          //die('hi');
          $data = $coopSess->invalidData;
          
          $form->isValid($data);
             
          // If the above line is true, it seems to populate the form
          // and provide the errors automatically.
                 
          unset($coopSess->invalidData);
       }    
   }
   
   
   /*
    * Checks if the user clicked agree in form.
    */
   public function chkAgreement($form, $view) 
   {
      $coopSess = new Zend_Session_Namespace('coop');
      //die(var_dump($coopSess->invalidData));
      if (isset($coopSess->invalidData['agreement'])) {
         
         $data = $coopSess->invalidData;
         if ($data['agreement'] == 'disagree') {
            
            $view->message = 'Must agree before continuing';
            $form->populate($data);
            unset($coopSess->invalidData);
            
         }
         
      }
   }
}

?>
