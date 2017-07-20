<?php

namespace sergeynezbritskiy\ZendPdf\Table;

use sergeynezbritskiy\ZendPdfTable\Table\Cell;

class Column extends Cell
{

    private $_colspan = 1;

    public function setColspan($value)
    {
        $this->_colspan = $value;
    }

    public function getColspan()
    {
        return $this->_colspan;
    }
}

?>