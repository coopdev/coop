<?php

class Application_Form_DeleteBackups extends Zend_Form
{

    public function init()
    {
       //$this->setAction('/backup/index');
       //$this->setMethod('POST');
       $backupList = new Zend_Form_Element_MultiCheckbox('backups');
       $backupList->setRequired(true);

       $backup = new My_Model_Backups();
       $backups = $backup->getAll();
       //die(var_dump($backups));

       foreach ($backups as $b) {
          $backupList->addMultiOptions(array($b->name => $b->name));
       }

       $delSubmit = new Zend_Form_Element_Submit('Delete');
       $restoreSubmit = new Zend_Form_Element_Submit('Restore');

       // for template
       $this->setDecorators( array( 
           array('ViewScript', array('viewScript' => '/backup/delete-backups-template.phtml'))));

       $this->addElements(array($backupList, $delSubmit, $restoreSubmit));

       // CLEAR DECORATORS FOR TEMPLATE
       $this->setElementDecorators(array('ViewHelper',
                                          "Errors"));
    }


}

