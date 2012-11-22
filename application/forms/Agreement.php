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

          $this->options = array('4' => 'Not Important',
                                 '3' => 'Not Very Important',
                                 '2' => 'Somewhat Important',
                                 '1' => 'Very Important');

          $this->setDecorators(array(array('ViewScript', 
                                      array('viewScript' => '/form/coop-agreement-template.phtml'))));

          $this->makeJobsiteSubform();

          $this->makeStatics();


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
             $this->checkSubmittedAnswers(); 
          }

          $this->setElementDecorators(array('ViewHelper',
                                            'Errors'
                                     ));
      }






      public function makeStatics()
      {
         $staticTasks = new Zend_Form_SubForm();
         $staticTasks->setElementsBelongTo('static_tasks');
         $staticTasks->setDecorators(array('FormElements',
                                           array('HtmlTag', array('tag' => 'div'))
                                     ));


         $Elems = new My_FormElement();

         $Class = new My_Model_Class();

         if ($Class->isTransferable($this->classId)) {
            $lrnObj1 = $Elems->getCommonTbox('lrnObjective1', "1.");
            $lrnObj1->setAttrib('size', '105')
                    ->setAttrib('placeholder', 'Enter Learning Objective');
            $lrnObj2 = $Elems->getCommonTbox('lrnObjective2', "2.");
            $lrnObj2->setAttrib('size', '105')
                    ->setAttrib('placeholder', 'Enter Learning Objective');
            
            $staticTasks->addElements(array($lrnObj1, $lrnObj2));
         } else {
            $duties = $Elems->getCommonTarea('duties', ' ');
            $staticTasks->addElement($duties);
         }

         $elems = $staticTasks->getElements();
         foreach ($elems as $e) {
            $e->setAttrib('class', 'static');
         }

         $staticTasks->setElementDecorators(array('ViewHelper',
                                             'Errors',
                                             'Label'
                                       ));

         $this->addSubForm($staticTasks, 'static_tasks');




      }
      
}

