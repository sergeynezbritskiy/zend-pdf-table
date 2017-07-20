<?php

namespace sergeynezbritskiy\ZendPdfTable;

/**
 * Class Pdf
 *
 * @package sergeynezbritskiy\ZendPdfTable
 * @deprecated1
 */
class Pdf extends \Zend_Pdf
{

    /**
     * @deprecated1
     */
    const TOP = 0;
    /**
     * @deprecated1
     */
    const RIGHT = 1;
    /**
     * @deprecated1
     */
    const BOTTOM = 2;
    /**
     * @deprecated1
     */
    const LEFT = 3;
    /**
     * @deprecated1
     */
    const CENTER = 4;    //horizontal center
    /**
     * @deprecated1
     */
    const MIDDLE = 5; //vertical center

    /**
     * @deprecated1
     * @param mixed $param1
     * @param null $param2
     * @return \sergeynezbritskiy\ZendPdfTable\Page|\Zend_Pdf_Page
     */
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