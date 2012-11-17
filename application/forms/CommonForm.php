<?php

class Application_Form_CommonForm extends Zend_Form
{
    protected $assignId;
    protected $classId;
    protected $username;
    protected $semId;
    protected $populateForm = true;

    protected $options = array('4' => '4',
                               '3' => '3',
                               '2' => '2',
                               '1' => '1',
                               'NA' => 'NA');

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
    }


    public function checkSubmittedAnswers()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       
       $where['username'] = $this->username;
       $where['classes_id'] = $this->classId;
       $where['assignments_id'] = $this->assignId;
       $where['semesters_id'] = $this->semId;
       //die(var_dump($where));

       //die(var_dump($this->assignId));
       $assign = new My_Model_Assignment();
       if ($assign->isSubmitted($where) || $assign->isSaveOnly($where)) {
          $assign->populateStudentEval($this, $where);
       }
    }

    protected function makeDynamics()
    {
       $aq = new My_Model_AssignmentQuestions();
       $Assignment = new My_Model_Assignment();

       // Using student eval ID because the three forms are using the same questions.
       $stuEvalId = $Assignment->getStudentEvalId();
       $questions = $aq->getQuestions(array('classes_id' => $this->classId, 'assignments_id' => $stuEvalId));
       
       $dynamic_tasks = new Zend_Form_SubForm('dynamic_tasks');
       $dynamic_tasks->setDecorators(array('FormElements',
                                      array('HtmlTag', array('tag' => 'div'))
                                ));
       $dynamic_tasks->setElementsBelongTo('dynamic_tasks');

       $qnum = 0;
       foreach ($questions as $q) {

          // Only include non parent type questions.
          if ($q['question_type'] !== 'parent') {
             $elem = new Zend_Form_Element_Radio($q['id']);
             $elem->setLabel(++$qnum . '. ' . $q['question_text'])
                  ->setRequired(true)
                  ->setAttrib('class', 'dynamic')
                  ->setSeparator('')
                  //->setMultiOptions($options);
                  ->setMultiOptions($this->options);
             
             $dynamic_tasks->addElement($elem);
          }

       }

       $this->addSubForm($dynamic_tasks, 'dynamic_tasks');

    }
    

    public function setClassId($classId)
    {
       $this->classId = $classId;

    }

    public function setUsername($username)
    {
       $this->username = $username;

    }

    public function setAssignId($assignId)
    {
       $this->assignId = $assignId;
    }

    public function setSemId($semId)
    {
       $this->semId = $semId;
    }
}

