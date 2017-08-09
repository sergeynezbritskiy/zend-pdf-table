<?php

namespace sergeynezbritskiy\ZendPdfTable;

use Zend_Pdf_Color_Rgb;
use Zend_Pdf_Font;
use Zend_Pdf_Resource_Font;
use Zend_Pdf_Style;

/**
 * Class AbstractElement
 *
 * @package sergeynezbritskiy\ZendPdfTable
 */
abstract class AbstractElement
{

    const TOP = 0;
    const RIGHT = 1;
    const BOTTOM = 2;
    const LEFT = 3;
    const CENTER = 4;
    const MIDDLE = 5;

    /**
     * @var Zend_Pdf_Color_Rgb
     */
    protected $fillColor;

    /**
     * @var Zend_Pdf_Color_Rgb
     */
    protected $lineColor;

    /**
     * @var Zend_Pdf_Resource_Font
     */
    protected $fontStyle;

    /**
     * @var int
     */
    protected $fontSize;

    /**
     * @var Zend_Pdf_Style[]
     */
    protected $borderStyles = [];

    /**
     * @var int[]
     */
    protected $paddings = [0, 0, 0, 0];

    /**
     * @var int[]
     */
    protected $margins = [0, 0, 0, 0];

    /**
     * @var int
     */
    protected $textAlign;

    /**
     * Options array example
     * ```php
     * [
     * 'font_size' => 8,
     * 'font_style' => \Zend_Pdf_Font::FONT_HELVETICA,
     * 'fill_color' => [0, 0, 0],
     * 'border_left' => [
     * 'line_color' => [255, 255, 255],
     * 'width' => $lineWidth,
     * ],
     * 'border_right' => [
     * 'line_color' => [255, 255, 255],
     * 'width' => $lineWidth,
     * ],
     * 'border_bottom' => [
     * 'line_color' => [200, 200, 200],
     * 'width' => $lineWidth,
     * ],
     * 'padding_bottom' => 2,
     * 'padding_top' => 2,
     * 'padding_left' => 1,
     * 'padding_right' => 1,
     * 'last_row' => [
     * 'border_bottom' => [
     * 'line_color' => [0, 0, 0],
     * ],
     * ],
     * ],
     * ```
     *
     * @param array $options
     * @return void
     */
    public function setStyles(array $options)
    {
        if (isset($options['fill_color'])) {
            $this->setFillColor($options['fill_color']);
        }
        if (isset($options['line_color'])) {
            $this->setLineColor($options['line_color']);
        }
        if (isset($options['font_style'])) {
            $this->setFontStyle($options['font_style']);
        }
        if (isset($options['font_size'])) {
            $this->setFontSize($options['font_size']);
        }
        if (isset($options['borders'])) {
            $this->setBorderStyles($options['borders']);
        }
        if (isset($options['border_top'])) {
            $this->setBorderStyle(self::TOP, $options['border_top']);
        }
        if (isset($options['border_right'])) {
            $this->setBorderStyle(self::RIGHT, $options['border_right']);
        }
        if (isset($options['border_bottom'])) {
            $this->setBorderStyle(self::BOTTOM, $options['border_bottom']);
        }
        if (isset($options['border_left'])) {
            $this->setBorderStyle(self::LEFT, $options['border_left']);
        }
        if (isset($options['paddings'])) {
            $this->setPaddings($options['paddings']);
        }
        if (isset($options['padding_top'])) {
            $this->setPadding(self::TOP, $options['padding_top']);
        }
        if (isset($options['padding_right'])) {
            $this->setPadding(self::RIGHT, $options['padding_right']);
        }
        if (isset($options['padding_bottom'])) {
            $this->setPadding(self::BOTTOM, $options['padding_bottom']);
        }
        if (isset($options['padding_left'])) {
            $this->setPadding(self::LEFT, $options['padding_left']);
        }
        if (isset($options['margins'])) {
            $this->setMargins($options['margins']);
        }
        if (isset($options['margin_top'])) {
            $this->setMargin(self::TOP, $options['margin_top']);
        }
        if (isset($options['margin_right'])) {
            $this->setMargin(self::RIGHT, $options['margin_right']);
        }
        if (isset($options['margin_bottom'])) {
            $this->setMargin(self::BOTTOM, $options['margin_bottom']);
        }
        if (isset($options['margin_left'])) {
            $this->setMargin(self::LEFT, $options['margin_left']);
        }
        if (isset($options['text_align'])) {
            $this->setTextAlign($options['text_align']);
        }
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        $result = [];
        if ($this->getFillColor() !== null) {
            $result['fill_color'] = $this->getFillColor();
        }
        if ($this->getLineColor() !== null) {
            $result['line_color'] = $this->getLineColor();
        }
        if ($this->getFontStyle() !== null) {
            $result['font_style'] = $this->getFontStyle();
        }
        if ($this->getFontSize() !== null) {
            $result['font_size'] = $this->getFontSize();
        }
        if ($this->getTextAlign() !== null) {
            $result['text_align'] = $this->getTextAlign();
        }
        $result['borders'] = $this->getBorderStyles();
        $result['paddings'] = $this->getPaddings();
        $result['margins'] = $this->getMargins();
        return $result;
    }

    /**
     * @return Zend_Pdf_Color_Rgb
     */
    public function getFillColor()
    {
        return $this->fillColor;
    }

    /**
     * @param array|Zend_Pdf_Color_Rgb $fillColor
     */
    public function setFillColor($fillColor)
    {
        $this->fillColor = $this->ensureColor($fillColor);
    }

    /**
     * @return Zend_Pdf_Color_Rgb
     */
    public function getLineColor()
    {
        return $this->lineColor;
    }

    /**
     * @param array|Zend_Pdf_Color_Rgb $lineColor
     */
    public function setLineColor($lineColor)
    {
        $this->lineColor = $this->ensureColor($lineColor);
    }

    /**
     * @return Zend_Pdf_Resource_Font
     */
    public function getFontStyle()
    {
        return $this->fontStyle;
    }

    /**
     * @param Zend_Pdf_Resource_Font $fontStyle
     */
    public function setFontStyle($fontStyle)
    {
        $this->fontStyle = $this->ensureFontStyle($fontStyle);
    }

    /**
     * @return int
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * @param int $fontSize
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
    }

    /**
     * @return Zend_Pdf_Style[]
     */
    public function getBorderStyles()
    {
        return $this->borderStyles;
    }

    /**
     * @param Zend_Pdf_Style[] $borderStyles
     */
    public function setBorderStyles(array $borderStyles)
    {
        //clear previous values
        $this->borderStyles = [];
        foreach ($borderStyles as $position => $borderStyle) {
            $this->setBorderStyle($position, $borderStyle);
        }
    }

    /**
     * @param int $position
     * @return null|Zend_Pdf_Style
     */
    public function getBorderStyle($position)
    {
        return isset($this->borderStyles[$position]) ? $this->borderStyles[$position] : null;
    }

    /**
     * @param int $position
     * @param Zend_Pdf_Style $style
     */
    public function setBorderStyle($position, $style)
    {
        $this->borderStyles[$position] = $this->ensureLineStyle($style);
    }

    /**
     * Set Horizontal Alignment
     *
     * @param int $align
     */
    public function setTextAlign($align)
    {
        $this->textAlign = $align;
    }

    /**
     * Get Alignment
     *
     * @return int
     */
    public function getTextAlign()
    {
        return $this->textAlign;
    }

    /**
     * @return int[]
     */
    public function getPaddings()
    {
        return $this->paddings;
    }

    /**
     * @param int[] $paddings
     */
    public function setPaddings($paddings)
    {
        $this->paddings = $paddings;
    }

    /**
     * @param int $position
     * @return int
     */
    public function getPadding($position)
    {
        return isset($this->paddings[$position]) ? $this->paddings[$position] : 0;
    }

    /**
     * @param int $position
     * @param int $cellPadding
     */
    public function setPadding($position, $cellPadding)
    {
        $this->paddings[$position] = $cellPadding;
    }

    /**
     * @return int[]
     */
    public function getMargins()
    {
        return $this->margins;
    }

    /**
     * @param int[] $margins
     */
    public function setMargins($margins)
    {
        $this->margins = $margins;
    }

    /**
     * @param int $position
     * @return int
     */
    public function getMargin($position)
    {
        return isset($this->margins[$position]) ? $this->margins[$position] : 0;
    }

    /**
     * @param int $position
     * @param int $margin
     */
    public function setMargin($position, $margin)
    {
        $this->margins[$position] = $margin;
    }

    /**
     * @param $style
     * @return \Zend_Pdf_Style
     */
    private function ensureLineStyle($style)
    {
        if ($style instanceof Zend_Pdf_Style) {
            return $style;
        } else {
            $result = new \Zend_Pdf_Style();
            if (isset($style['line_color'])) {
                $result->setLineColor($this->rgbArrayToColor($style['line_color']));
            }
            if (isset($style['line_width'])) {
                $result->setLineWidth($style['line_width']);
            }
            return $result;
        }
    }

    /**
     * @param string|Zend_Pdf_Resource_Font $fontStyle
     * @return \Zend_Pdf_Resource_Font
     */
    protected function ensureFontStyle($fontStyle)
    {
        return $fontStyle instanceof Zend_Pdf_Resource_Font ? $fontStyle : Zend_Pdf_Font::fontWithName($fontStyle);
    }

    /**
     * @param $color
     * @return \Zend_Pdf_Color_Rgb
     */
    protected function ensureColor($color)
    {
        return $color instanceof Zend_Pdf_Color_Rgb ? $color : $this->rgbArrayToColor($color);
    }

    /**
     * @param array $color
     * @return Zend_Pdf_Color_Rgb
     */
    protected function rgbArrayToColor(array $color)
    {
        return new Zend_Pdf_Color_Rgb(
            $color[0] / 255,
            $color[1] / 255,
            $color[2] / 255
        );
    }

}