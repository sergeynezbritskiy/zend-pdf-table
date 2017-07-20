<?php

namespace sergeynezbritskiy\ZendPdfTable;

/**
 * Class Page
 *
 * @package sergeynezbritskiy\ZendPdfTable
 * @deprecated
 */
class Page extends \Zend_Pdf_Page
{

    /*
     * If page contains pagebreaks, pages are stored here
     */
    private $_pages = array();

    /**
     * Get all pages for this page (page overflows)
     *
     * @return array pages
     */
    public function getPages()
    {
        if (count($this->_pages) > 0) {
            return array_merge(array($this), $this->_pages);
        } else {
            return false;
        }
    }

    private $_margin;

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
     * Get Page Margins
     *
     * @return array(TOP,RIGHT,BOTTOM,LEFT)
     */
    public function getMargins()
    {
        return $this->_margin;
    }

    /**
     * Get text properties (width, height, [#lines using $max Width]), and warps lines
     *
     * @param string $text
     * @param int $maxWidth
     * @return array
     */
    public function getTextProperties($text, $maxWidth = null)
    {

        $lines = $this->_textLines($text, $maxWidth);

        return array(
            'text_width' => $lines['text_width'],
            'max_width' => $lines['max_width'],
            'height' => ($this->getFontHeight() * count($lines['lines'])),
            'lines' => $lines['lines']
        );
    }

    /**
     * Draw Line
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param bool $inContentArea
     * @return \Zend_Pdf_Canvas_Interface
     */
    public function drawLine($x1, $y1, $x2, $y2, $inContentArea = true)
    {
        //move origin
        if ($inContentArea) {
            $y1 = $this->getHeight() - $y1 - $this->getMargin(Table::TOP);
            $y2 = $this->getHeight() - $y2 - $this->getMargin(Table::TOP);
            $x1 = $x1 + $this->getMargin(Table::LEFT);
            $x2 = $x2 + $this->getMargin(Table::LEFT);
        }

        return parent::drawLine($x1, $y1, $x2, $y2);
    }

    /**
     * Draw Text
     *
     * @param string $text
     * @param int $x1
     * @param int $y1
     * @param string $charEncoding
     * @param bool $inContentArea
     * @return \Zend_Pdf_Canvas_Interface
     */
    public function drawText($text, $x1, $y1, $charEncoding = "", $inContentArea = true)
    {
        //move origin
        if ($inContentArea) {
            $y1 = $this->getHeight() - $y1 - $this->getMargin(Table::TOP);
            $x1 = $x1 + $this->getMargin(Table::LEFT);
        }

        return parent::drawText($text, $x1, $y1, $charEncoding);
    }


    /**
     * Draw Rectangle
     *
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param string $filltype
     * @param bool $inContentArea
     * @return \Zend_Pdf_Canvas_Interface
     */
    public function drawRectangle($x1, $y1, $x2, $y2, $filltype = null, $inContentArea = true)
    {
        //move origin
        if ($inContentArea) {
            $y1 = $this->getHeight() - $y1 - $this->getMargin(Table::TOP);
            $y2 = $this->getHeight() - $y2 - $this->getMargin(Table::TOP);
            $x1 = $x1 + $this->getMargin(Table::LEFT);
            $x2 = $x2 + $this->getMargin(Table::LEFT);
        }

        return parent::drawRectangle($x1, $y1, $x2, $y2, $filltype);
    }

    public function drawImage(\Zend_Pdf_Resource_Image $image, $x1, $y1, $width, $height, $inContentArea = true)
    {
        $y1 = $this->getHeight() - $y1 - $this->getMargin(Table::TOP) - $height;
        $x1 = $x1 + $this->getMargin(Table::LEFT);

        $y2 = $y1 + $height;
        $x2 = $x1 + $width;
        parent::drawImage($image, $x1, $y1, $x2, $y2);
    }

    /**
     * Get Font Height
     *
     * @return int
     */
    public function getFontHeight()
    {
        $line_height = $this->getFont()->getLineHeight();
        $line_gap = $this->getFont()->getLineGap();
        $em = $this->getFont()->getUnitsPerEm();
        $size = $this->getFontSize();
        return ($line_height - $line_gap) / $em * $size;
    }

    /**
     * Returns the with of the text
     *
     * @param string $text
     * @return int $width
     */
    private function _getTextWidth($text)
    {

        $glyphs = array();
        $em = $this->_font->getUnitsPerEm();

        //get glyph for each character
        foreach (range(0, strlen($text) - 1) as $i) {
            $glyphs [] = @ord($text [$i]);
        }

        $width = array_sum($this->_font->widthsForGlyphs($glyphs)) / $em * $this->_fontSize;

        return $width;
    }

    /**
     * Wrap text according to max width
     *
     * @param string $text
     * @param int $maxWidth
     * @return array lines
     */
    private function _wrapText($text, $maxWidth)
    {
        $x_inc = 0;
        $curr_line = '';
        $words = explode(' ', trim($text));
        $space_width = $this->_getTextWidth(' ');
        foreach ($words as $word) {
            //no new line found
            $width = $this->_getTextWidth($word);

            if (isset ($maxWidth) && ($x_inc + $width) <= $maxWidth) {
                //add word to current line
                $curr_line .= ' ' . $word;
                $x_inc += $width + $space_width;
            } else {
                //store current line
                if (strlen(trim($curr_line, "\n")) > 0)
                    $lines [] = trim($curr_line);

                //new line
                $x_inc = 0; //reset position
                //add word
                $curr_line = $word;
                $x_inc += $width + $space_width;
            }
        }

        $lines = [];
        //last line
        if (strlen(trim($curr_line, "\n")) > 0) {
            $lines [] = trim($curr_line);
        }
        return $lines;
    }

    /**
     * Enter description here...
     *
     * @param string $text
     * @param int $maxWidth (optional, if not set (auto width) the max width is set by reference)
     * @return array line(text);
     */
    private function _textLines($text, $maxWidth = null)
    {
        $trimmed_lines = array();

        $lines = explode("\n", $text);
        $max_line_width = 0;
        foreach ($lines as $line) {
            if (strlen($line) <= 0) continue;
            $line_width = $this->_getTextWidth($line);
            if ($maxWidth > 0 && $line_width > $maxWidth) {
                $new_lines = $this->_wrapText($line, $maxWidth);
                $trimmed_lines += $new_lines;

                foreach ($new_lines as $nline) {
                    $line_width = $this->_getTextWidth($nline);
                    if ($line_width > $max_line_width)
                        $max_line_width = $line_width;
                }
            } else {
                $trimmed_lines[] = $line;
            }
            if ($line_width > $max_line_width)
                $max_line_width = $line_width;
        }

        //set actual width of line
        if (is_null($maxWidth))
            $maxWidth = $max_line_width;

        $textWidth = $max_line_width;


        return array('lines' => $trimmed_lines, 'text_width' => $textWidth, 'max_width' => $maxWidth);
    }

}

?>
