<?php

class Application_Form_EditQuestionStudentEval extends Zend_Form
{

    public function init()
    {
       $this->setAttrib('class', 'editQuestions');
       $as = new My_Model_Assignment();

       $coopSess = new Zend_Session_Namespace('coop');
       $stuEvalMngmt = $coopSess->stuEvalManagementData;
       $assignId = $stuEvalMngmt['assignId'];
       $where['classes_id'] = $stuEvalMngmt['classId'];
       $where['question_type'] = 'parent';

       $parents = $as->getQuestions($assignId, $where, 'question_number');

       foreach ($parents as $p) {
          $subf = new Zend_Form_SubForm();

          $question = new Zend_Form_Element_Textarea('question_text');
          $question->setLabel("Header ". $p['question_number'])
                   ->setRequired(true)
                   ->setValue($p['question_text']);

          $subf->addElement($question);

          $this->addSubForm($subf, "".$p['id']."");

          // where clause to get child questions
          $where['parent'] = $p['question_number'];
          $where['question_type'] = 'child';

          $children = $as->getQuestions($assignId, $where, 'question_number');

          foreach ($children as $c) {
             $subf = new Zend_Form_SubForm();

             $question = new Zend_Form_Element_Textarea('question_text');
             $question->setLabel($c['question_number'])
                      ->setRequired(true)
                      ->setValue($c['question_text']);

             $subf->addElement($question);

             $this->addSubForm($subf, "".$c['id']."");
          }
       }

       $elems = new My_FormElement();
       $submit = $elems->getSubmit();

       $this->addElement($submit);

    }

}

