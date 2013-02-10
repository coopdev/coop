<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FlashMessages
 *
 * @author joseph
 */
class My_View_Helper_FlashMessage extends Zend_View_Helper_Abstract
{
   public function flashMessage()
   {
      $message = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
      $message = $message[0];
      $output = '';
      
      if (!empty($message)) {
         foreach ($message as $key => $val) {
            $output .= "<p class='$key'> $val </p>";
         }
      }
      
      return $output;
      //die("hello");
   }
}

?>
