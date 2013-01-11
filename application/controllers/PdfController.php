<?php

class PdfController extends Zend_Controller_Action
{

    public function init()
    {
        require_once(APPLICATION_PATH . "/../external-classes/MPDF54/mpdf.php");

        $this->pdfRole = 'A592NXZ71680STWVR926';
    }


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

    public function supervisorEvalAction()
    {
        $coopSess = new Zend_Session_Namespace('coop');
        $this->_helper->getHelper('layout')->disableLayout();
        if ($this->getRequest()->isPost()) {
            //$formData = $_POST;
            //$formData = rawurlencode(serialize($formData));

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
            $url = $this->view->url(array('action' => 'supervisor-eval', 
                                    //'formData' => $formData,
                                    'pdfRole' => $this->pdfRole,
                                    'formMetaData' => $formMetaData));
            $url = $serverName . $url;
            //die($url);

            $html = fopen($url, 'r');
            $html = stream_get_contents($html);

            //$mpdf = new mPDF('c', 'A4', '', '', 0,0,0,0,0,0);
            $mpdf = new mPDF('c');
            $mpdf->debug = true;
            $mpdf->SetDisplayMode('fullpage');
            $mpdf->WriteHTML($html);
            $mpdf->Output();
            //$this->_helper->redirector('timesheet', 'assignment');


        } else if ($this->getRequest()->isGet()) {
            //$formData = $this->getRequest()->getParam('formData');
            //$formData = str_replace('\\', '', $formData);
            //$formData = unserialize(rawurldecode($formData));

            $formMetaData = $this->getRequest()->getParam('formMetaData');
            $formMetaData = str_replace('\\', '', $formMetaData);
            $formMetaData = unserialize(rawurldecode($formMetaData));
            $formMetaData['populateForm'] = false;
            //die(var_dump($formMetaData));
            $form = new Application_Form_SupervisorEval($formMetaData);
            //$form = new Application_Form_SupervisorEval(array('username' => 'johndoe', 'classId' => '4', 'semId' => '14'));
            //$form->populate($formData);

            $this->view->form = $form;

        }
    }

}

