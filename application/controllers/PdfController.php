<?php

class PdfController extends Zend_Controller_Action
{

    public function init()
    {
        require_once(APPLICATION_PATH . "/../external-classes/MPDF54/mpdf.php");
    }


    public function timesheetAction()
    {
       $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->isPost()) {
            $formData = $_POST;
            $formData = rawurlencode(serialize($formData));

            $serverName = $_SERVER['SERVER_NAME'];
            $baseUrl = $this->view->baseUrl();
            $url = $this->view->url(array('action' => 'timesheet', 
                                    'formData' => $formData,
                                    'pdfRole' => 'A592NXZ71680STWVR926'));
            $url = "http://$serverName" . $baseUrl . $url;

            $html = fopen($url, 'r');
            $html = stream_get_contents($html);

            $mpdf = new mPDF('c', 'A4', '', '', 0,0,0,0,0,0);
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->WriteHTML($html);
            $mpdf->Output();
            //$this->_helper->redirector('timesheet', 'assignment');


        } else if ($this->getRequest()->isGet()) {
            $formData = $this->getRequest()->getParam('formData');
            $formData = str_replace('\\', '', $formData);
            $formData = unserialize(rawurldecode($formData));
            //die(var_dump($formData));
            $form = new Application_Form_TimeSheet(array('populateForm' => false));
            $form->populate($formData);

            $this->view->form = $form;

        }
    }


}

