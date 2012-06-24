<?php

class Application_Form_AddQuestionStudentEval extends Zend_Form
{

    protected $classId;

    public function init()
    {
       $elems = new My_FormElement();

       $question = new Zend_Form_Element_Textarea('question_text');
       $question->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setLabel("Question:");

       $type = new Zend_Form_Element_Select('question_type');
       $type->setLabel("Question type:");
       $type->addMultiOptions(array('child' => 'Child', 'parent' => 'Parent'));

       $parent = new Zend_Form_Element_Select('parent');
       $parent->setLabel("Choose parent this question belongs to:");
       $aq = new My_Model_AssignmentQuestions();
       $coopSess = new Zend_Session_Namespace('coop');
       $stuEvalData = $coopSess->stuEvalManagementData;
       $where['classes_id'] = $stuEvalData['classId'];
       $where['assignments_id'] = $stuEvalData['assignId'];
       $parents = $aq->getParentQuestions($where);

       foreach ($parents as $p) {
          $parent->addMultiOptions(array($p['question_number'] => $p['question_number']));
       }

       // The assignment's ID
       $assignId = new Zend_Form_Element_Hidden('assignId');
       $assignId->setValue($stuEvalData['assignId']);

       $submit = $elems->getSubmit('Add');

       $this->addElements(array($question, $type, $parent, $assignId, $submit));
    }

    //public function setClassId($classId)
    //{
    //   $this->classId = $classId;
    //}

}

