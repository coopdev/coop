<?php

/*
 * Creates the Cooperative Education Agreement form.
 */
class Application_Form_Agreement extends Application_Form_CommonForm
{
   
      public function init()
      {  
          $as = new My_Model_Assignment();
          $this->assignId = $as->getCoopAgreementId();

          $this->setDecorators(array(array('ViewScript', 
                                      array('viewScript' => '/form/coop-agreement-template.phtml'))));


          //$this->makeStatics();


          $this->makeDynamics();


          $elems = new My_FormElement();
          $saveSubmit = $elems->getSubmit('saveOnly');
          $saveSubmit->setLabel('Save Only')
                     ->setAttrib('class', 'resubmit');
          $finalSubmit = $elems->getSubmit('finalSubmit');
          $finalSubmit->setLabel('Submit as Final')
                      ->setAttrib('class', 'resubmit');

          $this->addElements(array($saveSubmit,$finalSubmit));
          
          
          // Checks if there are submitted answers in order to populate the form with them.
          if ($this->populateForm === true) {
             //$this->checkSubmittedAnswers(); 
          }

          $this->setElementDecorators(array('ViewHelper',
                                            'Errors'
                                     ));
      }
      
}

