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

       $fields = array($position, $company, $hours, $semesters, $superv, $phone); 

       // Used elsewhere to get these fields.
       foreach ($fields as $f) {
          $f->setAttrib('fieldType', 'jobsite');
       }
       return $fields;

    }
    
    public function makeJobsiteSubform()
    {
       $formFields = $this->makeJobsiteFields();

       $jobSiteSubform = new Zend_Form_SubForm('jobsite');
       $jobSiteSubform->setElementsBelongTo('jobsite');

       $jobSiteSubform->addElements($formFields);
       foreach ($jobSiteSubform as $j) {
          // DO NOT GET RID OF THIS. IT IS USED ELSEWHERE TO GET THESE JOBSITE FIELDS.
          $j->setAttrib('class','jobsite');
       }


       //$this->populateJobsiteFields($jobSiteSubform);
       
       
       $jobSiteSubform->setElementDecorators(array('ViewHelper',
                                           'Errors',
                                           'Label'
                                     ));
       

       //$this->addSubForm($jobSiteSubform, 'jobsite');

       return $jobSiteSubform;

       
    }
    
    
    protected function populateJobsiteFields()
    {
       $Asnmt = new My_Model_Assignment();
       $agrmtFormId = $Asnmt->getCoopAgreementId();

       $answers = $Asnmt->fetchAnswersForLastSubmitted( 
              array('assignments_id' => $agrmtFormId,
                    'classes_id' => $this->classId,
                    'semesters_id' => $this->semId,
                    'username' => $this->username));


       $elems = $this->static_tasks->getElements();
       $jobSiteFields = array();
       foreach ($elems as $e) {
          //die(var_dump($e->getAttrib('class')));
          if ($e->getAttrib('fieldType') === 'jobsite') {
             foreach ($answers as $a) {
                if ($e->getName() === $a->static_question) {
                   $e->setValue($a->answer_text);
                }
             }
          }
       }
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


    public function getAllElementsIncludingNested()
    {
       $elems = array();

       // using array_values because getElements returns named indexes but I want numerical
       // indexes.
       $elems = array_merge($elems, array_values($this->getElements()));

       $subForms = $this->getSubForms();
       if (is_array($subForms)) {
          foreach($subForms as $sf) {
             $elems = array_merge($elems, array_values($sf->getElements()));
          }
       }

       return $elems;
    }

    public function makeOther() 
    {
       $elems = new My_FormElement();
       $other = $elems->getCommonTbox('other', 'Other (please specify):');
       $other->setRequired(false)
             ->setAttrib('size', '80')
             ->setDecorators( array('ViewHelper','Label'));


       $otherRating = new Zend_Form_Element_Radio('other_rating');
       $otherRating->setDecorators( array('ViewHelper', 'Label') )
                   ->setLabel("")
                   ->setSeparator("")
                   ->setMultiOptions($this->options);

       return array($other, $otherRating);
    }


    // Same as the default isValid() method except it checks for saves and unsets the 
    // "required" check on all elements if "Save Only" was clicked.
    public function isValid($formData)
    {
       if (parent::isValid($formData)) {

          return true;

       } else {
          
          if (isset($formData['saveOnly']) && !is_null($formData['saveOnly'])) {
             $elems = $this->getAllElementsIncludingNested();
             foreach ($elems as $e) {
                $e->removeDecorator('Errors');
             }
             return true;
          }
          
          return false;
       }

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

