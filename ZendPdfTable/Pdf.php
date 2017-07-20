<?php

namespace sergeynezbritskiy\ZendPdfTable;

class Pdf extends \Zend_Pdf
{

    const TOP = 0;
    const RIGHT = 1;
    const BOTTOM = 2;
    const LEFT = 3;
    const CENTER = 4;    //horizontal center
    const MIDDLE = 5; //vertical center

    public function newPage($param1, $param2 = null)
    {
        #require_once 'Zend/Pdf/Page.php';
        if ($param2 === null) {
            return new \sergeynezbritskiy\ZendPdfTable\Page($param1, $this->_objFactory);
        } else {
            return new \sergeynezbritskiy\ZendPdfTable\Page($param1, $param2, $this->_objFactory);
        }
    }
}


?>