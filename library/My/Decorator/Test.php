<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Test
 *
 * @author joseph
 */
class My_Decorator_Test extends Zend_Form_Decorator_Abstract
{
   protected $_format = "%s<input type='%s' name='%s' />";


   public function render($content)
   {
      
      $this->setElement($content);
      $elem = $this->getElement();


      $label = $elem->getLabel();
      $name = $elem->getName();
   }
}

?>
