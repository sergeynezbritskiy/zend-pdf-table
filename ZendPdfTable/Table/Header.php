<?php

namespace sergeynezbritskiy\ZendPdfTable\Table;

use sergeynezbritskiy\ZendPdfTable\Table\Column;
use sergeynezbritskiy\ZendPdfTable\Pdf;
use sergeynezbritskiy\ZendPdfTable\Page;
use sergeynezbritskiy\ZendPdfTable\Table;
use Zend_Pdf_Font;
use Zend_Pdf_Style;

class Header extends \sergeynezbritskiy\ZendPdfTable\Table\Row
{

    private $_align;
    private $_vAlign;

    public function setAlignment($align)
    {
        $this->_align = $align;
    }

    public function setVAlignment($align)
    {
        $this->_vAlign = $align;
    }

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
        $this->_align = Table::CENTER;

        //set default borders
        $style = new Zend_Pdf_Style();
        $style->setLineWidth(2);
        $this->setBorder(Table::BOTTOM, $style);
        $this->setCellPaddings(array(5, 5, 5, 5));

        //set default font
        $this->_font = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_HELVETICA_BOLD);
        $this->_fontSize = 12;
    }

    public function preRender(Page $page, $posX, $posY, $inContentArea = true)
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
                $col->setAlignment($this->_align);
        }

        parent::preRender($page, $posX, $posY);
    }
}

?>