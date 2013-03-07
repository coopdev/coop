<?php

class Application_Form_LearningOutcomeReport extends Application_Form_CommonForm
{
    protected $minLen;
    public $savedReports = array();

    public function init()
    {
       $Assign = new My_Model_Assignment();
       $this->assignId = $Assign->getLearningOutcomeId();
       
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



       $saveSubmit = $elems->getSubmit('saveOnly');
       $saveSubmit->setLabel('Save Only')
                  ->setAttrib('class', 'resubmit');
       $finalSubmit = $elems->getSubmit('finalSubmit');
       $finalSubmit->setLabel('Submit as Final')
                   ->setAttrib('class', 'resubmit');

       $this->addElements( array($report, $saveSubmit, $finalSubmit));


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


    public function setSavedReports()
    {
       
       $SubmittedAssign   = new My_Model_SubmittedAssignment();
       $AssignmentAnswers = new My_Model_AssignmentAnswers();
       
       $uname     = $this->username;
       $classId   = $this->classId;
       $semId     = $this->semId;
       $assignId  = $this->assignId;

       $where = array("username       = '$uname'", 
                      "classes_id     = $classId", 
                      "semesters_id   = $semId", 
                      "assignments_id = $assignId",
                      "is_final       = 0");
       
       $rows = $SubmittedAssign->fetchAll($where);

       if (count($rows) < 1) {
          return;
       }

       $count = 0;
       foreach ($rows as $row) {
          $count++;
          $subAssignId = $row->id;
          $answer = $AssignmentAnswers->fetchRow("submittedassignments_id = $subAssignId");

          $this->report->setValue($answer->answer_text);
          $this->report->setAttrib("id", "report-$count");
          $this->savedReports[] = clone $this;
       }

    }

    public function submit()
    {
       $SubmittedAssign = new My_Model_SubmittedAssignment();
       $Assignment      = new My_Model_Assignment();
       
       
       $insertVals = array("username"       => $this->username,
                           "classes_id"     => $this->classId,
                           "semesters_id"   => $this->semId,
                           "assignments_id" => $this->assignId,
                           "date_submitted" => date('Ymd'));
       
       if ($this->finalSubmit->isChecked()) {
          $insertVals["is_final"] = 1;
       } else {
          $insertVals["is_final"] = 0;
       }

       //die(var_dump($insertVals));

       $SubmittedAssign->insert($insertVals);
       $subAssignId = $SubmittedAssign->getAdapter()->lastInsertId();
       
       $answers['report'] = $this->report->getValue();
       $foreignKeys["submittedassignments_id"] = $subAssignId;

       $Assignment->insertAnswers($answers, $foreignKeys, array('static' => true));
    }


    public function submitFinal()
    {

    }

    public function submitSaveOnly()
    {

    }
}

