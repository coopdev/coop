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

       $form->setAction('/form/coop-agreement-pdf');

       $coopSess = new Zend_Session_Namespace('coop');
       $username = $coopSess->username;


       $user = new My_Model_User();

       $data = $user->fetchRow("username = '$username'")->toArray();

       $form->populate($data);

       $this->view->form = $form;

    }

    public function coopAgreementPdfAction()
    {
       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          $form = new Application_Form_Contract();

          $form->populate($data);
          //die(var_dump($data));

          $this->view->form = $form;
          $this->_helper->layout->disableLayout();

          $pdfPath = APPLICATION_PATH . '/../pdfs';
          $pdfPath = realpath($pdfPath);
          $filePath = $pdfPath . '/test.pdf';
          //die($filePath);
          //die($pdfPath);

          require_once(APPLICATION_PATH . '/../external-classes/WkHtmlToPdf.php');

          $pdf = new WkHtmlToPdf();

          $pdf->addPage('http://coop/form/coop-agreement-pdf');
          $pdf->saveAs('/var/www/pdfs/test.pdf');
          //$pdf->send('test.pdf');

          //$result = exec("wkhtmltopdf http://coop/form/coop-agreement-pdf $filePath", $output, $return);
          //$result = exec("/usr/bin/wkhtmltopdf http://coop/form/coop-agreement-pdf /var/www/coop/pdfs/test.pdf", $output, $return);

          sleep(5);
          //$result = exec("echo 'hello'");

          //die(var_dump($result));
          //die(var_dump($return));

          //$this->_helper->redirector('test');
       }

       //$coopSess = new Zend_Session_Namespace('coop');
       //$username = $coopSess->username;


       //$user = new My_Model_User();

       //$data = $user->fetchRow("username = '$username'")->toArray();

       //require_once(APPLICATION_PATH . '/../tcpdf/tcpdf.php');
       //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
       ////$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
       //$htmlcontent = $this->view->render('/form/coop-agreement-pdf.phtml');
       //// output the HTML content
       //$pdf->writeHTML($htmlcontent, true, 0, true, 0);
       //$pdf->lastPage();
       //$pdf->Output("pdf-name.pdf", 'D');


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

    public function stuinfoFormTemplateAction()
    {
       //$this->view->form = new Application_Form_StudentInfo();
    }

}







