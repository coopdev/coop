<?php

class BackupController extends Zend_Controller_Action
{
    private $request = '';

    public function init()
    {
       $this->request = $this->getRequest();
    }

    public function indexAction()
    {
       $request = $this->request;
       $backup = new My_Model_Backups();

       $doBackupForm = new Zend_Form;
       $submitBackup = new Zend_Form_Element_Submit('Backup Database');
       $doBackupForm->addElement($submitBackup);

       $delBackupForm = new Application_Form_DeleteBackups();

       $this->view->doBackupForm = $doBackupForm;
       //$this->view->delBackupForm = $delBackupForm;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          // $data['backups'] holds the backup IDs
          if (isset($data['backups'])) {
             $backups = $data['backups'];
          }
          //var_dump($data);

          // If backing up.
          if (isset($data['BackupDatabase'])) {

             $backup->backup();
             $delBackupForm = new Application_Form_DeleteBackups();

          // if deleting backups.
          } else if (isset($data['Delete'])) {
             if ($delBackupForm->isValid($data)) {

                $backup->destroy($backups);
                
                $delBackupForm = new Application_Form_DeleteBackups();

             }
          }
       // if restoring a backup
       } else if ($this->getRequest()->isGet() && $request->getParam('Restore')) {
          $backupName = $request->getParam('Restore');
          //var_dump($backupName);

          $backup->restore($backupName);

          $delBackupForm = new Application_Form_DeleteBackups();

       }

       $this->view->delBackupForm = $delBackupForm;

    }

}

