<?php

/**
 * Creates Form elements.
 *
 * @author joseph
 */
class My_FormElement 
{  
   private $requiredVal = false;
   
   /* * * * * * * * * * * * * * * * * * * * * 
    * STUDENT INFORMATION SHEET FORM FIELDS *
    *                                       *
    * * * * * * * * * * * * * * * * * * * * */  
   
   /* Personal Information Fields below */
   
   
   /**
    * Creates a text box with common filters added.
    * 
    * 
    * @param string $name Name of element
    * @param string $label Label of element
    * @return Zend_Form_Element_Text
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
   
   /**
    * Creates a text area with common filters added.
    * 
    * 
    * @param string $name Name of element
    * @param string $label Label of element
    * @return Zend_Form_Element_Textarea
    * 
    */
   public function getCommonTarea($name,$label)
   {
      $elem = new Zend_Form_Element_Textarea($name);
      $elem->setRequired($this->requiredVal)
           ->setLabel($label)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
    
   /**
    * Creates textbox used to enter a student's uuid.
    * 
    * 
    * Sets validators for the min and max string length to 8 which is the length of a uuid
    * @return \Zend_Form_Element_Text 
    */
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
   
   /**
    * Creates textbox used to enter a zipcode.
    * 
    * 
    * Sets validators for the min and max string length to 5 which is the length of a zipcode
    * @return \Zend_Form_Element_Text 
    */
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
   
      
   /**
    * Creates a drop down populated with all coop classes.
    * 
    * 
    * @return \Zend_Form_Element_Select 
    */
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
   
   /**
    * Creates a drop down populated with all assignments.
    * 
    * 
    * @return \Zend_Form_Element_Select 
    */
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


   /**
    * Creates a text box with an INT validator.
    * 
    * 
    * @return \Zend_Form_Element_Text 
    */
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
           ->setLabel('Graduation date (mm/dd/yyyy):')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   /**
    * Creates a drop down populated with majors
    * 
    * 
    * @return \Zend_Form_Element_Select 
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

   /**
    * Creates drop down populated with all coordinator's
    * 
    * 
    * @return \Zend_Form_Element_Select 
    */
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

   
   /**
    * Creates a drop down with all coordinator's and a blank option at the top 
    * (indicating not to filter on a specific coordinator).
    * 
    * 
    * @return \Zend_Form_Element_Select 
    */
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

   /**
    * Creates a drop down populated with all student aids.
    * 
    * 
    * @return \Zend_Form_Element_Select 
    */
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


   /**
    * Creates a drop down with all student aids and a blank option at the top 
    * (indicating not to filter on a specific student aid).
    * 
    * 
    * @return \Zend_Form_Element_Select 
    */
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


   /**
    * Creates a radio with a range of semesters.
    * 
    * 
    * @return \Zend_Form_Element_Radio 
    */
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


   public function getSemesterDropdown()
   {
      $elem = new Zend_Form_Element_Select('semesters_id');
      $semesters = $this->getSemRange();
      $elem->setLabel("Select semester:");

      foreach ($semesters as $s) {
         $elem->addMultiOptions(array($s['id'] => $s['semester']));
      }

      return $elem;

   }
      
   
   /**
    * Creates a text box with a email validator.
    * 
    * 
    * @param string $name Name of element
    * @param string $label Name of element
    * @return \Zend_Form_Element_Text 
    */
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
   
      
   /**
    * Creates a text box with a date validator
    * 
    * 
    * @param string $name Name of element
    * @param string $label Label of element
    * @return \Zend_Form_Element_Text 
    */
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
   
      
   /**
    * Creates a text box with a Float validator.
    * 
    * 
    * @return \Zend_Form_Element_Text 
    */
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
   
      
   /**
    * Creates a radio used to agree to things.
    * 
    * 
    * @param string $label Label of element
    * @param string $name Name of element
    * @return \Zend_Form_Element_Radio 
    */
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
   
   /**
    * Creates a submit button
    * @param type $text
    * @return \Zend_Form_Element_Submit 
    */
   public function getSubmit($text = 'Submit')
   {
      $elem = new Zend_Form_Element_Submit($text);
      return $elem;
   }
   
   
   /** HELPERS **/
   
   /**
    * Retrieves a certain range of semesters
    * 
    * 
    * @return array A range of semesters 
    */
   private function getSemRange()
   {
      $semester = new My_Model_Semester();
      
      // Get current semester.
      //$curSem = $semester->getRealSem();
      $curSem = $semester->fetchRow("current = 1")->semester;
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
