<?php

namespace sergeynezbritskiy\ZendPdfTable\Table;

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