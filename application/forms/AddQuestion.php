<?php

class Application_Form_AddQuestion extends Zend_Form
{

    public function init()
    {
       $elems = new My_FormElement();

       $question = new Zend_Form_Element_Textarea('question_text');
       $question->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setLabel("Question:");

       $answerLen = $elems->getCommonTbox('answer_minlength', "Set answer's minimum length:");

       // The assignment's ID
       $assignId = new Zend_Form_Element_Hidden('assignId');

       $submit = $elems->getSubmit('Add');

       $this->addElements(array($question, $answerLen, $assignId, $submit));
    }


}

