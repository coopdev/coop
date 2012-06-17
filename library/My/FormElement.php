<?php

/**
 * Creates Form elements.
 *
 * @author joseph
 */
class My_FormElement 
{  
   private $requiredVal = true;
   
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
      $elem->setRequired($this->requiredVal)
           ->setLabel($label)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getCommonTarea($name,$label)
   {
      $elem = new Zend_Form_Element_Textarea($name);
      $elem->setRequired($this->requiredVal)
           ->setLabel($label)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
    
   public function getUuidTbox()
   {
      $elem = new Zend_Form_Element_Text('uuid');

      $strLen = new Zend_Validate_StringLength(array('min'=>8,'max'=>8));
      $strLen->setMessage('Must be exactly %min% digits', 'stringLengthTooShort')
             ->setMessage('Must be exactly %min% digits', 'stringLengthTooLong');

      $elem->setRequired($this->requiredVal)
           ->setLabel('Student ID#:')
           ->addValidator($strLen)
           ->addValidator(new Zend_Validate_Digits())      
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getZipcodeTbox()
   {
      $elem = new Zend_Form_Element_Text('zipcode');

      $strLen = new Zend_Validate_StringLength(array('min'=>5,'max'=>5));
      $strLen->setMessage('Must be exactly %min% digits', 'stringLengthTooShort')
             ->setMessage('Must be exactly %min% digits', 'stringLengthTooLong');

      $elem->setRequired($this->requiredVal)
           ->setLabel('Zipcode:')
           ->addValidator($strLen)
           ->addValidator(new Zend_Validate_Digits())      
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
      
      $elem = new Zend_Form_Element_Select('semesters_id');
      
      $elem->setRequired($this->requiredVal)
           ->setLabel('When are you planning to enroll in co-op (Semester/Year)?');
      
      foreach ($sems as $sem) {
         $elem->addMultiOptions(array($sem['id'] => $sem['semester']));
      }
           
      return $elem;
   }
   
      
   public function getClassChoiceSelect()
   {  
      //$classes = $this->getClasses();

      $class = new My_Model_Class();
      $classes = $class->getall();

                             
      $elem = new Zend_Form_Element_Select('classes_id');
      $elem->setRequired($this->requiredVal)
           ->setLabel('Which co-op class are you planning to enroll in?');
      foreach ($classes as $c) {
         $elem->addMultiOptions(array($c['id'] => $c['name']));
      }
                      
      return $elem;
   }
   
   public function getAssignmentSelect()
   {  
      $assign = new My_Model_Assignment();
      $assigns = $assign->getall();

      $elem = new Zend_Form_Element_Select('assignments_id');
      $elem->setRequired($this->requiredVal)
           ->setLabel('Select assignment:');
      foreach ($assigns as $a) {
         $elem->addMultiOptions(array($a['id'] => $a['assignment']));
      }
                      
      return $elem;
   }
   public function getCreditAmtTbox()
   {
      $elem = new Zend_Form_Element_Text('credits');
      $elem->setRequired($this->requiredVal)
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
      $elem->setRequired($this->requiredVal)
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
      $major = new My_Model_Major();
      $rows = $major->getAll();
      
      $elem = new Zend_Form_Element_Select('majors_id');
      $elem->setRequired($this->requiredVal)
           ->setLabel('Major:');

      foreach ($rows as $r) {
         $elem->addMultiOptions(array($r['id'] => $r['major']));
      }
                 
      return $elem;
   }

   public function getCoordsSelect()
   {

       $coord = new Zend_Form_Element_Select('coordinator');
       $coord->setLabel("Select coordinator:");

       $user = new My_Model_User();
       $coords = $user->getAllCoords();

       
       foreach ($coords as $c) {
          $coord->addMultiOptions(array($c['username'] => $c['lname'].", ".$c['fname']." (".$c['username'].")"));
       }

       return $coord;
   }

   
   public function getCoordsSelectOptional()
   {

       $coord = new Zend_Form_Element_Select('coordinator');
       $coord->setLabel("Select coordinator:");

       $user = new My_Model_User();
       $coords = $user->getAllCoords();

       $coord->addMultiOptions(array('' => "--------------"));
       
       foreach ($coords as $c) {
          $coord->addMultiOptions(array($c['username'] => $c['lname'].", ".$c['fname']." (".$c['username'].")"));
       }

       return $coord;
   }

   public function getStuAidsSelect()
   {

       $stuAid = new Zend_Form_Element_Select('studentAid');
       $stuAid->setLabel("Select student helper:");

       $user = new My_Model_User();
       $coords = $user->getAllStuAids();

       
       foreach ($coords as $c) {
          $stuAid->addMultiOptions(array($c['username'] => $c['lname'].", ".$c['fname']." (".$c['username'].")"));
       }

       return $stuAid;
   }


   public function getStuAidsSelectOptional()
   {

       $coord = new Zend_Form_Element_Select('coordinator');
       $coord->setLabel("Select coordinator:");

       $user = new My_Model_User();
       $coords = $user->getAllCoords();

       $coord->addMultiOptions(array('' => "--------------"));
       
       foreach ($coords as $c) {
          $coord->addMultiOptions(array($c['username'] => $c['lname'].", ".$c['fname']." (".$c['username'].")"));
       }

       return $coord;
   }


   public function getSemesterInMajorRadio()
   {
      $elem = new Zend_Form_Element_Radio('semester_in_major');
      $elem->setRequired($this->requiredVal)
           ->setLabel('Semester in major:')
           ->setMultiOptions(array('1' => '1st',
                                   '2' => '2nd',
                                   '3' => '3rd',
                                   '4' => '4th',
                                   '5' => '5th'))
           ->setSeparator('');
           
      return $elem;
   }
      
   
   public function getEmailTbox($name,$label)
   {
      $elem = new Zend_Form_Element_Text($name);
      $elem->setLabel($label)
           ->addFilter('StripTags')
           ->addFilter('StringTrim')
           ->setRequired($this->requiredVal)
           ->addValidator(new Zend_Validate_EmailAddress());
      return $elem;
   }
   
   
   
   /* Employment information fields below */
   
      
   public function getDateTbox($name,$label)
   {  
      $dateValidator = new Zend_Validate_Date();
      $dateValidator->setFormat('MM/dd/yyyy');
      
      $elem = new Zend_Form_Element_Text($name);
      $elem->setLabel("$label (mm/dd/yyyy):")
           ->setRequired($this->requiredVal)
           ->addValidator($dateValidator)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
      
   public function getPayRateTbox()
   {  
      
      $elem = new Zend_Form_Element_Text('rate_of_pay');
      $elem->setLabel('Rate of pay:')
           ->setRequired($this->requiredVal)
           ->addValidator(new Zend_Validate_Float())
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   
   
   /* * * * * * * * * * * * * * * * * * * * * 
    * Cooperative Education Agreement Form  *
    *                                       *
    * * * * * * * * * * * * * * * * * * * * */
   
      
   public function getAgreementRadio($label, $name = 'agreement')
   {
      $elem = new Zend_Form_Element_Radio($name);
              
      $elem->setMultiOptions(array(true => 'Agree'))
           ->setName($name)
           ->setLabel($label)
           ->setSeparator('')
           ->setRequired($this->requiredVal)
           ->addErrorMessage('Must agree before continuing');
      
      return $elem;
   }
   
   public function getSubmit($text = 'Submit')
   {
      $elem = new Zend_Form_Element_Submit($text);
      return $elem;
   }
   
   
   /** HELPERS **/
   
   private function getSemRange()
   {
      $semester = new My_Semester();
      
      // Get current semester.
      $curSem = $semester->getRealSem();
      //$curSem = "Summer 2012";

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

      $temp = array();
      $tempPos = "";
      $ind = 0;
      //die(var_dump($sems));
      foreach ($sems as $s) {
         $tokens = explode(' ', $s['semester']);
         if ($tokens[0] === 'Summer') {
            $temp = $s;
            $tempPos = $ind;
         } else if ($tokens[0] === 'Spring') {
            $sems[$tempPos] = $s;
            $sems[$ind] = $temp;
         }
         $ind++;

      }
      //die(var_dump($sems));
      
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
