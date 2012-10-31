<?php

/*
 * Used in AssignmentController->studentEvalAction()
 *         AssignmentController->supervisorEvalAction()
 *         AssignmentController->supervisorEvalPdfAction()
 *         AsyncController->studentEvalAction()
 */
class Application_Form_StudentEval extends Zend_Form
{
    protected $classId;
    protected $assignId;

    // Determines whether the form should be populated through this class or not.
    // Form may still be populated from somewhere else.  Setting this to true just tells
    // it to do it from this class.
    protected $populateForm = true; 

    public function init()
    {
       $aq = new My_Model_AssignmentQuestions();
       $as = new My_Model_Assignment();

       if (isset($this->assignId)) {
          $asId = $this->assignId;
       } else {
          $this->assignId = $as->getStudentEvalId();
          $asId = $this->assignId;
       }

       $questions = $aq->getChildParentQuestions(array('classId' => $this->classId, 'assignId' => $asId));

       //die(var_dump($questions));


      $this->setDecorators(array(array('ViewScript', 
                                   array('viewScript' => '/assignment/student-eval-template.phtml'))));

      $options = $this->generateOptions();

       foreach ($questions as $q) {
          if ($q['question_type'] !== 'parent') {
             $elem = new Zend_Form_Element_Radio($q['id']);
             $elem->setLabel($q['question_text'])
                  ->setRequired(true)
                  ->setSeparator('')
                  ->setMultiOptions($options);
                  //->setMultiOptions(array('1' => '1',
                  //                        '2' => '2',
                  //                        '3' => '3',
                  //                        '4' => '4',
                  //                        '5' => '5'));
                  //->setMultiOptions(array('Strongly Disagree' => 'Strongly Disagree',
                  //                        'Disagree' => 'Disagree',
                  //                        'Neutral' => 'Neutral',
                  //                        'Agree' => 'Agree',
                  //                        'Strongly Agree' => 'Strongly Agree'));
          } else {
             $elem = new Zend_Form_Element_Hidden($q['id']);
             $elem->setLabel($q['question_text']);
          }

          $this->addElements(array($elem));
       }

       $elems = new My_FormElement();
       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'resubmit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'resubmit');

       // Checks if there are submitted answers in order to populate the form with them.
       if ($this->populateForm === true) {
          $this->checkSubmittedAnswers(); 
       }


       // Only add submit buttons if there are questions and options.
       if (!empty($questions) && !empty($options)) {
          $this->addElements(array($saveSubmit,$finalSubmit));
       }

       $this->setElementDecorators(array('ViewHelper',
                                        'Errors'
                                  ));
    }





    public function checkSubmittedAnswers()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       // If 
       if ($coopSess->role === 'user') {
          $where['username'] = $coopSess->username;
       } else if ($coopSess->role === 'coordinator') {
          $where['username'] = $coopSess->submitForStudentData['username'];
       }

       $where['classes_id'] = $this->classId;
       $where['assignments_id'] = $this->assignId;
       $where['semesters_id'] = $coopSess->currentSemId;

       $assign = new My_Model_Assignment();
       if ($assign->isSubmitted($where) || $assign->isSaveOnly($where)) {
          $assign->populateStudentEval($this, $where);
       }
    }


    public function generateOptions()
    {
       $assign = new My_Model_Assignment();
       $data['assignments_id'] = $this->assignId;
       $data['classes_id'] = $this->classId;

       $amount = $assign->getSurveyOptionAmount($data);

       $options = array();
       for ($i = 1; $i <= $amount; $i++) {
          $options[$i] = $i;
       }
       //die(var_dump($options));

       return $options;
       
    }

    public function setClassId($classId)
    {
       //die($classId);
       $this->classId = $classId;
    }

    public function setAssignId($assignId)
    {
       $this->assignId = $assignId;

    }

    public function setPopulateForm($flag)
    {
       $this->populateForm = $flag;
    }


}

