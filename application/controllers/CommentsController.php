<?php

class CommentsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function newAction()
    {
       $searchForm = new Application_Form_StudentRecSearch();
       $searchForm->setDecorators( array('ViewHelper', 'Errors'));
       $searchForm->removeElement('semesters_id');
       $searchForm->removeElement('classes_id');
       $searchForm->removeElement('coordinator');
       $this->view->searchForm = $searchForm;

       $commentForm = new Application_Form_Comment();
       $this->view->commentForm = $commentForm;

    }



}

