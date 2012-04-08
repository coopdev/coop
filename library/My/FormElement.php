<?php

/**
 * Creates HTML elements.
 *
 * @author joseph
 */
class My_FormElement 
{  
   
   /* * * * * * * * * * * * * * * * * * * * * 
    * STUDENT INFORMATION SHEET FORM FIELDS *
    *                                       *
    * * * * * * * * * * * * * * * * * * * * */  
   
   /* Personal Information Fields below */
   
   
   /*
    * @param $name - name of element
    * @param $label - label ob element
    * 
    */
   public function getCommonTbox($name,$label)
   {
      $elem = new Zend_Form_Element_Text($name);
      $elem->setRequired(true)
           ->setLabel($label)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
    
   public function getUuidTbox()
   {
      $elem = new Zend_Form_Element_Text('uuid');
      $elem->setRequired(true)
           ->setLabel('Student ID#:')
           ->addValidator(new Zend_Validate_StringLength(array('min'=>8,'max'=>8)))
           ->addValidator(new Zend_Validate_Int())      
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
      
   /*
    * Should display semesters from database as options.
    */
   public function getEnrollDateSelect()
   {  
      $sems = $this->getSemRange();
      
      $elem = new Zend_Form_Element_Select('enrollDate');
      
      $elem->setRequired(true)
           ->setLabel('When are you planning to enroll in co-op (Semester/Year)?');
      
      foreach ($sems as $sem) {
         $elem->addMultiOptions(array($sem['id'] => $sem['semester']));
      }
           
      return $elem;
   }
   
      
   public function getClassChoiceSelect()
   {  
      $classes = $this->getClasses();
                             
      $elem = new Zend_Form_Element_Select('classChoice');
      $elem->setRequired(true)
           ->setLabel('Which co-op class are you planning to enroll in?');
      foreach ($classes as $c) {
         $elem->addMultiOptions(array($c['id'] => $c['name']));
      }
                      
      return $elem;
   }
   
   public function getCreditAmtTbox()
   {
      $elem = new Zend_Form_Element_Text('credits');
      $elem->setRequired(true)
           ->setLabel('How many credits will you enroll this semester')
           ->addValidator(new Zend_Validate_Int())
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   /*
    * Should this be a dropdown with semesters as options?
    */
   public function getGradDateTbox()
   {
      $elem = new Zend_Form_Element_Text('grad_date');
      $elem->setRequired(true)
           ->setLabel('Graduation date:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   /*
    * 
    */
   public function getMajorSelect()
   {  
      // get majors from database //
      
      $elem = new Zend_Form_Element_Select('major');
      $elem->setRequired(true)
           ->setLabel('Major:');
                 
      return $elem;
   }
   
   public function getSemesterInMajorRadio()
   {
      $elem = new Zend_Form_Element_Radio('semester_in_major');
      $elem->setRequired(true)
           ->setLabel('Semester in major:')
           ->setMultiOptions(array('1st' => '1st',
                                   '2nd' => '2nd',
                                   '3rd' => '3rd',
                                   '4th' => '4th',
                                   '5th' => '5th'))
           ->setSeparator('');
           
      return $elem;
   }
      
   
   public function getEmailTbox($name,$label)
   {
      $elem = new Zend_Form_Element_Text($name);
      $elem->setLabel($label)
           ->addFilter('StripTags')
           ->addFilter('StringTrim')
           ->setRequired(true)
           ->addValidator(new Zend_Validate_EmailAddress());
      return $elem;
   }
   
   
   
   /* Employment information fields below */
   
      
   public function getDateTbox($name,$label)
   {  
      $dateValidator = new Zend_Validate_Date();
      $dateValidator->setFormat('MM-dd-yyyy');
      
      $elem = new Zend_Form_Element_Text($name);
      $elem->setLabel("$label (mm-dd-yyyy):")
           ->setRequired('true')
           ->addValidator($dateValidator)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
      
   public function getPayRateTbox()
   {  
      
      $elem = new Zend_Form_Element_Text('rate_of_pay');
      $elem->setLabel('Rate of pay:')
           ->setRequired('true')
           ->addValidator(new Zend_Validate_Float())
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   
   
   /* * * * * * * * * * * * * * * * * * * * * 
    * Cooperative Education Agreement Form  *
    *                                       *
    * * * * * * * * * * * * * * * * * * * * */
   
      
   public function getAgreementRadio()
   {
      $elem = new Zend_Form_Element_Radio('agreement');
              
      $elem->setMultiOptions(array('agree' => 'Agree',
                                    'disagree' => 'Disagree'))
             ->setSeparator('')
             ->setRequired(true);
      
      return $elem;
   }
   
   public function getSubmit()
   {
      $elem = new Zend_Form_Element_Submit('submit');
      return $elem;
   }
   
   
   /** HELPERS **/
   
   private function getSemRange()
   {
      $semester = new My_Semester();
      
      // Get current semester.
      $curSem = $semester->getCurrentSem();
      //$curSem = "Fall 2012";

      $semPieces = explode(' ',$curSem);
      
      // Current year.
      $curYear = (int)$semPieces[1];
      $yr2 = $curYear+1;
      $yr3 = $curYear+2;
      $yr4 = $curYear+3;
      $yr5 = $curYear+4;
      
      // Get semesters from database starting with the current semester, until
      // five years ahead.
      $qry = "SELECT id, semester FROM coop_semesters
               WHERE semester ";
      
      // If semester is Fall, then start the choices displayed in the drop down 
      // at Fall since Spring will already have passed for the year.
      if ($semPieces[0] == 'Fall') {
         $qry .= "= '$curSem' ";
      } else {
         $qry .= "LIKE '%$curYear%' ";
      }
      //die($qry);
            
      $qry .= "OR semester like '%$yr2%'
               OR semester like '%$yr3%' 
               OR semester like '%$yr4%' 
               OR semester like '%$yr5%' 
               ORDER BY SUBSTRING_INDEX(semester,' ', -1), 
               SUBSTRING_INDEX(semester,' ', 1) DESC";
     
      $link = My_DbLink::connect();
      $sems = $link->fetchAll($qry);
      
      return $sems;
   }
   
   private function getClasses()
   {
      $link = My_DbLink::connect();
      
      $classes = $link->fetchAll("SELECT id, name FROM coop_classes");
      return $classes;
   }
    
}

?>
