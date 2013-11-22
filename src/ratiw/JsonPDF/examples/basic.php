<?php
require "vendor/autoload.php";

    $data = array(
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
    $pdf->make(json_encode($data));
    $pdf->render();

?>
