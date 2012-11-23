<?php

class Application_Form_CommonForm extends Zend_Form
{
    // $assignId is only used to get answers to submitted forms, not the actual questions
    // since all three forms using this Class share the same questions.
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
    }



    // Supervisor Eval and Agreement form have some fields which are the same (at the top
    // of form). This method creates those and returns them.
    public function makeJobsiteFields()
    {
       $elems = new My_FormElement();

       //$id = new Zend_Form_Element_Hidden('id');

       //die(var_dump($id));

       $position = $elems->getCommonTbox('position', 'Position:');
       //die(var_dump($position->getId()));
       //die(var_dump($position));

       $company = $elems->getCommonTbox('company', 'Company:');

       $hours = $elems->getCommonTbox('hrs_per_week', 'Hrs/Week:');
       
       $semesters = $elems->getCommonTbox('semester_dates', 'Semester Dates:');
       
       $superv = $elems->getCommonTbox('supervisor', 'Supervisor:');
       
       $phone = $elems->getCommonTbox('phone', 'Telephone:');

       return array($position, $company, $hours, $semesters, $superv, $phone);

    }
    
    public function makeJobsiteSubform()
    {
       $formFields = $this->makeJobsiteFields();

       $jobSiteSubform = new Zend_Form_SubForm('jobsite');
       $jobSiteSubform->setElementsBelongTo('jobsite');

       $jobSiteSubform->addElements($formFields);
       foreach ($jobSiteSubform as $j) {
          $j->setAttrib('class','jobsite');
       }


       $this->populateJobsiteFields($jobSiteSubform);
       
       
       $jobSiteSubform->setElementDecorators(array('ViewHelper',
                                           'Errors',
                                           'Label'
                                     ));
       

       $this->addSubForm($jobSiteSubform, 'jobsite');

       
    }
    
    public function populateJobsiteFields($jobSiteSubForm)
    {
       $Jobsite = new My_Model_Jobsites();

       $record = $Jobsite->fetchLast( array('username' => $this->username,
                          'classes_id' => $this->classId, 
                          'semesters_id' => $this->semId) );

       $jobSiteSubForm->populate($record->toArray());

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


    

    
    
    /* SETTERS */
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
    
    public function setPopulateForm($flag)
    {
       $this->populateForm = $flag;
    }

    
    /* GETTERS */
    public function getClassId()
    {
       return $this->classId;

    }

    public function getUsername()
    {
       return $this->username;

    }

    public function getAssignId()
    {
       return $this->assignId;
    }

    public function getSemId()
    {
       return $this->semId;
    }
}

