<?php

class Application_Form_ViewLogins extends Zend_Form
{
    public function init()
    {
       $this->setAttrib('id', "searchform");
       $elems = new My_FormElement();

       $fname = $elems->getCommonTbox('fname', 'First name:');
       $lname = $elems->getCommonTbox('lname', 'Last name:');

       $username = $elems->getCommonTbox('username', 'Username:');

       $startDate = $elems->getDateTbox('startDate', "");
       $startDate->setLabel("Specify date to start search from. Leaving both date boxes blank will return all login records (mm/dd/yyyy): ")
                 ->setRequired(false);
       $endDate = $elems->getDateTbox('endDate', "");
       $endDate->setLabel("Specify date to end search (mm/dd/yyyy): ")
               ->setRequired(false);

       $limit = $elems->getCommonTbox('limit', 'Set limit on amount of records returned');
       $limit->setValue("50");

       $submit = new Zend_Form_Element_Button("search");
       $submit->setLabel("Search");

       $this->addElements(array($fname, $lname, $username, $startDate, $endDate, $limit, $submit));
    }
}

