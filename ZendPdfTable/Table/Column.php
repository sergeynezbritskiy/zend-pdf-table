<?php

namespace sergeynezbritskiy\ZendPdfTable\Table;

/**
 * Class Column
 *
 * @package sergeynezbritskiy\ZendPdfTable\Table
 */
class Column extends Cell
{

    /**
     * @var int
     */
    private $_colspan = 1;

    /**
     * @param int $value
     */
    public function setColspan($value)
    {
        $this->_colspan = $value;
    }

    /**
     * @return int
     */
    public function getColspan()
    {
        return $this->_colspan;
    }
}
