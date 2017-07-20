<?php

namespace sergeynezbritskiy\ZendPdfTable\Table;

use sergeynezbritskiy\ZendPdfTable\Table;
use Zend_Exception;
use Zend_Pdf_Color;
use Zend_Pdf_Image;
use Zend_Pdf_Page;
use Zend_Pdf_Resource_Font;
use Zend_Pdf_Style;

class Cell
{

    private $_width;
    private $_height;
    private $_recommendedWidth;
    private $_recommendedHeight;
    private $_text;

    /**
     * @var Zend_Pdf_Resource_Font
     */
    private $_font;
    private $_fontSize = 10;
    private $_hAlign;
    private $_vAlign;
    private $_bgColor;
    private $_color;
    private $_textLineSpacing = 0;

    private $_image;

    /**
     * Cell padding
     *
     * @var array (padding-top,padding-right,padding-bottom,padding-left)
     */
    private $_padding;

    /**
     * Cell borders
     *
     * @var array (sergeynezbritskiy\ZendPdfTable\My_Pdf position=>array('color','width','dashing_pattern'))
     */
    private $_border;

    /**
     * Set Text Line Height
     *
     * @param int $value
     */
    public function setTextLineSpacing($value)
    {
        $this->_textLineSpacing = $value;
    }

    /**
     * Checks if Cell contains a Image Element
     *
     * @return bool
     */
    public function hasImage()
    {
        if ($this->_image) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if Cell contains a Text Element
     *
     * @return bool
     */
    public function hasText()
    {
        if ($this->_text) {
            return true;
        } else {
            return false;
        }
    }

    public function getRecommendedWidth()
    {
        return $this->_recommendedWidth;
    }

    public function getRecommendedHeight()
    {
        return $this->_recommendedHeight;
    }

    /**
     * Set Cell Background Color
     *
     * @param Zend_Pdf_Color $color
     */
    public function setBackgroundColor(Zend_Pdf_Color $color)
    {
        $this->_bgColor = $color;
    }

    /**
     * Set Cell Text Color
     *
     * @param Zend_Pdf_Color $color
     */
    public function setColor(Zend_Pdf_Color $color)
    {
        $this->_color = $color;
    }

    /**
     * Set Cell Padding
     *
     * @param int $position
     * @param int $value padding
     */
    public function setPadding($position, $value)
    {
        $this->_padding[$position] = $value;
    }

    /**
     * Get Cell Padding
     *
     * @param int $position
     * @return int
     */
    public function getPadding($position)
    {
        if (isset($this->_padding[$position])) {
            return $this->_padding[$position];
        } else {
            return false;
        }
    }

    /**
     * Set Horizontal Alignment
     *
     * @param int $align
     */
    public function setAlignment($align)
    {
        $this->_hAlign = $align;
    }

    /**
     * Get Alignment
     *
     * @return int
     */
    public function getAlignment()
    {
        return $this->_hAlign;
    }

    /**
     * Set Vertical Alignment
     *
     * @param int $align
     */
    public function setVAlignment($align)
    {
        $this->_vAlign = $align;
    }

    public function getVAlignment()
    {
        return $this->_vAlign;
    }

    /**
     * Get Cell Border
     *
     * @param int $position
     * @return Zend_Pdf_Style $style
     */
    public function getBorder($position)
    {
        if (isset($this->_border[$position])) {
            return $this->_border[$position];
        } else {
            return false;
        }
    }

    /**
     * Set cell border properties
     *
     * @param int $position
     * @param Zend_Pdf_Style $style
     */
    public function setBorder($position, Zend_Pdf_Style $style)
    {
        $this->_border[$position] = $style;
    }

    /**
     * Set Cell Borders
     *
     * @param array (array(sergeynezbritskiy\ZendPdfTable\My_Pdf position, Zend_Pdf_Styles style))
     */
    public function setBorders($borders)
    {
        $this->_border = $borders;
    }

    /**
     * Remove Cell Border
     *
     * @param int $position
     */
    public function removeBorder($position)
    {
        unset($this->_border[$position]);
    }

    /**
     * Set Font and Size
     *
     * @param Zend_Pdf_Resource_Font $font
     * @param int $fontSize
     */
    public function setFont(Zend_Pdf_Resource_Font $font, $fontSize = 10)
    {
        $this->_font = $font;
        $this->_fontSize = $fontSize;
    }

    /**
     * Get Cell Font
     *
     * @return Zend_Pdf_Resource_Font
     */
    public function getFont()
    {
        return $this->_font;
    }

    /**
     * Set Cell Width
     *
     * @param int $value
     */
    public function setWidth($value)
    {
        $this->_width = $value;
    }

    /**
     * Get Cell Width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->_width;
    }

    /**
     * Set Cell Height
     *
     * @param int $value
     */
    public function setHeight($value)
    {
        $this->_height = $value;
    }

    /**
     * Get Cell Height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->_height;
    }

    /**
     * Add text to cell
     *
     * @param string $text
     * @param int $hAlign Horizontal Alignment
     * @param int $vAlign Vertical Alignment
     */
    public function setText($text, $hAlign = null, $vAlign = null)
    {
        $this->_text['text'] = $text;
        if ($hAlign)
            $this->_hAlign = $hAlign;
        if ($vAlign)
            $this->_vAlign = $vAlign;
    }

    /**
     * Get Cell Text Element
     *
     * @return array(text,width,lines)
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Replace Text String in Text-Element
     *
     * @param mixed $search
     * @param mixed $replace
     * @return int number of replaced strings
     */
    public function replaceText($search, $replace)
    {
        $num_replaced = 0;
        $text = str_replace($search, $replace, $this->_text['text'], $num_replaced);
        $this->_text['text'] = $text;
        return $num_replaced;
    }

    /**
     * Add image to cell
     *
     * @param string $filename full path
     * @param int $hAlign Horizontal Alignment
     * @param int $vAlign Vertical Alignment
     * @param float $scale between (0,1]
     * @throws \Zend_Exception
     */
    public function setImage($filename, $hAlign = null, $vAlign = null, $scale = 1.00)
    {
        $this->_image['filename'] = $filename;

        if ($scale > 1)
            throw new Zend_Exception("Scale must be between (0,1]", 'sergeynezbritskiy\ZendPdfTable\Table\Cell::addImage()');
        $this->_image['scale'] = $scale;

        $this->_hAlign = $hAlign;
        $this->_vAlign = $vAlign;
    }

    /**
     * Pre-render cell to get recommended width and height
     *
     * @param \Zend_Pdf_Page $page
     * @param int $posX
     * @param int $posY
     * @param bool $inContentArea
     * @throws \Zend_Exception
     * @throws \Zend_Pdf_Exception
     */
    public function preRender(\Zend_Pdf_Page $page, $posX, /** @noinspection PhpUnusedParameterInspection */
                              $posY, $inContentArea = true)
    {
        if (!$this->_width) {
            //no width given, get max width of page
            if ($inContentArea) {
                $width = $page->getWidth() - $posX - ($page->getMargin(Table::LEFT) + $page->getMargin(Table::RIGHT));
            } else {
                $width = $page->getWidth() - $posX;
            }
        } else {
            $width = $this->_width;
        }

        //calc max cell width
        $maxWidth = $width - ($this->_padding[Table::LEFT] + $this->_padding[Table::RIGHT]) - (+$this->_getBorderLineWidth(Table::LEFT) + $this->_getBorderLineWidth(Table::RIGHT));

        if ($this->_text) {

            //set font
            $page->setFont($this->_font, $this->_fontSize);

            //get height,width,lines
            $text_props = $this->getTextProperties($page, $this->_text['text'], $maxWidth);

            //reset style
            $page->setStyle($this->getDefaultStyle());

            //set width
            if (!$this->_width) {
                //add padding
                $this->_recommendedWidth = $text_props['text_width'] + ($this->_padding[Table::LEFT] + $this->_padding[Table::RIGHT]) + $this->_getBorderLineWidth(Table::LEFT) + $this->_getBorderLineWidth(Table::RIGHT);
            } else {
                $this->_recommendedWidth = $text_props['max_width'];
            }

            if (!$this->_height) {
                //set height, add padding
                if ($this->_textLineSpacing) {
                    $height = $this->_textLineSpacing * count($text_props['lines']) + $text_props['height'];
                } else {
                    $height = $text_props['height'];
                }
                $this->_recommendedHeight = $height + ($this->_padding[Table::TOP] + $this->_padding[Table::BOTTOM]);
            }

            //store text props;
            $this->_text['width'] = $text_props['text_width'];
            $this->_text['max_width'] = $text_props['max_width'];
            $this->_text['height'] = $text_props['height'];
            $this->_text['lines'] = $text_props['lines'];
        } elseif ($this->_image) {

            $image = Zend_Pdf_Image::imageWithPath($this->_image['filename']);

            if (!$this->_width)
                $this->_recommendedWidth = $this->_image['scale'] * $image->getPixelWidth() + ($this->_padding[Table::LEFT] + $this->_padding[Table::RIGHT]) + $this->_getBorderLineWidth(Table::LEFT) + $this->_getBorderLineWidth(Table::RIGHT);
            if (!$this->_height)
                $this->_recommendedHeight = $this->_image['scale'] * $image->getPixelHeight() + ($this->_padding[Table::TOP] + $this->_padding[Table::BOTTOM]);

            $this->_image['image'] = $image;
            $this->_image['width'] = $this->_image['scale'] * $image->getPixelWidth();
            $this->_image['height'] = $this->_image['scale'] * $image->getPixelHeight();
        } else {
            throw new Zend_Exception("not defined", "preRender()");
        }
    }

    /**
     * Get text properties (width, height, [#lines using $max Width]), and warps lines
     *
     * @param Zend_Pdf_Page $page
     * @param string $text
     * @param int $maxWidth
     * @return array
     */
    public function getTextProperties($page, $text, $maxWidth = null)
    {

        $lines = $this->_textLines($text, $maxWidth);

        return array(
            'text_width' => $lines['text_width'],
            'max_width' => $lines['max_width'],
            'height' => ($this->getFontHeight($page) * count($lines['lines'])),
            'lines' => $lines['lines']
        );
    }

    /**
     * Get Font Height
     *
     * @param \Zend_Pdf_Page $page
     * @return int
     */
    public function getFontHeight($page)
    {
        $line_height = $page->getFont()->getLineHeight();
        $line_gap = $page->getFont()->getLineGap();
        $em = $page->getFont()->getUnitsPerEm();
        $size = $page->getFontSize();
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

    /**
     * Render Cell
     *
     * @param \Zend_Pdf_Page $page
     * @param int $posX
     * @param int $posY
     */
    public function render(\Zend_Pdf_Page $page, $posX, $posY)
    {
        $this->_renderBackground($page, $posX, $posY);
        $this->_renderText($page, $posX, $posY);
        $this->_renderImage($page, $posX, $posY);
        $this->_renderBorder($page, $posX, $posY);
    }

    private function _renderText(\Zend_Pdf_Page $page, $posX, $posY)
    {
        if (!$this->_text) return;

        $page->setFont($this->_font, $this->_fontSize);

        if ($this->_color)
            $page->setFillColor($this->_color);

        if (count($this->_text['lines']) > 1) {

            $line_height = $this->getFontHeight($page) + $this->_textLineSpacing;
            /*
            //write multi-line text
            switch ($this->_vAlign){
                case sergeynezbritskiy\ZendPdfTable\My_Pdf::BOTTOM:
                    $y_inc=$posY-$this->_textLineSpacing;
                    $rev_lines=array_reverse($this->_text['lines']);
                    foreach ($rev_lines as $line){
                        $page->drawText($line,$this->_getTextPosX($posX), $this->_getTextPosY($page,$y_inc));
                        $y_inc-=$line_height;
                    }
                    break;
                default:
                    $y_inc=$posY-$this->_textLineSpacing;
                    foreach ($this->_text['lines'] as $line){
                        $page->drawText($line,$this->_getTextPosX($posX), $this->_getTextPosY($page,$y_inc));
                        $y_inc+=$line_height;
                    }
                    break;
            }
            */

            //@@TODO VERTICAL POSITIONING OF MULTI-LINED TEXT
            $y_inc = $posY - $this->_textLineSpacing; //@@TODO HACK
            $this->_vAlign = Table::TOP; //@@TODO ONLY TOP ALIGNMENT IS VALID AT THIS MOMENT
            foreach ($this->_text['lines'] as $line) {
                $this->drawText($page, $line, $this->_getTextPosX($posX), $this->_getTextPosY($page, $y_inc));
                $y_inc += $line_height;
            }


        } else {
            //write single line of text
            $this->drawText($page, $this->_text['text'], $this->_getTextPosX($posX), $this->_getTextPosY($page, $posY));
        }
        //reset style
        $page->setStyle($this->getDefaultStyle());
    }

    /**
     * Draw Text
     *
     * @param \Zend_Pdf_Page $page
     * @param string $text
     * @param int $x1
     * @param int $y1
     * @param string $charEncoding
     * @return \Zend_Pdf_Canvas_Interface
     * @throws \Zend_Pdf_Exception
     * @internal param bool $inContentArea
     */
    public function drawText(\Zend_Pdf_Page $page, $text, $x1, $y1, $charEncoding = "")
    {
        //move origin
        $y1 = $page->getHeight() - $y1 - $page->getMargin(Table::TOP);
        $x1 = $x1 + $page->getMargin(Table::LEFT);
        return $page->drawText($text, $x1, $y1, $charEncoding);
    }

    private function _renderImage(\Zend_Pdf_Page $page, $posX, $posY)
    {
        if (!$this->_image) return;
        $this->drawImage($page, $this->_image['image'], $this->_getImagePosX($posX), $this->_getImagePosY($posY), $this->_image['width'], $this->_image['height']);

    }

    public function drawImage(\Zend_Pdf_Page $page, \Zend_Pdf_Resource_Image $image, $x1, $y1, $width, $height)
    {
        $y1 = $page->getHeight() - $y1 - $page->getMargin(Table::TOP) - $height;
        $x1 = $x1 + $page->getMargin(Table::LEFT);

        $y2 = $y1 + $height;
        $x2 = $x1 + $width;
        $page->drawImage($image, $x1, $y1, $x2, $y2);
    }

    private function _renderBorder(\Zend_Pdf_Page $page, $posX, $posY)
    {
        if (!$this->_border) return;

        foreach ($this->_border as $key => $style) {
            $page->setStyle($style);
            switch ($key) {
                case Table::TOP:
                    $this->drawLine($page,
                        $posX, $posY - $this->_getBorderLineWidth(Table::TOP) / 2,
                        $posX + $this->_width, $posY - $this->_getBorderLineWidth(Table::TOP) / 2
                    );
                    break;
                case Table::BOTTOM:
                    $this->drawLine($page,
                        $posX, $posY + $this->_height + $this->_getBorderLineWidth(Table::BOTTOM) / 2,
                        $posX + $this->_width, $posY + $this->_height + $this->_getBorderLineWidth(Table::BOTTOM) / 2
                    );
                    break;
                case Table::RIGHT:
                    //@@TODO INCLUDE BORDER LINE WIDTH??
                    $this->drawLine($page,
                        $posX + $this->_width, $posY,
                        $posX + $this->_width, $posY + $this->_height
                    );
                    break;
                case Table::LEFT:
                    //@@TODO INCLUDE BORDER LINE WIDTH??
                    $this->drawLine($page,
                        $posX, $posY,
                        $posX, $posY + $this->_height
                    );
                    break;
            }
            //reset page style
            $page->setStyle($this->getDefaultStyle());
        }
    }

    /**
     * Draw Line
     *
     * @param \Zend_Pdf_Page $page
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @return \Zend_Pdf_Canvas_Interface
     */
    public function drawLine(\Zend_Pdf_Page $page, $x1, $y1, $x2, $y2)
    {
        $y1 = $page->getHeight() - $y1 - $page->getMargin(Table::TOP);
        $y2 = $page->getHeight() - $y2 - $page->getMargin(Table::TOP);
        $x1 = $x1 + $page->getMargin(Table::LEFT);
        $x2 = $x2 + $page->getMargin(Table::LEFT);

        return $page->drawLine($x1, $y1, $x2, $y2);
    }

    private function _renderBackground(\Zend_Pdf_Page $page, $posX, $posY)
    {
        if (!$this->_bgColor) return;
        $page->setFillColor($this->_bgColor);
        $this->drawRectangle($page, $posX,
            $posY,
            $posX + $this->_width,
            $posY + $this->_height,
            Zend_Pdf_Page::SHAPE_DRAW_FILL);
        //reset style
        $page->setStyle($this->getDefaultStyle());
    }

    /**
     * Draw Rectangle
     *
     * @param \Zend_Pdf_Page $page
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param int $y2
     * @param string $fillType
     * @return \Zend_Pdf_Canvas_Interface
     */
    public function drawRectangle(\Zend_Pdf_Page $page, $x1, $y1, $x2, $y2, $fillType = null)
    {
        //move origin
        $y1 = $page->getHeight() - $y1 - $page->getMargin(Table::TOP);
        $y2 = $page->getHeight() - $y2 - $page->getMargin(Table::TOP);
        $x1 = $x1 + $page->getMargin(Table::LEFT);
        $x2 = $x2 + $page->getMargin(Table::LEFT);

        return $page->drawRectangle($x1, $y1, $x2, $y2, $fillType);
    }

    /**
     * Positions text horizontally (x-axis) adding alignment
     * Default alignment: LEFT
     *
     * @param int $posX
     * @return int
     */
    private function _getTextPosX($posX)
    {
        switch ($this->_hAlign) {
            case Table::RIGHT:
                $x = $posX + $this->_width - $this->_text['width'] - $this->_padding[Table::RIGHT] - $this->_getBorderLineWidth(Table::RIGHT) / 2;
                break;
            case Table::CENTER:
                $x = $posX + $this->_width / 2 - $this->_text['width'] / 2;
                break;
            default: //LEFT
                $x = $posX + $this->_padding[Table::LEFT] + $this->_getBorderLineWidth(Table::LEFT) / 2;
                break;
        }
        return $x;
    }

    /**
     * Positions text vertically (y-axis) adding vertical alignment
     * Default alignment: TOP
     *
     * @param \Zend_Pdf_Page $page
     * @param int $posY
     * @return int
     */
    private function _getTextPosY(\Zend_Pdf_Page $page, $posY)
    {
        $line_height = $this->getFontHeight($page) + $this->_textLineSpacing;

        switch ($this->_vAlign) {
            case Table::BOTTOM:
                $y = $posY + $this->_height - $this->_padding[Table::BOTTOM];
                break;
            case Table::MIDDLE:
                $y = $posY + $this->_height / 2 + $line_height / 2;
                break;
            default: //TOP
                $y = $posY + $line_height + $this->_padding[Table::TOP];
                break;
        }
        return $y;
    }

    private function _getImagePosX($posX)
    {
        switch ($this->_hAlign) {
            case Table::RIGHT:
                $x = $posX + $this->_width - $this->_image['width'] - $this->_padding[Table::RIGHT];
                break;
            case Table::CENTER:
                $x = $posX + $this->_width / 2 - $this->_image['width'] / 2;
                break;
            default: //LEFT
                $x = $posX + $this->_padding[Table::LEFT];
                break;
        }
        return $x;
    }

    private function _getImagePosY($posY)
    {
        switch ($this->_vAlign) {
            case Table::BOTTOM:
                $y = $posY + $this->_height - $this->_image['height'] - $this->_padding[Table::BOTTOM];
                break;
            case Table::MIDDLE:
                $y = $posY + ($this->_height - $this->_image['height']) / 2;
                break;
            default: //TOP
                $y = $posY + $this->_padding[Table::TOP];
                break;
        }
        return $y;
    }

    private function _getBorderLineWidth($position)
    {
        if (isset($this->_border[$position])) {
            $style = $this->_border[$position];
            $width = $style->getLineWidth();
        } else {
            $width = 0;
        }
        return $width;
    }

    private function getDefaultStyle()
    {
        $style = new Zend_Pdf_Style();
        $style->setLineColor(new \Zend_Pdf_Color_Html("#000000"));
        $style->setFillColor(new \Zend_Pdf_Color_Html("#000000"));
        $style->setLineWidth(0.5);

        $font = \Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_COURIER);
        $style->setFont($font, 10);

        /** @noinspection PhpParamsInspection */
        $style->setLineDashingPattern(Zend_Pdf_Page::LINE_DASHING_SOLID);
        return $style;
    }

}

?>
