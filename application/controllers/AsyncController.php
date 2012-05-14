<?php

class AsyncController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }


    // Retrieve and displays student records after search
    public function studentRecSearchResultAction()
    {
       if ($this->_request->isPost()) {
          $data = $_POST;
          //die(var_dump($data));

          $user = new My_Model_User();
          $results = $user->searchStudentRecs($data);

          

          $this->view->results = $results;

       } else {
          $this->_helper->viewRenderer->setNoRender();
       }

       $this->_helper->getHelper('layout')->disableLayout();
    }

    // Records of submitted assignments for student, semester, class combination
    public function submissionRecsAction()
    {
       if ($this->getRequest()->isPost()) {
          $data = $_POST['data'][0];
          //die(var_dump($data));

          //die(var_dump($arr));

          $sub = new My_Model_SubmittedAssignment();
          $recs = $sub->getSubmissionRec($data);

          $this->view->recs = $recs;

          //die(var_dump($recs));

       } else {
          $this->_helper->viewRenderer->setNoRender();
       }

       $this->_helper->getHelper('layout')->disableLayout();
    }


    public function classRollJsonAction()
    {
       if ($this->getRequest()->isPost()) {

          $id = $_POST['id'];

          $class = new My_Model_Class();

          $rows = $class->getRollForCurrentSem($id);

          $json = Zend_Json_Encoder::encode($rows);
          //$json = json_encode($rows);

          echo $json;

       } 

       $this->_helper->viewRenderer->setNoRender();
       $this->_helper->getHelper('layout')->disableLayout();
    }

}



