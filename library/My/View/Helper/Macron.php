<?php
/**
 * Returns the correct Hawaiian font based on input
 *
 */
class My_View_Helper_Macron extends Zend_View_Helper_Abstract
{

     public function macron($letter) {

       switch($letter) {
         case 'A':
           return html_entity_decode("&#256;", ENT_NOQUOTES, 'UTF-8');
         case 'a':
           return html_entity_decode("&#257;", ENT_NOQUOTES, 'UTF-8');
         case 'E':
           return html_entity_decode("&#274;", ENT_NOQUOTES, 'UTF-8');
         case 'e':
           return html_entity_decode("&#275;", ENT_NOQUOTES, 'UTF-8');
         case 'I':
           return html_entity_decode("&#298;", ENT_NOQUOTES, 'UTF-8');
         case 'i':
           return html_entity_decode("&#299;", ENT_NOQUOTES, 'UTF-8');
         case 'O':
           return html_entity_decode("&#332;", ENT_NOQUOTES, 'UTF-8');
         case 'o':
           return html_entity_decode("&#333;", ENT_NOQUOTES, 'UTF-8');
         case 'U':
           return html_entity_decode("&#362;", ENT_NOQUOTES, 'UTF-8');
         case 'u':
           return html_entity_decode("&#363;", ENT_NOQUOTES, 'UTF-8');
         default:
           echo "<h3>The letter ".$letter. "does not have an equivalent.</h3>";
       }

     }
     
}
