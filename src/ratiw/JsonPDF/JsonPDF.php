<?php namespace ratiw\JsonPDF;

//Scale factor
define('_MPDFK', (72/25.4));

class JsonPDF extends Fpdf
{
    protected $settings     = null;

    protected $header      = null;

    protected $footer      = null;

    protected $tables       = null;

    protected $varKeys      = null;
    protected $varValues    = null;
    protected $varSets      = null;

    const RENDER_FORM_ONLY  = 'form';
    const RENDER_DATA_ONLY  = 'data';
    const RENDER_ALL        = 'both';

    protected $renderWhat   = self::RENDER_ALL;

    public function make($document, $data = null, $renderWhat = self::RENDER_ALL)
    {
        $document = json_decode($document);
        $data = json_decode($data);
        $this->renderWhat = $renderWhat;

        isset($document->settings) and $this->init($document->settings);
        isset($document->fonts)    and $this->addFonts($document->fonts);

        isset($document->tables) and $this->setTables($document->tables);
        isset($data) and $this->setVars($data);

        isset($document->header) and $this->setHeader($document->header);
        isset($document->footer) and $this->setFooter($document->footer);

        $this->AddPage();
        isset($document->body) and $this->renderSection($document->body);

        return $this;
    }

    public function init($settings)
    {
        $this->initPageSettings($settings);

        $this->initDisplayMode($settings);

        $this->initFontSettings($settings);

        // default to UTF8 encoding
        isset($settings->utf8) || $settings->utf8 = true;

        // meta-data
        isset($settings->author)   and $this->SetAuthor($settings->author, $settings->utf8);
        isset($settings->title)    and $this->SetTitle($settings->title, $settings->utf8);
        isset($settings->subject)  and $this->SetSubject($settings->subject, $settings->utf8);
        isset($settings->keywords) and $this->SetKeywords($settings->keywords, $settings->utf8);
        isset($settings->creator)  or  $settings->creator = "JsonPDF";
        $this->SetCreator($settings->creator, $settings->utf8);

        // update settings
        $this->settings = $settings;
    }

    protected function initPageSettings($settings)
    {
        // Number of Pages Alias
        isset($settings->{'alias-nb-pages'}) || $settings->{'alias-nb-pages'} = '{nb}';
        $this->AliasNbPages($settings->{'alias-nb-pages'});

        // margins
        isset($settgins->{'left-margin'})  and $this->SetLeftMargin($settings->{'left-margin'});
        isset($settgins->{'top-margin'})   and $this->SetTopMargin($settings->{'top-margin'});
        isset($settgins->{'right-margin'}) and $this->SetRightMargin($settings->{'right-margin'});
        // bottomMargin is not used in FPDF, see SetMargins() documentation.

        // AutoPageBreak
        if (isset($settings->{'auto-pagebreak'}))
        {
            if (isset($settings->{'auto-pagebreak-margin'}))
            {
                $this->SetAutoPageBreak($settings->{'auto-pagebreak'}, $settings->{'auto-pagebreak-margin'});
            }
            else
            {
                $this->SetAutoPageBreak($settings->{'auto-pagebreak'});
            }
        }

        // page compression
        isset($settings->compression) and $this->SetCompression($settings->compression);
    }

    protected function initDisplayMode($settings)
    {
        if (isset($settings->zoom))
        {
            if (isset($settings->layout))
            {
                $this->SetDisplayMode($settings->zoom, $settings->layout);
            }
            else
            {
                $this->SetDisplayMode($settings->zoom);
            }
        }
    }

    protected function initFontSettings($settings)
    {
        // default font
        if (isset($settings->{'default-font'}))
        {
            $font   = $settings->{'default-font'};
            $fname  = $font->name;
            $fstyle = isset($font->style) ? $font->style : '';
            $ffile  = isset($font->file) ? $font->file : false;
            $fsize  = isset($font->size) ? $font->size : 10;

            if ($ffile)
            {
                $this->AddFont($fname, $fstyle, $ffile);
            }
            else
            {
                $this->AddFont($fname, $fstyle);
            }

            $this->SetFont($fname, $fstyle, $fsize);
        }
    }

    public function addFonts($fonts)
    {
        if (is_null($fonts)) return;

        foreach ($fonts as $f)
        {
            $this->AddFont($f[0], $f[1], $f[2]);
        }
    }

    public function setHeader($objects)
    {
        $this->header = $objects;
    }

    public function setFooter($objects)
    {
        $this->footer = $objects;
    }

    public function setTables($objects)
    {
        $this->tables = array();
        foreach($objects as $key => $obj)
        {
            $this->tables[$key] = $obj;
        }
    }

    public function setVars($vars)
    {
        unset($this->varKeys);   $this->varKeys = array();
        unset($this->varValues); $this->varValues = array();

        $i = 0;
        foreach($vars as $key => $value)
        {
            $this->varKeys[$i] = '{'.$key.'}';
            if (is_array($value))
            {
                $this->varSets[$key] = $value;
            }
            else
            {
                $this->varValues[$i] = $value;
                $i++;
            }
        }
    }

    public function renderSection($objects)
    {
        if (is_null($objects)) return;

        foreach ($objects as $obj)
        {
            $method = 'render'.$this->snakeToCamel($obj->type, '-');
            if (method_exists(__CLASS__, $method))
            {
                call_user_func(array($this, $method), $obj);
            }
            else
            {
                $this->Error(__CLASS__.': method '.$method.' does not exists');
            }
        }
    }

    public function bindVars($text)
    {
        $text = str_replace(array('{page}'), array($this->PageNo()), $text);

        if (is_null($this->varKeys)) return $text;

        return str_replace($this->varKeys, $this->varValues, $text);
    }

    protected function setObjectFont($obj)
    {
        $family = isset($obj->font) ? $obj->font : $this->FontFamily;
        $style = isset($obj->{'font-style'}) ? $obj->{'font-style'} : $this->FontStyle.($this->underline ? 'U' : '');
        $this->SetFont($family, $style);
        isset($obj->{'font-size'}) and $this->SetFontSize($obj->{'font-size'});
    }

    protected function setObjectProperties($obj)
    {
        // colors
        isset($obj->{'text-color'}) and $this->callSetColorMethod('SetTextColor', $obj->{'text-color'});

        isset($obj->{'draw-color'}) and $this->callSetColorMethod('SetDrawColor', $obj->{'draw-color'});

        isset($obj->{'fill-color'}) and $this->callSetColorMethod('SetFillColor', $obj->{'fill-color'});

        // line width
        isset($obj->{'line-width'}) and $this->SetLineWidth($obj->{'line-width'});

        // set font
        $this->setObjectFont($obj);

        // set coordinate
        isset($obj->y) and $this->SetY($obj->y);
        isset($obj->x) and $this->SetX($obj->x);
    }

    public function callSetColorMethod($method, $color)
    {
        $rgb = explode(',', $color);
        for ($i=0; $i<count($rgb); $i++)
        {
            $rgb[$i] = intval($rgb[$i]);
        }

        if (count($rgb) < 3)
        {
            call_user_func(array($this, $method), $rgb[0]);
        }
        else
        {
            call_user_func(array($this, $method), $rgb[0], $rgb[1], $rgb[2]);
        }
    }

    public function isRenderable($obj)
    {
        $renderAs = isset($obj->{'render-as'}) ? strtolower($obj->{'render-as'}) : self::RENDER_ALL;
        if ($this->renderWhat == self::RENDER_ALL or $renderAs == $this->renderWhat)
        {
            return true;
        }

        return false;
    }

    public function isNotRenderable($obj)
    {
        return (! $this->isRenderable($obj));
    }

    public function renderText($obj)
    {
        if ($this->isNotRenderable($obj)) return;

        $this->setObjectProperties($obj);

        // alignment and ln-height
        $align = isset($obj->align) ? $obj->align : 'L';
        $ln = isset($obj->ln) ? $obj->ln : 0;

        // border
        $border = isset($obj->border) ? $obj->border : 0;

        // bind data variables
        $text = isset($obj->text) ? $this->bindVars($obj->text) : '';
        // convert text from utf8
        $text = $this->encodeText($text);

        // width & height
        $width  = isset($obj->width) ? $obj->width : 0;
        $height = isset($obj->height) ? $obj->height : 0;

        $multiline = isset($obj->multiline) ? $obj->multiline : false;

        if ($multiline)
        {
            $this->MultiCell(
                $width,
                $height,
                $text,
                $border,
                $align,
                isset($obj->{'fill-color'})
            );
        }
        else
        {
            $this->Cell(
                $width,
                $height,
                $text,
                $border,
                $ln,
                $align,
                isset($obj->{'fill-color'})
            );
        }
    }

    public function renderLine($obj)
    {
        if ($this->isNotRenderable($obj)) return;

        $this->setObjectProperties($obj);

        isset($obj->x2) or $obj->x2 = ($this->w - $this->rMargin);
        isset($obj->y2) or $obj->y2 = $obj->y1;

        $this->Line($obj->x1, $obj->y1, $obj->x2, $obj->y2);
    }

    public function renderRect($obj)
    {
        if ($this->isNotRenderable($obj)) return;

        $this->setObjectProperties($obj);

        $style = isset($obj->style) ? strtoupper($obj->style) : 'D';

        $radius = isset($obj->radius) ? $obj->radius : false;

        if ($radius) {
            $this->RoundedRect($obj->x, $obj->y, $obj->width, $obj->height, $radius, $style);
        }
        else {
            $this->Rect($obj->x, $obj->y, $obj->width, $obj->height, $style);
        }
    }

    public function renderImage($obj)
    {
        if ($this->isNotRenderable($obj)) return;

        $x = isset($obj->x) ? $obj->x : null;
        $y = isset($obj->y) ? $obj->y : null;
        $w = isset($obj->width)  ? $obj->width : 0;
        $h = isset($obj->height) ? $obj->height : 0;

        $this->Image($obj->url, $x, $y, $w, $h);
    }

    public function renderTable($obj)
    {
        $this->initTable($obj);

        $this->_renderTableHeader($obj);
        $this->_renderTableData($obj);
    }

    private function initTable($obj)
    {
        is_null($this->tables) and $this->Error('No "tables" section.');
        isset($obj->table) or $this->Error('Table type requires "table" parameter.');
        array_key_exists($obj->table, $this->tables) or $this->Error('['.$obj->table.'] table definition not found!');

        $table = $this->tables[$obj->table];
        $style = isset($table->style) ? $table->style : $table->style = new StdClass;

        $this->setObjectProperties($obj);

        isset($table->style->{'border-color'}) and $this->callSetColorMethod('SetDrawColor', $table->style->{'border-color'});
    }

    public function renderTableHeader($obj)
    {
        if ($this->isNotRenderable($obj)) return;

        $this->initTable($obj);
        $this->_renderTableHeader($obj);
    }

    public function renderTableBody($obj)
    {
        $this->initTable($obj);
        $this->_renderTableData($obj);
    }

    protected function _renderTableHeader($obj)
    {
        $table = $this->tables[$obj->table];
        $columns = $table->columns;

        // override default table style, if provided
        $style = isset($table->style) ? $table->style : new StdClass;
        if (isset($obj->style)) {
            $style = (object) $this->deep_merge((array) $style, (array) $obj->style);
        }
        $rowStyle = $style->{'title-row'};

        $lineHeight = isset($rowStyle->height) ? $rowStyle->height : $this->lasth;

        isset($rowStyle) and $this->setTableStyle($rowStyle);

        $border  = isset($style->border) ? $style->border : 1;
        $filled  = isset($rowStyle->{'fill-color'}) ? $rowStyle->{'fill-color'} : false;
        $drawText = isset($rowStyle->{'draw-text'}) ? $rowStyle->{'draw-text'} : true;

        for ($i=0; $i<count($columns); $i++)
        {
            $col = $columns[$i];
            $colWidth = isset($col->width) ? $col->width : 20;
            $text = isset($col->title) && $drawText ? $col->title : ucfirst($col->name);
            $this->Cell(
                $colWidth,
                $lineHeight,
                $this->encodeText($text),
                $border,
                0,
                isset($col->{'title-align'}) ? $col->{'title-align'} : 'C',
                $filled
            );
        }
        $this->Ln();
    }

    public function encodeText($text)
    {
        // convert text from utf8
        if ($this->settings->utf8)
        {
            $text = ($text == '') ? '' : iconv('UTF-8', 'ISO-8859-11', $text);
        }

        return $text;
    }

    public function setTableStyle($style)
    {
        $this->setObjectProperties($style);
    }

    protected function _renderTableData($obj)   //$columns, $data
    {
        $table = $this->tables[$obj->table];

        if ( ! isset($this->varSets[$table->data])) return;

        $data = $this->varSets[$table->data];

        $columns = $table->columns;

        // override default table style, if provided
        $style = isset($table->style) ? $table->style : new StdClass;
        if (isset($obj->style)) {
            // $style = (object) array_merge((array) $style, (array) $obj->style);
            $style = (object) $this->deep_merge((array) $style, (array) $obj->style);
        }

        $rowStyle = $style->{'data-row'};

        $lineHeight = isset($rowStyle->height) ? $rowStyle->height : $this->lasth;

        isset($rowStyle) and $this->setTableStyle($rowStyle);

        // set render options
        $border  = isset($style->border) ? $style->border : 'LR';
        $filled  = isset($rowStyle->{'fill-color'}) ? $rowStyle->{'fill-color'} : false;
        $striped = $filled && isset($rowStyle->striped) ? $rowStyle->striped : false;
        $drawText = true;

        if ($this->renderWhat == self::RENDER_FORM_ONLY)
        {
            $drawText = false;
        }
        elseif ($this->renderWhat == self::RENDER_DATA_ONLY)
        {
            $border  = false;
            $filled  = false;
            $striped = false;
            $drawText = true;
        }

        $totalWidth = 0;

        $numRows = isset($table->{'rows-per-page'}) ? $table->{'rows-per-page'} : 0;
        $numRows = ($numRows > 0) ? $numRows * (ceil(count($data) / $numRows)) : count($data);

        for ($r=0; $r<$numRows; $r++)
        {
            $row = ($r < count($data)) ? $data[$r] : array();
            for ($c=0; $c<count($columns); $c++)
            {
                $col = $columns[$c];
                $colWidth = isset($col->width) ? $col->width : 20;
                $text = ($drawText && $r < count($data)) ? $row->{$col->name} : "";
                $this->Cell(
                    $colWidth,
                    $lineHeight,
                    $this->encodeText($text),
                    $border,
                    0,
                    isset($col->{'data-align'}) ? $col->{'data-align'} : 'L',
                    $filled
                );
                // calculate total width from the first row of data
                if ($r == 0)
                {
                    $totalWidth += $colWidth;
                }
            }
            $this->Ln();
            $filled = $striped ? !$filled : $filled;
        }
        // $this->Cell($totalWidth, 0, '', 'T');
    }

    public function render($name = '', $dest = '')
    {
        $this->Output($name, $dest);
        exit;
    }

    function Header()
    {
        $this->resetDrawing();
        $this->renderSection($this->header);
        isset($this->settings->{'header-height'}) and $this->SetY($this->settings->{'header-height'});
   }

    function Footer()
    {
        $this->resetDrawing();
        isset($this->settings->{'auto-pagebreak-margin'}) and $this->SetY(-$this->settings->{'auto-pagebreak-margin'});
        $this->renderSection($this->footer);
    }

    // from mPDF
    function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' or $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.3F %.3F m',($x+$r)*_MPDFK,($hp-$y)*_MPDFK ));
        $xc = $x+$w-$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.3F %.3F l', $xc*_MPDFK,($hp-$y)*_MPDFK ));

        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
        $xc = $x+$w-$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.3F %.3F l',($x+$w)*_MPDFK,($hp-$yc)*_MPDFK));
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x+$r ;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.3F %.3F l',$xc*_MPDFK,($hp-($y+$h))*_MPDFK));
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.3F %.3F l',($x)*_MPDFK,($hp-$yc)*_MPDFK ));
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    // from mPDF
    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.3F %.3F %.3F %.3F %.3F %.3F c ', $x1*_MPDFK, ($h-$y1)*_MPDFK,
                            $x2*_MPDFK, ($h-$y2)*_MPDFK, $x3*_MPDFK, ($h-$y3)*_MPDFK));
    }

    function getPageCount()
    {
        return count($this->pages);
    }

    function AcceptPageBreak()
    {
        return true;
    }

    public function resetDrawing()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFillColor(255, 255, 255);
    }

    public function snakeToCamel($str, $separator = '_')
    {
        return str_replace(' ', '', ucwords(str_replace($separator, ' ', $str)));
    }

    public function deep_merge()
    {
        $args = func_get_args();
        return $this->deep_merge_recursive($args);
    }

    public function deepMerge()
    {
        $args = func_get_args();
        return $this->deep_merge_recursive($args);
    }

    // modified from drupal_array_merge_deep_array
    // but also handle embeded objects
    protected function deep_merge_recursive($arrays)
    {
        $result = array();

        foreach ($arrays as $array)
        {
            foreach ($array as $key => $value)
            {
                if (is_integer($key))
                {
                    $result[] = $value;
                }
                elseif (isset($result[$key]) && is_array($result[$key]) && is_array($value))
                {
                    $result[$key] = $this->deep_merge_recursive(array($result[$key], $value));
                }
                // handle embeded object with object value
                elseif (isset($result[$key]) && is_object($result[$key]) && is_object($value))
                {
                    $result[$key] = (object)$this->deep_merge_recursive(array((array)$result[$key], (array)$value));
                }
                // handle embeded object with array value
                elseif (isset($result[$key]) && is_object($result[$key]) && is_array($value))
                {
                    $result[$key] = (object)$this->deep_merge_recursive(array((array)$result[$key], $value));
                }
                else
                {
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}