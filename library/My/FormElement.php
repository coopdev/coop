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
      // get semesters from database to be displayed as options //
      
      $elem = new Zend_Form_Element_Select('enrollDate');
      
      $elem->setRequired(true)
           ->setLabel('When are you planning to enroll in co-op (Semester/Year)?');
           
      return $elem;
   }
   
      
   public function getClassChoiceSelect()
   {  
      // get classes from database to be displayed as options //
      
      $elem = new Zend_Form_Element_Select('classChoice');
      $elem->setRequired(true)
           ->setLabel('Which co-op class are you planning to enroll in?');
           
      return $elem;
   }
   
   public function getCreditAmtTbox()
   {
      $elem = new Zend_Form_Element_Text('creditAmt');
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
      $elem = new Zend_Form_Element_Text('gradDate');
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
      
      $elem = new Zend_Form_Element_Select('Major');
      $elem->setRequired(true)
           ->setLabel('Major:');
                 
      return $elem;
   }
   
   public function getSemesterInMajorRadio()
   {
      $elem = new Zend_Form_Element_Radio('semInMajor');
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
      $dateValidator->setFormat('MM/dd/yyyy');
      
      $elem = new Zend_Form_Element_Text($name);
      $elem->setLabel("$label (mm/dd/yyyy):")
           ->setRequired('true')
           ->addValidator($dateValidator)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
      
   public function getPayRateTbox()
   {  
      
      $elem = new Zend_Form_Element_Text('payRate');
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
}

?>
