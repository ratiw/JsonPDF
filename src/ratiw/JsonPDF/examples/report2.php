<?php
// use these require statements if you point your http server directly to this folder.
require "../Fpdf.php";
require "../JsonPDF.php";

// use this require statement instead if you install JsonPDF via composer
// and comment out the above require statements.
//require 'vendor/autoload.php';

    $document = array(

        'header' => array(
            array(
                'type' => 'image',
                'url'  => 'img/rbb_logo.png',
                'x' => 10,
                'y' => 5,
                'width' => 20,
            ),
            array(
                'type' => 'text',
                'text' => '{report_name}',
                'font' => 'THSarabun',
                'font-style' => 'B',
                'font-size'  => 26,
                'y' => 13,
                'align'  => 'R',
            ),
            array(
                'type' => 'text',
                'text' => 'M U I  Rubber Belt Co., Ltd.',
                'font' => 'THSarabun',
                'font-style' => 'b',
                'font-size'  => 20,
                'x' => 33,
                'y' => 13,
                'width' => 80,
            ),
            array(
                'type' => 'text',
                'text' => 'บริษัท เอ็ม ยู ไอ รับเบอร์เบลท์ จำกัด',
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
            array(
                'type' => 'table-header',
                'y' => 35,
                'x' => 10,
                'table' => 'world_info_table',
                'font' => 'THSarabun',
                'font-size' => 20,
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
                'type' => 'table-body',
                'table' => 'world_info_table',
                'font' => 'THSarabun',
                'font-size' => 20,
            ),
        ),

        'tables' => array(
            'world_info_table' => array(
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
                    //'border-color' => '50,55,200',
                    'border' => '',
                    'title-row' => array(
                        'height' => 8,
                        'text-color' => '200, 100, 50',
                        //'fill-color' => '100,50,50',
                        // 'font' => 'Arial',
                    ),
                    'data-row' => array(
                        'height' => 8,
                        'text-color' => '0,0,0',
                        //'fill-color' => '224,235,255',
                        //'striped' => true,
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
        ),

        'fonts' => array(
            array('THSarabun', '', 'THSarabun.php'),
            array('THSarabun', 'B', 'THSarabun Bold.php'),
            array('THSarabun', 'I', 'THSarabun Italic.php'),
            array('THSarabun', 'BI', 'THSarabun Bold Italic.php'),
        ),
    );

    $data = array(
        'name' => 'รติ วรรณภานพ',
        'date' => '09/11/2556',
        'report_name' => 'World Info Report',
        'world_info' => array(
            // set 1
            array('country' => 'Austria', 'capital' => 'Vienna', 'area' => '83,859', 'pop' => '8,075'),
            array('country' => 'Belgium', 'capital' => 'Brussels', 'area' => '30,518', 'pop' => '10,192'),
            array('country' => 'Denmark', 'capital' => 'Copenhagen', 'area' => '43,094', 'pop' => '5,295'),
            array('country' => 'Finland', 'capital' => 'Helsinki', 'area' => '304,529', 'pop' => '5,147'),
            array('country' => 'Franch', 'capital' => 'Paris', 'area' => '543,965', 'pop' => '58,728'),
            array('country' => 'Germany', 'capital' => 'Berlin', 'area' => '357,22', 'pop' => '82,057'),
            array('country' => 'Greece', 'capital' => 'Athens', 'area' => '131,625', 'pop' => '10,511'),
            array('country' => 'Ireland', 'capital' => 'Dublin', 'area' => '70,723', 'pop' => '3,694'),
            array('country' => 'Italy', 'capital' => 'Roma', 'area' => '301,316', 'pop' => '57,563'),
            array('country' => 'Lunxembourg', 'capital' => 'Luxembourg', 'area' => '2,586', 'pop' => '424'),
            array('country' => 'Netherlands', 'capital' => 'Amsterdam', 'area' => '41,526', 'pop' => '15,654'),
            array('country' => 'Portugal', 'capital' => 'Lisbon', 'area' => '91,906', 'pop' => '9,957'),
            array('country' => 'Spain', 'capital' => 'Madrid', 'area' => '504,790', 'pop' => '39,348'),
            array('country' => 'Sweden', 'capital' => 'Stockholm', 'area' => '410,934', 'pop' => '8,839'),
            array('country' => 'United Kingdom', 'capital' => 'London', 'area' => '243,820', 'pop' => '58,862'),
            // set 2
            array('country' => 'Austria', 'capital' => 'Vienna', 'area' => '83,859', 'pop' => '8,075'),
            array('country' => 'Belgium', 'capital' => 'Brussels', 'area' => '30,518', 'pop' => '10,192'),
            array('country' => 'Denmark', 'capital' => 'Copenhagen', 'area' => '43,094', 'pop' => '5,295'),
            array('country' => 'Finland', 'capital' => 'Helsinki', 'area' => '304,529', 'pop' => '5,147'),
            array('country' => 'Franch', 'capital' => 'Paris', 'area' => '543,965', 'pop' => '58,728'),
            array('country' => 'Germany', 'capital' => 'Berlin', 'area' => '357,22', 'pop' => '82,057'),
            array('country' => 'Greece', 'capital' => 'Athens', 'area' => '131,625', 'pop' => '10,511'),
            array('country' => 'Ireland', 'capital' => 'Dublin', 'area' => '70,723', 'pop' => '3,694'),
            array('country' => 'Italy', 'capital' => 'Roma', 'area' => '301,316', 'pop' => '57,563'),
            array('country' => 'Lunxembourg', 'capital' => 'Luxembourg', 'area' => '2,586', 'pop' => '424'),
            array('country' => 'Netherlands', 'capital' => 'Amsterdam', 'area' => '41,526', 'pop' => '15,654'),
            array('country' => 'Portugal', 'capital' => 'Lisbon', 'area' => '91,906', 'pop' => '9,957'),
            array('country' => 'Spain', 'capital' => 'Madrid', 'area' => '504,790', 'pop' => '39,348'),
            array('country' => 'Sweden', 'capital' => 'Stockholm', 'area' => '410,934', 'pop' => '8,839'),
            array('country' => 'United Kingdom', 'capital' => 'London', 'area' => '243,820', 'pop' => '58,862'),
            // set 3
            array('country' => 'Austria', 'capital' => 'Vienna', 'area' => '83,859', 'pop' => '8,075'),
            array('country' => 'Belgium', 'capital' => 'Brussels', 'area' => '30,518', 'pop' => '10,192'),
            array('country' => 'Denmark', 'capital' => 'Copenhagen', 'area' => '43,094', 'pop' => '5,295'),
            array('country' => 'Finland', 'capital' => 'Helsinki', 'area' => '304,529', 'pop' => '5,147'),
            array('country' => 'Franch', 'capital' => 'Paris', 'area' => '543,965', 'pop' => '58,728'),
            array('country' => 'Germany', 'capital' => 'Berlin', 'area' => '357,22', 'pop' => '82,057'),
            array('country' => 'Greece', 'capital' => 'Athens', 'area' => '131,625', 'pop' => '10,511'),
            array('country' => 'Ireland', 'capital' => 'Dublin', 'area' => '70,723', 'pop' => '3,694'),
            array('country' => 'Italy', 'capital' => 'Roma', 'area' => '301,316', 'pop' => '57,563'),
            array('country' => 'Lunxembourg', 'capital' => 'Luxembourg', 'area' => '2,586', 'pop' => '424'),
            array('country' => 'Netherlands', 'capital' => 'Amsterdam', 'area' => '41,526', 'pop' => '15,654'),
            array('country' => 'Portugal', 'capital' => 'Lisbon', 'area' => '91,906', 'pop' => '9,957'),
            array('country' => 'Spain', 'capital' => 'Madrid', 'area' => '504,790', 'pop' => '39,348'),
            array('country' => 'Sweden', 'capital' => 'Stockholm', 'area' => '410,934', 'pop' => '8,839'),
            array('country' => 'United Kingdom', 'capital' => 'London', 'area' => '243,820', 'pop' => '58,862'),
        ),
    );

    $pdf = new ratiw\JsonPDF\JsonPDF('P', 'mm', 'A4');
    $pdf->make(json_encode($document), json_encode($data));
    $pdf->render();

?>