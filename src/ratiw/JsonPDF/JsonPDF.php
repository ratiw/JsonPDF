<?php namespace ratiw\JsonPDF;

class JsonPDF extends Fpdf
{
    protected $settings     = null;

    protected $header       = null;
    
    protected $footer       = null;

    protected $tables       = null;
 
    protected $varKeys      = null;
    protected $varValues    = null;
    protected $varSets      = null;
    

    public function make($data)
    {
        $data = json_decode($data);

        isset($data->settings) and $this->init($data->settings);
        isset($data->fonts)    and $this->addFonts($data->fonts);
        
        isset($data->header) and $this->setHeader($data->header);
        isset($data->footer) and $this->setFooter($data->footer);
        
        isset($data->tables) and $this->setTables($data->tables);
        isset($data->data)   and $this->setVars($data->data);
        
        $this->AddPage();
        $this->renderSection($data->body);

        return $this;
    }
    
    public function init($settings)
    {
        // Number of Pages Alias
        isset($settings->aliasNbPages) || $settings->aliasNbPages = '{nb}';
        $this->AliasNbPages($settings->aliasNbPages);
        
        // margins
        isset($settgins->leftMargin)  and $this->SetLeftMargin($settings->leftMargin);
        isset($settgins->topMargin)   and $this->SetTopMargin($settings->topMargin);
        isset($settgins->rightMargin) and $this->SetRightMargin($settings->rightMargin);
        // bottomMargin is not used in FPDF, see SetMargins() documentation.
        
        // AutoPageBreak
        if (isset($settings->autoPagebreak))
        {       
            if (isset($settings->autoPagebreakMargin))
            {
                $this->SetAutoPageBreak($settings->autoPagebreak, $settings->autoPagebreakMargin);
            }
            else
            {
                $this->SetAutoPageBreak($settings->autoPagebreak);
            }
        }
        
        // page compression
        isset($settings->compression) and $this->SetCompression($settings->compression);
        
        // display mode
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
        
        // default font
        if (isset($settings->defaultFont))
        {
            $fname  = $settings->defaultFont->name;
            $fstyle = isset($settings->defaultFont->style) ? $settings->defaultFont->style : '';
            $ffile  = isset($settings->defaultFont->file) ? $settings->defaultFont->file : false;
            $fsize  = isset($settings->defaultFont->size) ? $settings->defaultFont->size : 10;
            
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
            $method = 'render'.ucfirst($obj->type);
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
    
    public function renderText($obj)
    {
        $this->setObjectProperties($obj);
        
        // alignment and ln-height
        $align = isset($obj->align) ? $obj->align : 'L';
        $ln = isset($obj->ln) ? $obj->ln : 0;
        
        // border
        $border = isset($obj->border) ? $obj->border : 0;

        // bind data variables
        $text = isset($obj->text) ? $this->bindVars($obj->text) : '';

        // convert text from utf8
        $utf8 = isset($this->settings->utf8) ? $this->settings->utf8 : true;
        if ($utf8)
        {
            $text = ($text == '') ? '' : iconv('UTF-8', 'ISO-8859-11', $text);
        }
        
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
    
    public function renderLine($obj)
    {
        $this->setObjectProperties($obj);
        
        isset($obj->x2) or $obj->x2 = ($this->w - $this->rMargin);
        isset($obj->y2) or $obj->y2 = $obj->y1;
        
        $this->Line($obj->x1, $obj->y1, $obj->x2, $obj->y2);
    }
    
    public function renderRect($obj)
    {
        $this->setObjectProperties($obj);
        
        $style = isset($obj->style) ? strtoupper($obj->style) : 'D';
        
        $this->Rect($obj->x, $obj->y, $obj->width, $obj->height, $style);
    }
    
    public function renderImage($obj)
    {
        $x = isset($obj->x) ? $obj->x : null;
        $y = isset($obj->y) ? $obj->y : null;
        $w = isset($obj->width)  ? $obj->width : 0;
        $h = isset($obj->height) ? $obj->height : 0;
        
        $this->Image($obj->url, $x, $y, $w, $h);
    }
    
    private function initTable($obj)
    {
        is_null($this->tables) and $this->Error('No "tables" section.');
        isset($obj->table) or $this->Error('Table type requires "table" parameter.');
        array_key_exists($obj->table, $this->tables) or $this->Error('['.$obj->table.'] table definition not found!');

        $table = $this->tables[$obj->table];
        isset($table->style) || $table->style = new stdClass;
        
        $this->setObjectProperties($obj);
        
        isset($table->style->{'border-color'}) and $this->callSetColorMethod('SetDrawColor', $table->style->{'border-color'});
    }
    
    public function renderTable($obj)
    {
        $this->initTable($obj);

        $this->_renderTableHeader($obj);
        $this->_renderTableData($obj);
    }

    public function renderTableHeader($obj)
    {
        $this->initTable($obj);
        $this->_renderTableHeader($obj);
    }
    
    public function renderTableBody($obj)
    {
        $this->initTable($obj);
        $this->_renderTableData($obj);
    }
    
    public function setTableStyle($style)
    {
        $this->setObjectProperties($style);
    }
    
    protected function _renderTableHeader($obj)
    {
        $table = $this->tables[$obj->table];
        $columns = $table->columns;

        $lineHeight = isset($table->style->{'title-row'}->height) ? $table->style->{'title-row'}->height : $this->lasth;
        
        isset($table->style->{'title-row'}) and $this->setTableStyle($table->style->{'title-row'});

        $border  = isset($table->style->border) ? $table->style->border : 1;
        $filled  = isset($table->style->{'title-row'}->{'fill-color'}) ? $table->style->{'title-row'}->{'fill-color'} : false;

        for ($i=0; $i<count($columns); $i++)
        {
            $col = $columns[$i];
            $colWidth = isset($col->width) ? $col->width : 20;
            $this->Cell(
                $colWidth, 
                $lineHeight, 
                isset($col->title) ? $col->title : ucfirst($col->name),
                $border, 
                0, 
                isset($col->{'title-align'}) ? $col->{'title-align'} : 'C',
                $filled
            );
        }
        $this->Ln();
    }
    
    protected function _renderTableData($obj)   //$columns, $data
    {
        $table = $this->tables[$obj->table];

        if ( ! isset($this->varSets[$table->data])) return;
        
        $data = $this->varSets[$table->data];
        
        $columns = $table->columns;
        
        $lineHeight = isset($table->style->{'data-row'}->height) ? $table->style->{'data-row'}->height : $this->lasth;

        isset($table->style->{'data-row'}) and $this->setTableStyle($table->style->{'data-row'});
        
        $border  = isset($table->style->border) ? $table->style->border : 'LR';
        $filled  = isset($table->style->{'data-row'}->{'fill-color'}) ? $table->style->{'data-row'}->{'fill-color'} : false;
        $striped = isset($table->style->{'data-row'}->striped) ? $table->style->{'data-row'}->striped : false;

        $totalWidth = 0;
        
        for ($r=0; $r<count($data); $r++)
        {
            $row = $data[$r];
            for ($c=0; $c<count($columns); $c++)
            {
                $col = $columns[$c];
                $colWidth = isset($col->width) ? $col->width : 20;
                $this->Cell(
                    $colWidth, 
                    $lineHeight, 
                    $row->{$col->name}, 
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
        $this->Cell($totalWidth, 0, '', 'T');
    }
    
    public function render()
    {
        $this->Output();
        exit;
    }

    function Header()
    {
        $this->resetDrawing();
        $this->renderSection($this->header);
    }
    
    function Footer()
    {
        $this->resetDrawing();
        $this->renderSection($this->footer);
    }
    
    public function resetDrawing()
    {
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetFillColor(255, 255, 255);
    }
    
}