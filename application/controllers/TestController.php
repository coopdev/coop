<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }


    public function dbqueriesAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $link = My_DbLink::connect();
       //$config = new Zend_Config_Ini(APPLICATION_PATH.
       //                            '/configs/application.ini','production');

       $db = new My_Db();
      $semester = new My_Semester();
      $currentSem = $semester->getCurrentSem();
      $coopSess->currentSemId = $db->getId('coop_semesters', array('semester' => $currentSem));

      $coopSess->classIds = $db->getCols('coop_users_semesters', 
                                'classes_id',
                                array('student'=>'johndoe', 
                                   'semesters_id' => $coopSess->currentSemId));
      die(var_dump($coopSess->classIds));
       $cols = $db->insertFormData('blah');
       
       $row = $db->fetchRow("SELECT * FROM coop_roles where role = 'user'");
       //die(var_dump($cols));


       //$link->update('coop_classes', $updates, 'id = 1');
       
       //$statement = $select->from('coop_users_contracts');
       $statement = $select->from('coop_roles');
       $rows = $link->fetchAll($statement);
       //die(var_dump($rows));
       
       /* Testing performance for class instantiation and db queries. */
//       for ($i = 0; $i < 10; $i++) {
//          $link = My_DbLink::connect();
//          $users = $link->fetchAll('SELECT * FROM coop_users');
//          $a = 'hi';
//       
    }

    public function paginateAction()
    {
       $paginator = Zend_Paginator::factory($statement);
       $currentPage = 1;
       $i = $this->getRequest()->getQuery('i');
       
       if (!empty($i)) {
          $currentPage = $i;
       }
       
       $paginator->setItemCountPerPage(1);
       $paginator->setPageRange(2);
       $paginator->setCurrentPageNumber($currentPage);
       
       
       $this->view->paginator = $paginator;
    }

    public function formAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $form = new Application_Form_Contract();
       $testForm = new Application_Form_Test();
       $this->view->form = $form;
       
//       if ($this->getRequest()->isPost()) {
//          if ($form->isValid($_POST)) {
//             
//          } else {
//             $this->view->form = $testForm;
//          }
//       
    }

    public function semesterAction()
    {
//      $curDate = date('Y-m-d');
//      $dateParts = explode('-',$curDate);
//      $curYear = $dateParts[0];
//      $curMonth = $dateParts[1];
//      $curSem = '';
//      
//      if ($curMonth < 7) {
//         $curSem = 'Spring';
//      } else {
//         $curSem = 'Fall';
//      }
//      
//      $curSem .= ' ' . $curYear;
//      die($curSem);
       
        $link = My_DbLink::connect();
        $semester = new My_Semester();
        $curSem = $semester->getCurrentSem();
        
        $semPieces = explode(' ',$curSem);
        $curYear = (int)$semPieces[1];
        //$curYear = 2018;
        
        $semesters = $link->fetchRow('SELECT semester FROM coop_semesters');
        
        
                
        $firstSem = $semesters['semester'];
        //die(var_dump($firstSem));
        $firstSem = explode(' ', $firstSem);
        $firstYear = (int)$firstSem[1];
        
        if ($curYear != $firstYear) {
           $link->query('DELETE FROM coop_semesters');
           $query = 'INSERT INTO coop_semesters (semester) VALUES (?)';
           for ($i = $curYear; $i < $curYear+5; $i++) {
              $link->query($query, "Spring $i");
              $link->query($query, "Fall $i");
           }
        }
        
        //test. This can be used to just retrieve a specific range 
        // (e.g. from current semester to 5 years ahead) while keeping more than
        // that range in the database (possibly for checking histories of students).
        // One problem is that the query is returning the results in order (all Fall
        // semesters are returned before Spring semesters), so it mighth take 
        // extra processing to get in proper order to be displayed in select box.
        $yr2 = $curYear+1;
        $yr3 = $curYear+2;
        $yr4 = $curYear+3;
        $yr5 = $curYear+4;
        
        $sems = $link->fetchAll("SELECT semester FROM coop_semesters 
                WHERE semester LIKE '%$curYear%'
                OR semester like '%$yr2%'
                OR semester like '%$yr3%'
                OR semester like '%$yr4%'
                OR semester like '%$yr5%'
                ORDER BY SUBSTRING_INDEX(semester,' ',-1),
                SUBSTRING_INDEX(semester,' ', 1) DESC");
        die(var_dump($sems));
        //end test
        //die(var_dump($firstYear));
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function decoratorAction()
    {
       $form = new Zend_Form();
       $form->setAction('/test/decorator');

       $form->addElement('text', 'fname');
       $form->addElement('text', 'lname');
       $form->addElement('text', 'pass');
       $form->addElement('text', 'user');
       $form->addElement('submit', 'submit');

       $form->getElement('fname')->setRequired(true);

       $form->addDisplayGroup(array('fname', 'lname'),  'first');
       $form->addDisplayGroup(array('pass', 'user'),  'second');
       $form->addDisplayGroup(array('submit'),  'submitrow');

       $form->setDisplayGroupDecorators(array('FormElements',
                                              array('HtmlTag', array('tag' => 'div', 'class' => 'fields'))
                                       ));

       $form->setElementDecorators(array('ViewHelper',
                                         'Errors',
                                         array('HtmlTag', array('tag' => 'span', 'class' => 'fields'))
                                   ));

       $this->view->form = $form;

       if ($this->_request->isPost()) {
          $data = $_POST;

          //die(var_dump($data));

          if ($form->isValid($data)) {       
             die('hi');
          }

          //$errors = $form->getMessages();
          //die(var_dump($errors));

       }

       //$dec->render($text);
       //die(var_dump($name, $label));
    }

    public function subformAction()
    {
       
       $this->view->form = $form;
    }


}

