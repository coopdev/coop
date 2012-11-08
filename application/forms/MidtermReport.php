<?php

class Application_Form_MidtermReport extends Zend_Form
{
    protected $classId;
    protected $semId;
    protected $username;

    public function init()
    {

       $as = new My_Model_Assignment();
       $id = $as->getMidtermId(); // Midterm Report's id.

       $questions = $as->getQuestions($id);

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

       if ($coopSess->role === 'user') {
          $where['username'] = $coopSess->username;
          $where['classes_id'] = $coopSess->currentClassId;
          $where['semesters_id'] = $coopSess->currentSemId;
       } else if ($coopSess->role === 'coordinator') {
          $where['username'] = $this->username;
          $where['classes_id'] = $this->classId;
          $where['semesters_id'] = $this->semId;
       }
       $where['assignments_id'] = $assign->getMidtermId();


       if ($assign->isSubmitted($where) || $assign->isSaveOnly($where)) {
          $assign->populateMidTermReport($this, $where);
       }
    }

    public function setClassId($classId)
    {
       //die($classId);
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

