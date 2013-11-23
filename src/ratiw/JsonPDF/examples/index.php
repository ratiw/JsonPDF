<?php
// use these require statements if you point your http server directly to this folder.
require "../Fpdf.php";
require "../JsonPDF.php";

// use this require statement instead if you install JsonPDF via composer 
// and comment out the above require statements.
//require 'vendor/autoload.php';

    $data = array(

        'header' => array(
            array(
                'type' => 'image',
                'url'  => 'img/logo.gif',
                'x' => 10,
                'y' => 10,
                'width' => 20,
            ),
            array(
                'type' => 'text',
                'text' => 'Country Data',
                'font' => 'THSarabun',
                'font-style' => 'B',
                'font-size'  => 26,
                'y' => 13,
                'align'  => 'R',
            ),
            array(
                'type' => 'text',
                'text' => 'World Infomation Corp.',
                'font' => 'THSarabun',
                'font-style' => 'b',
                'font-size'  => 22,
                'x' => 33,
                'y' => 13,
                'width' => 80,
            ),
            array(
                'type' => 'text',
                'text' => 'All the information at your reach.',
                'font' => 'THSarabun',
                'font-size'  => 16,
                'x' => 33,
                'y' => 20,
                'width' => 80,
            ),
            array(
                'type' => 'line',
                'x1' => 10, 'y1' => 28,
                'draw-color' => '100',  // gray scale
            ),
        ),
        'footer' => array(
            array(
                'type' => 'text',
                'y' => -15,
                'width' => 0,
                'height' => 10,
                'font' => 'Arial',
                'font-style' => 'I',
                'font-size'  => 8,
                'draw-color' => '0,0,0',
                'text' => 'Page {page}/{nb}',
                'border' => 'T',
                'align' => 'C',
            ),
        ),

        'body' => array(
            array(
                'type' => 'text', 'x' => 10, 'y' => 20, 
                'multiline' => true,
                'font' => 'THSarabun', 
                'font-size' => 20,
                'text' => 'Hello, {name}. Today is {date}',
                'align' => 'L',
                'y' => 35,
                'width' => 150,
                'height' => 10,
                'text-color' => '0,0,0',
                'fill-color' => '255,125,125',
            ),

            array(
                'type' => 'table-header',
                'y' => 90,
                'table' => 'world_info_table',
            ),
            array(
                'type' => 'tableBody',
                'table' => 'world_info_table',
            ),
            array(
                'type' => 'line',
                'x1' => 10, 'y1' => 190,
                'x2' => 150, 'y2' => 190,
                'draw-color' => '255,128,128',
            ),
            array(
                'type' => 'rect',
                'x' => 10, 'y' => 60,
                'width' => 50,
                'height' => 20,
                'draw-color' => '255,128,128',
            ),
            array(
                'type' => 'text',
                'multiline' => true,
                'x' => 10, 
                'y' => 200, 
                'font' => 'THSarabun', 
                'font-size' => 16,
                'text' => "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum. Typi non habent claritatem insitam; est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius. Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum. Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.",
                'align' => 'L',
                'width' => 180,
                'height' => 8,
            ),
        ),
        
        'tables' => array(
            'world_info_table' => array(
                'max-rows' => 10,
                'columns' => array(
                    array(
                        'name' => 'country',
                        'width' => 45,
                        'title' => 'Country',
                        'title-align' => 'L',
                        'data-align'  => 'L',
                    ),
                    array(
                        'name' => 'capital',
                        'width' => 40,
                        'title' => 'Capital',
                        'data-align'  => 'L',
                    ),
                    array(
                        'name' => 'area',
                        'width' => 45,
                        'title' => 'Area (sq km)',
                        'title-align' => 'C',
                        'data-align'  => 'R',
                    ),
                    array(
                        'name' => 'pop',
                        'width' => 50,
                        'title' => 'Pop. (thousands)',
                        'title-align' => 'C',
                        'data-align'  => 'R',
                    ),
                ),
                'data' => 'world_info',
                'style' => array(
                    'border-color' => '50,55,200',
                    'title-row' => array(
                        'text-color' => '200, 100, 50',
                        'fill-color' => '100,50,50',
                        // 'font' => 'Arial',
                    ),
                    'data-row' => array(
                        'height' => 8,
                        'text-color' => '0,0,0',
                        'fill-color' => '224,235,255',
                        // 'font' => 'THSarabun',
                        // 'font-style' => 'B',
                        // 'font-size' => 10,
                        'striped' => true,
                    ),
                ),
            ),
        ),
        
        'settings' => array(
            'title'       => 'Test PDF',
            'author'      => 'Rati Wannapanop',
            'subject'     => 'Test Creating PDF',
            'keywords'    => 'test pdf fpdf ratiw',
            'default-font' => array(
                'name' => 'THSarabun',
                'size' => 30,
            ),
            'header-height' => 30,
        ),
        
        'fonts' => array(
            array('THSarabun', '', 'THSarabun.php'),
            array('THSarabun', 'B', 'THSarabun Bold.php'),
            array('THSarabun', 'I', 'THSarabun Italic.php'),
            array('THSarabun', 'BI', 'THSarabun Bold Italic.php'),
        ),
        'data' => array(
            'name' => 'Rati',
            // 'date' => 'Nov. 09, 2013',
            'date' => date('l jS \of F Y h:i:s A'),
            'world_info' => array(
                array('country' => 'Austria', 'capital' => 'Vienna', 'area' => '83,859', 'pop' => '8,075'),
                array('country' => 'Belgium', 'capital' => 'Brussels', 'area' => '30,518', 'pop' => '10,192'),
                array('country' => 'Denmark', 'capital' => 'Copenhagen', 'area' => '43,094', 'pop' => '5,295'),
                array('country' => 'Finland', 'capital' => 'Helsinki', 'area' => '304,529', 'pop' => '5,147'),
                array('country' => 'Franch', 'capital' => 'Paris', 'area' => '543,965', 'pop' => '58,728'),
                array('country' => 'Germany', 'capital' => 'Berlin', 'area' => '357,22', 'pop' => '82,057'),
            ),
        ),
        
    );

    $pdf = new ratiw\JsonPDF\JsonPDF('P', 'mm', 'A4');
    $pdf->make(json_encode($data));
    $pdf->render();

?>