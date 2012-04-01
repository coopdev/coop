<?php

class Application_Form_Test extends Application_Form_Contract
{

    public function init()
    {
        $this->makeElems();
        $this->addElement($this->fname);
    }

}

