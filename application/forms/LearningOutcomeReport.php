<?php

class Application_Form_LearningOutcomeReport extends Zend_Form
{
    protected $minLen;

    public function init()
    {
       $this->setDecorators(array(array('ViewScript', 
                                   array('viewScript' => '/assignment/forms/learning-outcome.phtml'))));

       $elems = new My_FormElement();

       $this->setAttrib('id','learningOutcomeReport'); 

       $report = new Zend_Form_Element_Textarea('report');

       
       $this->setMinLen();
       $minLength = new Zend_Validate_StringLength(array('min' => $this->minLen));
       $minLength->setMessage("Must be at least %min% characters long", 'stringLengthTooShort');
       $report->addValidator($minLength)
              ->setRequired(true)
              ->setAttrib('rows', '100')
              ->setAttrib('cols', '100');

       //$hiddenMinLen = new Zend_Form_Element_Hidden('answer_minlength');
       //$hiddenMinLen->
       // set value for this hidden.



       $save = $elems->getSubmit('Save Only');

       $submit = $elems->getSubmit();


       $this->addElements(array($report, $save, $submit));

       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));

    }


    private function setMinLen()
    {
       $assign = new My_Model_Assignment();
       $row = $assign->getAssignmentByNum(4);

       $this->minLen = $row->answer_minlength;
    }


}

