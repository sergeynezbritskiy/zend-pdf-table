<?php

namespace sergeynezbritskiy\ZendPdfTable;

use sergeynezbritskiy\ZendPdfTable\Table\Column;
use sergeynezbritskiy\ZendPdfTable\Table\Row;

/**
 * Class Table
 *
 * @package sergeynezbritskiy\ZendPdfTable
 */
class Table extends AbstractElement
{

    private $_width;
    private $_autoWidth = true;

    /**
     * @var Row[]
     */
    private $_rows;
    private $_headerRow;
    private $_pages;        //spanning pages or this table
    private $_repeatHeader = true;

    private $_margin = [0, 0, 0, 0];

    /**
     * Set page margins
     *
     * @param array (TOP,RIGHT,BOTTOM,LEFT)
     */
    public function setMargins($margin = array())
    {
        $this->_margin = $margin;
    }

    /**
     * Get Page Margins
     *
     * @return array(TOP,RIGHT,BOTTOM,LEFT)
     */
    public function getMargins()
    {
        return $this->_margin;
    }

    /**
     * Get a Page margin
     *
     * @param My_Pdf ::Position $position
     * @return int margin
     */
    public function getMargin($position)
    {
        return $this->_margin[$position];
    }

    /**
     * Set Table Width
     *
     * @param int $val
     */
    public function setWidth($val)
    {
        $this->_autoWidth = false;
        $this->_width = $val;
    }

    /**
     * Get Table Width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Render Table
     *
     * @param \Zend_Pdf_Page $page
     * @param int $posX
     * @param int $posY
     * @param bool $inContentArea
     * @return array
     */
    public function render($page, $posX, $posY, $inContentArea = true)
    {

        if ($this->_headerRow && $this->_rows) {
            //set header in front of rows
            $this->_rows = array_merge($this->_headerRow, $this->_rows);
        } elseif ($this->_headerRow) {
            //no rows in this table, just the header
            $this->_rows = $this->_headerRow;
        }

        if ($inContentArea) {
            $start_y = $posY + $this->getMargin(self::TOP);
            $max_y = $page->getHeight() - $this->getMargin(self::BOTTOM) - $this->getMargin(self::TOP);
        } else {
            $start_y = $posY;
            $max_y = $page->getHeight();
            $posX -= $this->getMargin(self::LEFT);
        }


        $y = $start_y;
        //pre render
        $this->_preRender($page, $posX, $posY, $inContentArea);
        foreach ($this->_rows as $row) {
            //check current position (height)
            $test = ($y + $row->getHeight());

            if ($test > $max_y || $row->hasPageBreak()) {
                //page-break
                $nPage = new \Zend_Pdf_Page($page);

                //copy previous page-settings
                $nPage->setFont($page->getFont(), $page->getFontSize());

                $page = $nPage;
                $this->_pages[] = $page;
                $y = $this->getMargin(self::TOP);

                if ($this->_headerRow && $this->_repeatHeader) {
                    $header = $this->_rows[0];//pre-rendered header row (is first row)
                    $header->render($page, $posX, $y);
                    $y += $header->getHeight() + $header->getBorderLineWidth(self::BOTTOM);
                }
            }

            $row->render($page, $posX, $y);
            $y += $row->getHeight() + $row->getBorderLineWidth(self::BOTTOM);
        }

        return $this->_pages;
    }

    /**
     * Add Header Row
     *
     * @param Row $row
     */
    public function setHeader(Table\Row $row)
    {
        if (!$this->_autoWidth)
            $row->setWidth($this->_width);

        $this->_headerRow[] = $row;
    }

    /**
     * Add Row
     *
     * @param Row $row
     */
    public function addRow(Row $row)
    {
        //add default row properties if non are set (font/color/size,...)
        //set width
        if (!$this->_autoWidth)
            $row->setWidth($this->_width);

        $this->_rows[] = $row;
    }

    /**
     * Replace specific Row in Table
     *
     * @param Row $row
     * @param int $index
     */
    public function replaceRow(Row $row, $index)
    {
        if (!$this->_autoWidth)
            $row->setWidth($this->_width);

        $this->_rows[$index] = $row;
    }

    /**
     * Get all Rows in this Table
     *
     * @return array(My_Pdf_Table_Rows)
     */
    public function getRows()
    {
        return $this->_rows;
    }

    /**
     * @return void
     */
    public function __clone()
    {
        foreach ($this as $key => $val) {
            if (is_object($val) || (is_array($val))) {
                $this->{$key} = unserialize(serialize($val));
            }
        }
    }

    /**
     * Pre-Render Table
     *
     * @param \Zend_Pdf_Page $page
     * @param int $posX
     * @param int $posY
     * @param bool $inContentArea
     */
    private function _preRender(\Zend_Pdf_Page $page, $posX, $posY, $inContentArea = true)
    {
        //get auto-column widths
        $col_widths = array();
        foreach ($this->_rows as $row) {
            //check for colspan's
            $new_dummy_cells = array();
            foreach ($row->getColumns() as $idx => $col) {
                $col_widths[$idx] = $col->getWidth(); //store width ->for dummy cells
                if ($col->getColspan() > 1) {
                    //insert new cell, for each spanning column
                    $new_dummy_cells[$idx] = $col;
                }
            }

            //insert dummy cells
            foreach ($new_dummy_cells as $idx => $col) {
                for ($i = 1; $i < $col->getColspan(); $i++) {
                    //new col
                    $nCol = new Column();
                    $nCol->setText('');
                    if (isset($col_widths[$idx + 1]))
                        $nCol->setWidth($col_widths[$idx + 1]);

                    $row->insertColumn($nCol, $idx + 1);
                }
            }

            //pre-render row
            $row->preRender($page, $posX, $posY, $inContentArea);
            $posY += $row->getHeight() + $row->getBorderLineWidth(self::BOTTOM);
        }

        //set max col width
        $max_col_width = array();
        foreach ($this->_rows as $row) {
            //get columns max width
            $max_col_width = array();
            foreach ($row->getColumns() as $idx => $col) {
                $width = $col->getWidth();
                if (!isset($max_col_width[$idx]) || $width > $max_col_width[$idx])
                    $max_col_width[$idx] = $width;
            }
        }

        //set uniform column width for all rows
        foreach ($this->_rows as $row) {
            foreach ($row->getColumns() as $idx => $col) {
                $col->setWidth($max_col_width[$idx]);
            }
        }
    }

    /**
     * @return Row
     */
    public function createRow()
    {
        $row = new Row();
        //inherit styles from table
        $row->setStyles($this->getStyles());
        return $row;
    }

}