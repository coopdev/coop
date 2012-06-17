<?php

class Application_Form_LearningOutcomeReport extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $report = new Zend_Form_Element_Textarea('report');
       $minLength = new Zend_Validate_StringLength(array('min' => '20'));
       $minLength->setMessage("Must be at least %min% characters long", 'stringLengthTooShort');
       $report->addValidator($minLength)
              ->setRequired(true);

       $save = $elems->getSubmit('Save Only');

       $submit = $elems->getSubmit();

       $this->addElements(array($report, $save, $submit));
    }


}

