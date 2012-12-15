<?php

class Application_Form_CoverLetter extends Application_Form_CommonForm
{

    public function init()
    {
       $Assign = new My_Model_Assignment();
       $this->assignId = $Assign->getCoverLetterId();

       $this->makeStatics();
       
       // Submit buttons.
       $elems = new My_FormElement();
       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'resubmit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'resubmit');

       $this->addElements( array($saveSubmit, $finalSubmit));
       
       // Checks if there are submitted answers in order to populate the form with them.
       if ($this->populateForm === true) {
          $this->checkSubmittedAnswers(); 
       }
        /* Form Elements & Other Definitions Here ... */
    }

    public function makeStatics()
    {
       $staticTasks = new Zend_Form_SubForm();
       $staticTasks->setElementsBelongTo('static_tasks');
       
       
       $resume = new Zend_Form_Element_Textarea('coverLetter');
       $resume->setRequired(true)
              ->addFilter('StringTrim');

       $staticTasks->addElement($resume);
       $this->addSubForm($staticTasks, 'static_tasks');

    }

}

