<?php

class Application_Form_Report extends Zend_Form
{

    public function init()
    {
       $report = new Zend_Form_Element_Radio('report');
       $report->setRequired(true)
              ->setMultiOptions( array('assignment'       => 'Student Assignment Submission Report',
                                       'empSatisfaction'  => 'Employer Satisfaction Report',
                                       'completionRate'   => 'Completion Rate Report',
                                       'demog'            => 'Student Demographic Report'));

       $Semester = new My_Model_Semester();
       $sems = $Semester->getUpToCurrent();

       $semesterSelect = new Zend_Form_Element_Select('semesters_id');
       $semesterSelect->setLabel("Report by Semester")
                      ->addMultiOptions( array(' ' => '-----------') );



       foreach ($sems as $s) {
          $semesterSelect->addMultiOptions(array($s['id'] => $s['semester']));
       }

       $bySemesterSubmit = new Zend_Form_Element_Submit('bySemester');
       $bySemesterSubmit->setLabel("Generate Report by Semester");


       $years = array();
       foreach ($sems as $s) {
          $years[] = $s['year'];
       }
       $years = array_unique($years);
       $year = new Zend_Form_Element_Select('year');
       $year->setLabel("Report by Academic Year");
       $year->addMultiOptions( array(' ' => '-----------') );
       foreach ($years as $y) {
          $year->addMultiOptions( array($y => $y) );
       }
            
            

       $byYear = new Zend_Form_Element_Submit('byYear');
       $byYear->setLabel("Generate Report by Academic Year");

       $this->addElements(array($semesterSelect, $report, $bySemesterSubmit, $year, $byYear));
    }


}

