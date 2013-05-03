<?php

class PdfController extends Zend_Controller_Action
{

    public function init()
    {
        require_once(APPLICATION_PATH . "/../external-classes/MPDF54/mpdf.php");

        $this->pdfRole = 'A592NXZ71680STWVR926';
    }


    // Does not use generatePdf action because it requires special handling.
    public function timesheetAction()
    {
        $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->isPost()) {
            $formData = $_POST;
            //die(var_dump($formData));
            $formData = rawurlencode(serialize($formData));

            //$serverName = $_SERVER['SERVER_NAME'];
            $serverName = $this->view->serverUrl();
            $url = $this->view->url(array('action' => 'timesheet', 
                                    'formData' => $formData,
                                    'pdfRole' => $this->pdfRole));
            $url = $serverName . $url;

            $html = fopen($url, 'r');
            $html = stream_get_contents($html);

            //$mpdf = new mPDF('c', 'A4', '', '', 0,0,0,0,0,0);
            $mpdf = new mPDF('c');
            //$mpdf->debug = true;
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

    

    // Generic PDF action that doesn't require special handling.
    public function generatePdfAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->isGet()) {
            //$formData = $_POST;
            //$formData = rawurlencode(serialize($formData));

            $assignment = $this->getRequest()->getParam("assignment");

            $formMetaData = array();
            if ($coopSess->role === 'user') {
                $formMetaData['classId'] = $coopSess->currentClassId;
                $formMetaData['username'] = $coopSess->username;
                $formMetaData['semId'] = $coopSess->currentSemId;
            } else {
                $subForStudentData = $coopSess->submitForStudentData;
                $formMetaData['classId'] = $subForStudentData['classes_id'];
                $formMetaData['username'] = $subForStudentData['username'];
                $formMetaData['semId'] = $subForStudentData['semesters_id'];
            }
            $formMetaData = rawurlencode(serialize($formMetaData));
            

            //$serverName = $_SERVER['SERVER_NAME'];
            $serverName = $this->view->serverUrl();
            //$url = $this->view->url(array('action' => 'supervisor-eval', 
            //                        //'formData' => $formData,
            //                        'pdfRole' => $this->pdfRole,
            //                        'formMetaData' => $formMetaData));
            
            $url = $this->view->url(array('action' => 'html-to-convert', 
                                    //'formData' => $formData,
                                    'pdfRole' => $this->pdfRole,
                                    'formMetaData' => $formMetaData,
                                    'assignment' => $assignment
                                    ));
            
            $url = $serverName . $url;

            $html = fopen($url, 'r');
            $html = stream_get_contents($html);

            //$mpdf = new mPDF('c', 'A4', '', '', 0,0,0,0,0,0);
            $mpdf = new mPDF('c');
            $mpdf->debug = true;
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }

    }
    
    
    // View that has the HTML version of the forms that will be converted to PDF.
    public function htmlToConvertAction()
    {
        $this->_helper->getHelper('layout')->disableLayout();
        
        //$formData = $this->getRequest()->getParam('formData');
        //$formData = str_replace('\\', '', $formData);
        //$formData = unserialize(rawurldecode($formData));

        $formMetaData = $this->getRequest()->getParam('formMetaData');
        $formMetaData = str_replace('\\', '', $formMetaData);
        $formMetaData = unserialize(rawurldecode($formMetaData));
        $formMetaData['populateForm'] = false;

        $assignment = $this->getRequest()->getParam("assignment");

        if ($assignment === "supervisor-eval") {
            $this->view->form = new Application_Form_SupervisorEval($formMetaData);
            $this->view->form->populateJobsiteFields();
            $this->render("supervisor-eval");
        } else if ($assignment === "coop-agreement") {
            $this->view->form = new Application_Form_Agreement($formMetaData);
            $this->view->form->populateJobsiteFields();
            $this->render("coop-agreement");
        }

        //$form->populate($formData);

    }

}