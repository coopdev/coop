<?php

class TestController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function jsonAction()
    {
       $array = array('fname' => 'hi', 'lname' => 'bye');
       $user = new My_Model_User();
       $newRow = $user->createRow();
       $json = json_encode($array);

       var_dump($json);

       $array = json_decode($json);

       foreach ($array as $a) {
          echo "<br /> $a";
       }
    }

    public function loginAction()
    {
       $login = new My_Model_Logins();

       $login->getLogins(array("u.username = 'johndoe'"));

    }

    public function dbRowAction()
    {
       $aq = new My_Model_User();

       $newRow = $aq->fetchNew();

       $newRow->setFromArray(array('fname' => 'newest', 'blah' => 'blah'));
       $newRow->save();
       die('hi');

       $res = $aq->select();

       $rows = $aq->fetchAll($res);

       //$rows->

       //die(var_dump($rows));
       $row = $rows->getRow(0);

       $row->setFromArray(array('fname' => 'Joseph', 'blah' => 'blah', 'llname' => 'new lname'));
       die(var_dump($row->getTableClass()));

       //$row->
       $row->save();
       
       //die(var_dump($row->isConnected()));

       $row2 = new Zend_Db_Table_Row();
       //$row->setFro
       die(var_dump($row2->getTable()));

       die(var_dump($qType));





       //$rows = $aq->getParentQuestions(array('assignments_id' => 7, 'classes_id' => 4));

    }

    public function indexAction()
    {
        // action body
    }
    
    public function htmlStringAction()
    {

       //$doc = new DOMDocument();
       //$doc->loadHTMLFile(APPLICATION_PATH . '/views/scripts/form/coop-agreement-template.phtml');
       //die(var_dump($doc->saveHTML()));

       $name = $_SERVER['SERVER_NAME'];

       //die($name);

       $coopSess = new Zend_Session_Namespace('coop');

       //$page = file_get_contents('http://coop/form/coop-agreement-show?uname=');
       $page = file_get_contents("http://$name/form/coop-agreement-show?uname=");

       $ofile = fopen('/var/www/coop/pdfs/temp.html', 'w');
       fwrite($ofile, $page);
       fclose($ofile);

       die(var_dump($page));

       $this->_helper->viewRenderer->setNoRender(true);
       
    }

    public function dbqueriesAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $link = My_DbLink::connect();
       //$config = new Zend_Config_Ini(APPLICATION_PATH.
       //                            '/configs/application.ini','production');

       $db = new My_Db();

       $temp = new Application_Model_DbTable_Assignment();

       $row = $temp->rowExists(array('username' => 'vlah'));
       //die(var_dump($row));
      $semester = new My_Semester();
      $currentSem = $semester->getRealSem();
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
        $semester = new My_Model_Semester();
        $curSem = $semester->setCurrentSem();
        
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
       $coopSess = new Zend_Session_Namespace('coop');

       if (isset($coopSess->subfcount) && $coopSess->subfcount > 0) {

          // Preserve the subfcount if it's greater than 0
          $sfCount = $coopSess->subfcount;

       }

       $coopSess->subfcount = 0;
       $form = new Application_Form_StudentInfo();

       $this->view->form = $form;

       if ($this->getRequest()->isPost()) {
          $data = $_POST;
          if ($coopSess->dynamicForm instanceof Application_Form_StudentInfo) {

             $dynaForm = $coopSess->dynamicForm;

             //unset($coopSess->dynamicForm);
             //die(var_dump($data));
             if ($dynaForm->isValid($data)) {

             } else {

                // If not valid, must use the preserved subfcount so the count doesn't reset
                // causing users to lose the subforms they have added.
                if (isset($sfCount)) {
                   $coopSess->subfcount = $sfCount;
                }
                $this->view->form = $dynaForm;
             }

          } else {
             $form->isValid($data);
          }
       } else {
          if (isset($coopSess->dynamicForm)) {
             unset($coopSess->dynamicForm);
          }
          
       }
       
    }

    public function addSubformAction()
    {
       $coopSess = new Zend_Session_Namespace('coop');
       $coopSess->dynamicForm = new Application_Form_StudentInfo();
       $form = $coopSess->dynamicForm;
       $form->setAction('/test/subform');

       if ($this->getRequest()->isPost()) {

          $data = $this->getRequest()->getPost('data');
          $flag = $this->getRequest()->getPost('flag');

          $form->makeDynaForm($flag, $data);

          $coopSess->dynamicForm = $form;

          //if ($flag == "addsf") {
          //   //die('hi');
          //   $coopSess->subfcount++;
          //} else if ($flag == "rmsf") {
          //   $coopSess->subfcount--;
          //   if ($coopSess->subfcount < 0) {
          //      $coopSess->subfcount = 0;
          //   }
          //}

          //$subfcount = $coopSess->subfcount;

          //for ($i = 0; $i < $subfcount; $i++) {
          //   $num = $i + 1;
          //   $sfname = "empinfo";
          //   $empInfoText = new Zend_Form_Element_Hidden("empinfoText$num");
          //   //die(var_dump($empInfo));
          //   $empInfoText->setLabel("EMPLOYMENT INFORMATION (If you are currently working at a job related to your major please describe below)");
          //   $empInfoText->setDecorators(array('ViewHelper',
          //                                  array('Label', array('tag' => 'p', 'style' => 'font-size: 14px;border-width:1px;border-style:solid;padding:10px')),
          //                                  array('HtmlTag', array('tag' => 'br', 'placement' => 'PREPEND'))
          //                            ));
          //   $form->addElement($empInfoText);
          //   $empSubf = $form->makeEmpSubf();
          //   $empSubf->setElementsBelongTo("$sfname\[$num]");
          //   
          //   $form->addSubForm($empSubf, "$sfname\[$num]");
          //   $addsf = $form->getElement('addsf');
          //   $form->removeElement('addsf');
          //   $rmsf = $form->getElement('rmsf');
          //   $form->removeElement('rmsf');
          //   $agreeLabel = $form->getElement('partAgreement');
          //   $form->removeElement('partAgreement');
          //   $agree = $form->getElement('agreement');
          //   $form->removeElement('agreement');
          //   $submit = $form->getElement('Submit');
          //   $form->removeElement('Submit');
          //   $form->addElements(array($addsf, $rmsf, $agreeLabel, $agree, $submit));

          //}
          //$form->populate($data);
          //$coopSess->dynamicForm = $form;
          //die(var_dump($coopSess->dynamicForm));
          
          //$this->_helper->redirector('get-form');

          //} else {
          //   $form = $coopSess->dynamicForm;
          //   $form->isValid($_POST);
          //}

          $form->setSubFormDecorators(array('FormElements',
                                             array('HtmlTag', array('tag' => 'table', 'class' => 'studentInfo'))
                                      ));
          echo $form;

          $this->_helper->viewRenderer->setNoRender();
          $this->_helper->getHelper('layout')->disableLayout();
       } else {
          //echo "<h2> Access Denied </h2>";
       }


       //echo $form;


    }

    public function subfAction()
    {
       $form = new Zend_Form();
       $sf1 = new Zend_Form_SubForm();
       //$sf2 = new Zend_Form_SubForm();

       $sf1->addElement('text', 'fname')
           ->addElement('text', 'lname');


       //$sf2->addElement('text', 'fname')
       //    ->addElement('text', 'lname');

       $sf2 = $sf1;

       $form->addSubForm($sf1, 'sf1');
       $form->addSubForm($sf2, 'sf2');

       $this->view->form = $form;

		 $this->_helper->getHelper('layout')->disableLayout();
    }

    public function blahAction()
    {
       $user = new My_Model_User();

       $rows = $user->getEmpInfo(array('username' => 'kuukekoa', 'classes_id' => 4, 'semesters_id' => 9));
       die(var_dump($rows));
    }

    public function simplePagerAction()
    {
       
    }


}



