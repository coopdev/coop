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
   
   public function getNameTbox($name,$label)
   {
      $elem = new Zend_Form_Element_Text($name);
      $elem->setRequired(true)
           ->setLabel($label)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getFnameTbox()
   {
      $elem = new Zend_Form_Element_Text('fname');
      $elem->setRequired(true)
           ->setLabel('First Name:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getLnameTbox()
   {
      $elem = new Zend_Form_Element_Text('lname');
      $elem->setRequired(true)
           ->setLabel('Last Name:')
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
   
   public function getAddressTbox()
   {
      $elem = new Zend_Form_Element_Text('address');
      $elem->setRequired(true)
           ->setLabel('Address/City/State/ZIP:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   /*
    * Should display semesters as options.
    */
   public function getEnrollDateSelect()
   {
      $elem = new Zend_Form_Element_Select('enrollDate');
      
      $elem->setRequired(true)
           ->setLabel('When are you planning to enroll in co-op (Semester/Year)?');
           
      return $elem;
   }
   
   public function getJobChoiceTbox()
   {
      $elem = new Zend_Form_Element_Text('jobChoice');
      $elem->setRequired(true)
           ->setLabel('What job do you want for your co-op experience?')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getClassChoiceSelect()
   {
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
    * Should this be a dropdown?
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
   
   public function getMajorSelect()
   {
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
   
   public function getPhoneNumTbox()
   {
      $elem = new Zend_Form_Element_Text('phoneNum');
      $elem->setLabel('Telephone number:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getMobileNumTbox()
   {
      $elem = new Zend_Form_Element_Text('mobileNum');
      $elem->setLabel('Mobile number:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getEmailTbox()
   {
      $elem = new Zend_Form_Element_Text('email');
      $elem->setLabel('E-mail:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim')
           ->setRequired(true)
           ->addValidator(new Zend_Validate_EmailAddress());
      return $elem;
   }
   
   
   
   /* Employment information fields below */
   
   public function getJobTitleTbox()
   {
      $elem = new Zend_Form_Element_Text('jobTitle');
      $elem->setLabel('Job title:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getStartDateTbox()
   {  
      $dateValidator = new Zend_Validate_Date();
      $dateValidator->setFormat('MM/dd/yyy');
      $elem = new Zend_Form_Element_Text('startDate');
      $elem->setLabel('Start date (mm/dd/yyyy):')
           ->setRequired('true')
           ->addValidator($dateValidator)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getEndDateTbox()
   {  
      $dateValidator = new Zend_Validate_Date();
      $dateValidator->setFormat('MM/dd/yyy');
      $elem = new Zend_Form_Element_Text('endDate');
      $elem->setLabel('End date (mm/dd/yyyy):')
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
   
   public function getEmployerTbox()
   {
      $elem = new Zend_Form_Element_Text('employer');
      $elem->setLabel('Employer:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getDepartmentTbox()
   {
      $elem = new Zend_Form_Element_Text('department');
      $elem->setLabel('Department:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getJobAddressTbox()
   {
      $elem = new Zend_Form_Element_Text('jobAddress');
      $elem->setLabel('Address/City/State/ZIP:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getSupervNameTbox()
   {
      $elem = new Zend_Form_Element_Text('supervName');
      $elem->setLabel('Supervisor\'s name:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getSupervTitleTbox()
   {
      $elem = new Zend_Form_Element_Text('supervTitle');
      $elem->setLabel('Supervisor\'s title:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getSupervPhoneTbox()
   {
      $elem = new Zend_Form_Element_Text('supervPhone');
      $elem->setLabel('Telephone:')
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   public function getSupervEmailTbox()
   {
      $elem = new Zend_Form_Element_Text('supervEmail');
      $elem->setLabel('E-mail:')
           ->addValidator(new Zend_Validate_EmailAddress())
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      return $elem;
   }
   
   
   
   /* * * * * * * * * * * * * * * * * * * * * 
    * Cooperative Education Agreement Form  *
    *                                       *
    * * * * * * * * * * * * * * * * * * * * */
   
   public function getCoopCoordNameTbox()
   {
      $elem = new Zend_Form_Element_Text();
      $elem->setLabel('Co-op coordinator name:')
           ->setRequired(true)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      
      return $elem;
   }
   
   public function getCoopCoordPhoneTbox()
   {
      $elem = new Zend_Form_Element_Text();
      $elem->setLabel('Co-op coordinator telephone:')
           ->setRequired(true)
           ->addFilter('StripTags')
           ->addFilter('StringTrim');
      
      return $elem;
   }
   
   public function getAgreementRadio()
   {
      $elem = new Zend_Form_Element_Radio('agreement');
              
      $elem->setMultiOptions(array('agree' => 'Agree',
                                    'disagree' => 'Disagree'))
             ->setSeparator('')
             ->setRequired(true);
   }
   
   public function getSubmit()
   {
      $elem = new Zend_Form_Element_Submit('submit');
      return $elem;
   }
}

?>
