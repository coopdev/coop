<?php

class Application_Form_LearningOutcomeReport extends Application_Form_CommonForm
{
    protected $minLen;
    public $submittedReports = array();

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


    /*
     * @param $isFinal Value of is_final column in submittedassignments table.
     */
    public function setSubmittedReports($isFinal=1)
    {
       $this->submittedReports = array();
       
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
                      "is_final       = $isFinal");
       
       $rows = $SubmittedAssign->fetchAll($where);

       if (count($rows) < 1) {
          return;
       }

       $count = 0;
       foreach ($rows as $row) {
          $count++;
          
          $subAssignId = $row->id;
          $answer = $AssignmentAnswers->fetchRow("submittedassignments_id = $subAssignId");
          
          $submittedReport = clone $this;
          $submittedReport->report->setValue($answer->answer_text);
          $submittedReport->report->setAttrib("id", "report-$count");
          $hiddenSubAssignId = new Zend_Form_Element_Hidden("submittedassignments_id");
          $hiddenSubAssignId->id = "submittedassignment-$count-id";
          $hiddenSubAssignId->setValue($answer->submittedassignments_id);

          $lastPage = new Zend_Form_Element_Hidden("lastPage");
          $lastPage->id = "lastPage$count";
          
          $submittedReport->addElements( array($hiddenSubAssignId, $lastPage) );
          //die(var_dump($savedReport->getElements()));

          // Use subassignments_id as the index of array so the submitted form can 
          // be retrieved from the array easier.
          $this->submittedReports[$answer->submittedassignments_id] = $submittedReport;
       }

    }

    public function submit($data)
    {
       $SubmittedAssign = new My_Model_SubmittedAssignment();
       $Assignment      = new My_Model_Assignment();

       $where = array();
       $where[] = "username = '" . $this->username . "'";
       $where[] = "classes_id = " . $this->classId;
       $where[] = "semesters_id = " . $this->semId;
       $where[] = "assignments_id = " . $this->assignId;
       $submits = $SubmittedAssign->fetchAll($where);
       
       // Only allow at most 3 submits.
       if (count($submits) >= 3) {
          return "tooManySubmits";
       }
       
       
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
       
       $answers['report'] = $data['report'];

       //die(var_dump($this->getValues()));
       $foreignKeys["submittedassignments_id"] = $subAssignId;

       $Assignment->insertAnswers($answers, $foreignKeys, array('static' => true));
    }

    public function update($data)
    {
       unset($data['lastPage']);

       $SubAssign = new My_Model_SubmittedAssignment();
       $Assignment = new My_Model_Assignment();

       $dateSubmitted = date('Ymd');

       $updateVals['date_submitted'] = $dateSubmitted;
       if ($this->finalSubmit->isChecked()) {
          $updateVals['is_final'] = 1;
       } else {
          $updateVals['is_final'] = 0;
       }

       $username = $this->username;
       $SubAssign->update($updateVals, 
                          "id = " . $data['submittedassignments_id'] .
                          " AND username = '$username'");

       $updateVals = NULL;
       $answers["report"] = $data['report'];
       $where['submittedassignments_id'] = $data['submittedassignments_id'];
       $Assignment->updateAnswers($answers, $where, array('static' => true));

    }

}

