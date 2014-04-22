<?php
// use these require statements if you point your http server directly to this folder.
require "../Fpdf.php";
require "../JsonPDF.php";

// use this require statement instead if you install JsonPDF via composer
// and comment out the above require statements.
//require 'vendor/autoload.php';

    $document = array(

        'body' => array(
            array(
                'type' => 'text',
                'width' => 40,
                'height' => 10,
                'text' => 'Hello World!',
                'font' => 'Arial',
                'font-style' => 'B',
                'font-size' => 16,
            ),
            array(
                'type' => 'text',
                'text' => 'Hi, there!',
            ),
        ),
    );

    $pdf = new ratiw\JsonPDF\JsonPDF('P', 'mm', 'A4');
    $pdf->make(json_encode($document));
    $pdf->render();

?>
