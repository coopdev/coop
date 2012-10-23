<?php

class Application_Form_SurveyOptions extends Zend_Form
{
    protected $surveyName;

    // determines whether this form is for class specific survey.
    protected $isClass;

    public function init()
    {
        $surveyName = $this->surveyName;
        $optionAmount = new Zend_Form_Element_Text('option_amount');

        $intValid = new Zend_Validate_Int();
        $optionAmount->setRequired(true)
                     ->setLabel("Set amount of options (Affects forms for all classes but can be overridden for individual classes):")
                     ->addValidator($intValid)
                     ->addFilter('StringTrim')
                     ->addFilter('StripTags')
                     ->setAttrib('size', '4');

        $this->addElement($optionAmount);

        // if class specific survey.
        if ($this->isClass === true) {
           $optionAmount->setLabel("Set amount of options for this evaluation. Will override global amount.");
           // determines if the class survey should use the global option amount.
           $useGlobalRadio = new Zend_Form_Element_Checkbox('use_global');
           $useGlobalRadio->setLabel('Check this box to use global option amount.');
           //$useGlobalRadio->addMultiOptions( array(true => 'Check this if you want to use global option amount instead.') );

           $this->addElement($useGlobalRadio);
        }

        $submit = new Zend_Form_Element_Submit('Submit');
        $this->addElement($submit);

        if ($this->isClass === true) {
           $this->populateSpecificForm();
        } else {
           $this->populateGlobalForm();
        }

    }

    /*
     * Populates the option amount form for a class specific survey.
     * 
     */
    private function populateSpecificForm()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $classId = $coopSess->stuEvalManagementData['classId'];
       $assignId = $coopSess->stuEvalManagementData['assignId'];

       $assign = new My_Model_Assignment();
       $where['classes_id'] = $classId;
       $where['assignments_id'] = $assignId;
       $rows = $assign->getSurveySpecs(array('where' => $where));
       $row = $rows->current();
       if (!empty($row)) {
          $this->populate($row->toArray());
       }
    }

    /*
     * Populates the global option amount form for a survey.
     * 
     */
    private function populateGlobalForm()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $assignId = $coopSess->stuEvalManagementData['assignId'];

       $assign = new My_Model_Assignment();
       $row = $assign->getAssignment($assignId);

       if (!empty($row)) {
          $this->populate($row);
       }
    }

    /*
     * When instantiating this form, if an array is passed to it's constructor with the 
     * array key 'foo', it will call the setFoo() method before the init() method.
     */
    public function setSurveyname($name)
    {
       $this->surveyName = $name;
    }

    public function setIsClass($isClass)
    {
       $this->isClass = $isClass;
    }


}

