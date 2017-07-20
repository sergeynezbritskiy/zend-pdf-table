<?php

namespace sergeynezbritskiy\ZendPdfTable\Table;

use sergeynezbritskiy\ZendPdfTable\Table;
use Zend_Pdf_Font;
use Zend_Pdf_Style;

/**
 * Class Header
 *
 * @package sergeynezbritskiy\ZendPdfTable\Table
 */
class Header extends \sergeynezbritskiy\ZendPdfTable\Table\Row
{

    private $_hAlign;
    private $_vAlign;

    /**
     * @param $align
     */
    public function setAlignment($align)
    {
        $this->_hAlign = $align;
    }

    /**
     * @param $align
     */
    public function setVAlignment($align)
    {
        $this->_vAlign = $align;
    }

    /**
     * Header constructor.
     *
     * @param array $labels
     */
    public function __construct($labels = array())
    {

        parent::__construct();
        $cols = null;
        foreach ($labels as $label) {
            $col = new Column();
            $col->setText($label);
            $cols[] = $col;
        }
        if ($cols)
            $this->setColumns($cols);

        //set default alignment
        $this->_hAlign = Table::CENTER;

        //set default borders
        $style = new Zend_Pdf_Style();
        $style->setLineWidth(2);
        $this->setBorder(Table::BOTTOM, $style);
        $this->setCellPaddings(array(5, 5, 5, 5));

        //set default font
        $this->_font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $this->_fontSize = 12;
    }

    /**
     * @param \Zend_Pdf_Page $page
     * @param int $posX
     * @param int $posY
     * @param bool $inContentArea
     */
    public function preRender(\Zend_Pdf_Page $page, $posX, $posY, $inContentArea = true)
    {

        foreach ($this->_cols AS $col) {
            //set default font
            if (!$col->getFont())
                $col->setFont($this->_font, $this->_fontSize);
            //set default borders if not set
            foreach ($this->_border as $pos => $style) {
                if (!$col->getBorder($pos))
                    $col->setBorder($pos, $style);
            }

            if (!$col->getAlignment())
                $col->setAlignment($this->_hAlign);
        }

        parent::preRender($page, $posX, $posY);
    }
}