<?php

namespace sergeynezbritskiy\ZendPdfTable;

/**
 * Class Pdf
 *
 * @package sergeynezbritskiy\ZendPdfTable
 * @deprecated
 */
class Pdf extends \Zend_Pdf
{


    //horizontal center
    //vertical center

    /**
     * @deprecated
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