<?php

/*
 * Creates the Cooperative Education Agreement form.
 */
class Application_Form_Agreement extends Application_Form_CommonForm
{
      // array of forms that were submitted (since this form can be submitted multiple times).
      public $submissions = array(); 
      
      public $assignNum = 3;
   
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
          

          //$this->makeJobsiteSubform();

          $this->makeStatics();

          $this->makeDynamics();

          // Do this in order to get the right question number on th
          $temp = $this->getSubForm('dynamic_tasks');
          $this->getSubForm('static_tasks')->getElement('other')->setLabel(count($temp) + 1 . '. Other (please specify):');
       
          // Submitted assignment ID.
          $id = new Zend_Form_Element_Hidden('id');

          $elems = new My_FormElement();
          //$saveSubmit = $elems->getSubmit('saveOnly');
          //$saveSubmit->setLabel('Save Only')
          //           ->setAttrib('class', 'resubmit');
          $finalSubmit = $elems->getSubmit('finalSubmit');
          $finalSubmit->setLabel('Submit as Final')
                      ->setAttrib('class', 'resubmit');

          $this->addElements(array($id, $finalSubmit));
          
          
          // Checks if there are submitted answers in order to populate the form with them.
          if ($this->populateForm === true) {
             $this->checkSubmittedAnswers(); 
          }

          $this->setElementDecorators(array('ViewHelper',
                                            'Errors'
                                     ));
      }

      public function checkSubmittedAnswers() {
         $Assign = new My_Model_Assignment();

         $where = array('classes_id' => $this->classId, 
             'semesters_id' => $this->semId,
             'username' => $this->username,
             'assignments_id' => $this->assignId);
         
         $subAssigns = $Assign->fetchSubmittedAssignments($where);

         if (!empty($subAssigns)) {
            foreach ($subAssigns as $s) {
               $answers = $Assign->fetchAnswers( array('submittedassignments_id' => $s->id ));

               $formData = array();
               $formData['id'] = $s->id;
               foreach ($answers as $a) {
                  if (!empty($a->static_question)) {
                     $elemName = $a->static_question;
                  } else {
                     $elemName = $a->assignmentquestions_id;
                  }
                  $formData[$elemName] = $a->answer_text;
               }

               //$form = new Application_Form_Agreement(
               //              array('classId' => $this->classId,
               //                    'semId' => $this->semId,
               //                    'username' => $this->username));
               //$form->populate($formData);

               $this->populate($formData);
               $this->setAttrib('submissionid', $s->id);
               $this->getElement('finalSubmit')->setAttrib('submissionid', $s->id);
               $this->submissions[] = clone $this;


            }
         }

      }






      public function makeStatics()
      {
         $staticTasks = new Zend_Form_SubForm();
         $staticTasks->setElementsBelongTo('static_tasks');
         $staticTasks->setDecorators(array('FormElements',
                                           array('HtmlTag', array('tag' => 'div'))
                                     ));
         $Elems = new My_FormElement();

         //$id = new Zend_Form_Element_Text('id');

         //die(var_dump($id));

         $position = $Elems->getCommonTbox('position', 'Position:');
         //die(var_dump($position->getId()));
         //die(var_dump($position));

         $company = $Elems->getCommonTbox('company', 'Company:');

         $hours = $Elems->getCommonTbox('hrs_per_week', 'Hrs/Week:');
         
         $semesters = $Elems->getCommonTbox('semester_dates', 'Semester Dates:');
         
         $superv = $Elems->getCommonTbox('supervisor', 'Supervisor:');
         
         $phone = $Elems->getCommonTbox('phone', 'Telephone:');

         $staticTasks->addElements($this->makeOther());

         $staticTasks->addElements(array($position, $company, $hours, $semesters, $superv, $phone));


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

         $Elems = $staticTasks->getElements();
         foreach ($Elems as $e) {
            $e->setAttrib('class', 'static');
         }

         $staticTasks->setElementDecorators(array('ViewHelper',
                                             'Errors',
                                             'Label'
                                       ));

         $this->addSubForm($staticTasks, 'static_tasks');




      }
      
}

