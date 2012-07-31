<?php

class FormController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function studentInfoShowAction()
    {
       $assignment = new My_Model_Assignment();
       $assignId = $assignment->getStuInfoId();

       if ($assignment->isDue($assignId)) {
          $this->view->message = "<p class=error> This assignment is past it's due date </p>";
          return;
       }

       // User $this->view->url() to refresh page when changing classes (it return the current url)
       //die(var_dump($this->view->url()));
       $form = new Application_Form_StudentInfo();
       //$form->setIsArray(true);
       
       $coopSess = new Zend_Session_Namespace('coop');
       $form->setAction($coopSess->baseUrl.'/form/student-info-show');

       
       if ($this->_request->isPost()) {
          $data = $_POST;
          //die(var_dump($data));

          if ($form->isValid($data)) {
             //die(var_dump($data));
             $coopSess->validData = $data;
             $this->_helper->redirector('student-info-submit');
          }  

       } else {
          $assignment = new My_Model_Assignment();
          $form = $assignment->populateStuInfoSheet($form);
          $form->getElement('uuid')->setValue("");
          //die(var_dump($form));
       }

       $this->view->form = $form;
       

    }

    public function studentInfoSubmitAction()
    {
       date_default_timezone_set('US/Hawaii');
       $coopSess = new Zend_Session_Namespace('coop');
       if ( isset($coopSess->validData) ) {
          
          $data = $coopSess->validData;
          //die(var_dump($data));
          //$subf1 = $data['subf1'];
          //$subf2 = $data["empinfo"];
          //$subf2 = $subf2[0];
          //$data = $subf1 + $subf2;

          unset($coopSess->validData);

          $assignment = new My_Model_Assignment();
          $assignment->submitStuInfoSheet($data);
          $this->view->message = "<p class=success> Success </p>";
          
       
       }
    }

    public function coopAgreementShowAction()
    {
       $form = new Application_Form_Contract();

       $coopSess = new Zend_Session_Namespace('coop');

       $form->setAction($coopSess->baseUrl . '/form/coop-agreement-pdf');

       $username = $coopSess->username;

       //die($username);

       $user = new My_Model_User();

       $data = $user->fetchRow("username = '$username'")->toArray();
       $form->populate($data);

       $data = $user->getStudentInfo();
       $stuInfo['grad_date'] = $data['grad_date'];
       $form->populate($stuInfo);

       $data = $user->getSemesterInfo(array('username' => $coopSess->username,
                                            'classes_id' => $coopSess->currentClassId,
                                            'semesters_id' => $coopSess->currentSemId));
       $data = $data[0];
       $data = $user->getCoordInfo(array('username' => $data['coordinator']));
       $coordInfo['coord_phone'] = $data[0]['phonenumber'];
       $form->populate($coordInfo);

       //die(var_dump($coordInfo));

       $this->view->form = $form;

    }

    public function coopAgreementPdfAction()
    {
       if ($this->getRequest()->isPost()) {
          $data = $_POST;

          $data = rawurlencode(serialize($data));

          $server = $_SERVER['SERVER_NAME'];

          $coopSess = new Zend_Session_Namespace('coop');
          $baseUrl = $coopSess->baseUrl;

          // Returns the rendered HTML as a string
          //$page = file_get_contents("http://$server$baseUrl/form/coop-agreement-pdf?data=".$data);

          exec(APPLICATION_PATH . "/../pdfs/wkhtmltopdf-i386  http://$server$baseUrl/form/coop-agreement-pdf?role=A592NXZ71680STWVR926\&data=$data " . 
                  APPLICATION_PATH . '/../pdfs/coopAgreement.pdf');

          $pdfPath = APPLICATION_PATH . '/../pdfs/coopAgreement.pdf';
          $pdf = Zend_Pdf::load($pdfPath);
          header("Content-Disposition: attachment; filename=Coop Agreement.pdf");
          header("Content-type: application/x-pdf");
          $pdfData = $pdf->render();

          echo $pdfData;

          $this->_helper->layout->disableLayout();
          $this->_helper->viewRenderer->setNoRender(true);


       // For the wkhtmltopdf GET request
       } else if ($this->getRequest()->isGet()) {

          if (isset($_GET['data'])) {
             $data = $_GET['data'];

             $data = unserialize(rawurldecode($data));

             //die($data);

             $form = new Application_Form_Contract();
             $form->removeElement('Submit');
             $form->populate($data);

             $this->view->form = $form;

             $this->_helper->layout->disableLayout();
          }


       }

    }

    public function editDisclaimerAction()
    {

       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          unset($data['Submit']);
          //die(var_dump($data));
          $db = new My_Db();
          try {
             $db->update('coop_disclaimer_text', $data);
             $this->view->message = "<p class=success> Updated successfully </p>";
          } catch(Exception $e) {
             $this->view->message = "<p class=error> Error occured </p>";
          }
       }

       $form = new Zend_Form();

       $elems = new My_FormElement();

       $db = new My_Db();
       $res = $db->select()->from('coop_disclaimer_text');
       $res = $db->fetchAll($res);
       $res = $res[0];

       $tArea = $elems->getCommonTarea('text', 'Enter text for disclaimer page:');
       $tArea->removeFilter('StripTags');
       $submit = $elems->getSubmit();
       $form->addElements(array($tArea, $submit));
       $form->populate($res);

       $this->view->form = $form;
    }


    private function handlePost($form, $data)
    {
       $coopSess = new Zend_Session_Namespace('coop');
       if ($form->isValid($data)) {
          if ($data['agreement'] == 'agree') {
             $coopSess->validData = $data;
             return true;
          } else {
             $this->view->message = 'Must agree before continuing';
             $form->populate($data);
             return false;
          }
       } else {
          return false;
       }
       
    }

    //public function stuinfoFormTemplateAction()
    //{
    //   //$this->view->form = new Application_Form_StudentInfo();
    //}

}







