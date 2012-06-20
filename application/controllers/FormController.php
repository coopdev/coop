<?php

class FormController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function studentInfoShowAction()
    {

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

    // displays coop agreement pdf
    public function testAction()
    {

       $this->_helper->layout->disableLayout();
       $path = APPLICATION_PATH . '/views/scripts/form/test.pdf';
       //die(var_dump($path));
       $pdf = Zend_Pdf::load(APPLICATION_PATH . '/views/scripts/form/test.pdf');
       //$pdf->pages[] = new Zend_Pdf_Page(Zend_Pdf_Page::SIZE_A4);
       //die(var_dump($pdf));
       header("Content-Disposition: inline; filename=Coop-Agreement.pdf");
       header("Content-type: application/x-pdf");
       $pdfData = $pdf->render();

       echo $pdfData;



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







