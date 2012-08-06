<?php

class SemesterController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function changeCurrentAction()
    {
       $sem = new My_Model_Semester();

       $coopSess = new Zend_Session_Namespace('coop');
       $curSemId = $coopSess->currentSemId;
       //die(var_dump($curSemId));
       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          $sem = new My_Model_Semester();

          if (array_key_exists('Next', $data)) {
             $result = $sem->nextSem();
          } else {
             $result = $sem->prevSem();
          }

       }
       $this->view->curSem = $sem->fetchRow("current = 1");
    }


}



