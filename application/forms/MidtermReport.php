<?php

class Application_Form_MidtermReport extends Application_Form_CommonForm
{
    protected $classId;
    protected $semId;
    protected $username;
    protected $assignId;

    public function init()
    {

       $as = new My_Model_Assignment();
       $this->assignId = $as->getMidtermId(); // Midterm Report's id.

       $questions = $as->getQuestions($this->assignId);

       // TEMPLATE
       $this->setDecorators( array( 
           array('ViewScript', array('viewScript' => '/assignment/forms/midterm-report.phtml'))));
           //array('ViewScript', array('viewScript' => '/assignment/midterm-report-form.phtml'))));

       foreach ($questions as $q) {

          //$elem = "elem".$q['id'];


          $elem = new Zend_Form_Element_Textarea($q['id']);

          // String length validator
          $strLength = new Zend_Validate_StringLength(array('min'=>$q['answer_minlength']));
          $strLength->setMessage("Must be at least %min% characters long", 'stringLengthTooShort');


          $elem->setLabel($q['question_text'])
               ->setRequired(true)
               ->addValidator($strLength)
               ->addFilter("StringTrim")
               ->addFilter("StripTags")
               ->setAttrib('class', 'answerText');

          $validator = $elem->getValidator('StringLength');
          $min = $validator->getMin();
          //die(var_dump($min));


          $this->addElement($elem);
       }

       $finalSubmit = new Zend_Form_Element_Submit("finalSubmit");
       $finalSubmit->setLabel("Submit as Final");

       $saveOnly = new Zend_Form_Element_Submit("saveOnly");
       $saveOnly->setLabel("Save Only");

       $this->addElements(array($saveOnly, $finalSubmit));

       //$this->checkSubmittedAnswers();


       // CLEAR DECORATORS FOR TEMPLATE
       $this->setElementDecorators(array('ViewHelper',
                                         "Errors"));
    }


    /*
     * Populates the midterm report if it has been previously submitted or saved.
     */
    public function checkSubmittedAnswers()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $assign = new My_Model_Assignment();

       $where['username'] = $this->username;
       $where['classes_id'] = $this->classId;
       $where['semesters_id'] = $this->semId;
       $where['assignments_id'] = $assign->getMidtermId();


       if ($assign->isSubmitted($where) || $assign->isSaveOnly($where)) {
          $assign->populateMidTermReport($this, $where);
       }
    }

    public function setClassId($classId)
    {
       $this->classId = $classId;
    }

    public function setSemId($semId)
    {
       $this->semId = $semId;

    }

    public function setUsername($username)
    {
       $this->username = $username;

    }

}

