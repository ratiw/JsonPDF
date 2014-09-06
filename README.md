
__JsonPDF__ is a wrapper class for [FPDF](http://www.fpdf.org) to allow creating PDF document from [JSON](http://en.wikipedia.org/wiki/JSON) data.


##Install using Composer
Just add the requirement to you `composer.json` file.

    {
        "require": {
            "ratiw/jsonpdf": "dev-master"
        }
    }


----
##Example

    <?php
        require "vendor/autoload.php";

        // This part usually comes from file or http request
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
                    'text' => 'Hi, {name}!',
                ),
            ),
        );
        
        $data = array(
            'name' => 'Rati'
        );

        // The main code is here
        $pdf = new ratiw\JsonPDF\JsonPDF('P', 'mm', 'A4');
        $pdf->make(json_encode($document), json_encode($data), 'both');
        // $pdf->make(json_encode($document), json_encode($data), 'form');
        // $pdf->make(json_encode($document), json_encode($data), 'data');
        $pdf->render();
    ?>

See more examples in the `examples` direcoty.

----
##JSON data structure


    {
        "header":   [..array of object..],
        "footer":   [..array of object..],
        "body":     [..array of object..],
        "settings": [
            "title":    "Document Title",
            "author":   "Author name",
            "subject":  "Subject",
            "creator":  "Generator name",
            "keywords": "list of keywords here",
            "defaultFont": [
                "name": "FontName",
                "size":  16
            ]
        ],
        "fonts":    [..array of font to be added..],
        "data":     [..array of data..],
        "tables":   [..table definition..]
    }


 Sections     | Description
:-------------|:-------------------------------------------
__header__    | Contains array of objects to be rendered in the header section on every pages of the PDF document.
__footer__    | Contains array of objects to be rendered in the footer section on every pages of the PDF document.
__body__      | Contains array of objects to be rendered in the body section of the PDF docuemnt.
__settings__  | Defines the properties and the default settings of the PDF document.
__fonts__     | List of fonts to be added before rendering the PDF document. These fonts will be embedded to the document by default.
__data__      | Array of data to be binded to the text object while rendering the PDF document.
__tables__    | Defines each table properties. These table will be reference by `table`, `table-header`, `table-body` objects.

##Objects
There are 5 object types in JsonPDF. Each doing a specific task to render PDF document.

Every objects share some common properties like the draw color, fill color (background color), x-y position, etc.

The following object types are available:

- text
- line
- rect
- image
- table


###Common Object Properties
The following properties are common to all objects, which mean they can be set on any object. They may or may not affect the rendering of the object depending on whether that object uses that property or not.

__properties__

- `x` _(optional)_
    starting horizontal position of the object.

- `y` _(optional)_
    starting vertical position of the object.

- `font` _(optional)_
    font name.

- `font-style` _(optional)_
    font style: N, B, BI

- `font-size` _(optional)_
    font size

- `text-color` _(optional)_
    text color. If omitted, the current text color will be used.

- `draw-color` _(optional)_
    line color. If omitted, the current line color will be used.

- `fill-color` _(optional)_
    fill color, If omitted, the current fill color will be used.

- `line-width` _(optional)_
    line width. If omitted, the default value (0.2mm as specified in the [FPDF document](http://www.fpdf.org/en/doc/setlinewidth.htm)) will be used.

- `render-as` _(optional)_
    specify whether this object should be render as part of the `form` or as `data`.

###Render-as Property
JsonPDF can be rendered in three different mode. Render only those objects marked as `form` (`RENDER_FORM_ONLY`), render only those objects marked as `data` (`RENDER_DATA_ONLY`), or render both `form` and `data` ('RENDER_ALL`).

The rendered document can be shown as a blank form (`RENDER_FORM_ONLY`), or can be shown raw data (`RENDER_DATA_ONLY`) to printed on the pre-made paper form, or can be shown both (`RENDER_ALL`) to be printed on plain paper.

By default if the `render-as` property of the object is not defined, the object will be regarded as `RENDER_ALL`. That means it will get rendered both as part of a `form` and a `data`. 


###Text Object
The `text` object uses to display text at the specified position. Data can be bound to the `text` object by enclosing the data name in curly braces `{}` embedding in the _text_ property of the `text` object.

The following example shows the `username` data embedded in the text property. This `username` will be replaced by the actual data provided in the `data` section of the JSON.

    "body": [
        {
            "type": "text",
            "x": 10,
            "y": 20,
            "text": "Hello, {username}. How are you today!",
            "text-color": "255,125,125"
        }
    ],
    "data": [
        "username": "Rati"
    ]

####Additional properties

- `align` _(optional)_

- `ln` _(optional)_

- `border` _(optional)_

- `text` _(optional)_

- `width` _(optional)_

- `height` _(optional)_

- `multiline` _(optional)_


###Line Object
The `line` object uses to draw a line at the given position specified by `x1`, `y1`, `x2`, `y2`.

__properties__

- `x1`, `y1`
    starting position of the line.

- `x2`, `y2`
    end position of the line. If `x2` is omitted, the line will be drawn to the right margin of the document. If `y2` is omitted, it assumes the value of `y1`, thus drawing a straight line.


### Rect Object
The `rect` object uses to draw a rectangle on the document using the specified `x`, `y`, `width`, `height` properties.

__properties__

- `x`, `y`
    starting position of the object.

- `width`
- `height`
- `radius` _(optional)_
    If provided, will draw a rounded rectangle.
- `style` _(optional)_


### Image Object
The `image` object uses to draw the given image on the document at the speicifed location, `width` and `height`.

__properties__

- `url`
    URL of the image.

- `width` _(optional)_
- `height` _(optional)_


### Table, Table Header, Table Body Objects
The `table` object is used to draw a data table on the document. The table properties must be defined in the `tables` section and the data must be present in the `data` section of the JSON.

`table` object will render the complete table with the table header and body.

`table-header` object will render only the header part of the given table.

`table-body` object will render only the body part of the given table.

__properties__

- `table`
    Name of table defined in the `tables` section.


----
##Settings
This section is used to set various properties for the PDF document.

    "settings": {
        "title": "Test PDF Document",
        "author": "Rati Wannapanop",
        "creator": "JsonPDF"
    }

__properties__

- `alias-nb-pages`
- `left-margin`
- `top-margin`
- `right-margin`
- `auto-pagebreak`
- `auto-agebreak-margin`
- `compression`
- `zoom`
- `layout`
- `default-font`
- `utf8`
- `author`
- `title`
- `subject`
- `keywords`
- `creator`
- `header-height`


----
##Defining Fonts
You can add custom fonts to be used in your PDF document in this section by providing the array of `fontname`, `fontstyle`, and `fontfile`.

In order to use your own custom fonts, you must creating the `fontfile` using FPDF's MakeFont function.

See more information on this at [FPDF website](http://www.fpdf.org/en/tutorial/tuto7.htm).

    "fonts": [
        ["THSarabun", "", "THSarabun.php"],
        ["THSarabun", "B", "THSarabun Bold.php"],
        ["THSarabun", "I", "THSarabun Italic.php"],
        ["THSarabun", "BI", "THSarabun Bold Italic.php"]
    ]


----
##Data Binding
You define data for the variables in this `data` section. Variable name is enclosed in the curly braces, e.g. `{name}`.

Variables are usually embedded in the `text` property of the `Text` object.

    "body": [
        {
            'type': 'text',
            'text': 'Hello, {name}! Today is {date}.'
        }
    ],
    //
    // ....
    //
    "data": {
        "name": "Rati Wannapanop",
        "date": "17/11/2556",
        "table1": [
            ["country": "Austria", "capital": "Vienna", "area": "83,859", "pop": "8,075"],
            ["country": "Belgium", "capital": "Brussels", "area": "30,518", "pop": "10,192"],
            ["country": "Denmark", "capital": "Copenhagen", "area": "43,094", "pop": "5,295"]
        ]
    }

----
##Tables Definition
You can define tables structure in this section. Each table definition consists of 3 properties: `columns`, `data`, and `style`.

`rows-per-page` property (_optional_) specifies the number of rows to be displayed per page. Blank rows will be displayed if necessary.

`columns` property defines each column characteristic for the given table. See Table Column below.

`data` property specifies which _key_ in the `data` section should be used for data rendering inside the given table.

`style` property defines how the given table should be rendered. The `style` properties is optional and if omitted default value will be used. See Table Style below.


    "tables": [
        ["world_info_table": {
            "rows-per-page": 10,
            "columns": [
                {
                    "name": "country",
                    "width": 45,
                    "title": "Country",
                    "title-align": "L",
                    "data-align": "L"
                },
                ...
                ...
                {
                    "name": "pop",
                    "width": 50,
                    "title": "Pop. (thousands)",
                    "title-align": "C",
                    "data-align": "R"
                }
            ],
            "data": "world_info_data",
            "style": {
                "border-color": "50,55,200",
                "border": "LR",
                "title-row": {
                    "height": 8,
                    "text-color": "200,100,50",
                    "fill-color": "100,50,50"
                },
                "data-row": {
                    "height": 8,
                    "text-color": "0,0,0",
                    "fill-color": "224,235,255",
                    "striped": true,
                }
            }
        }],
        ["second_table": {
            ...
            ...
        }]
    ],


__Table Column__

    "columns": [
        {"name": "country", "width": 45, "title": "Country", "title-align": "L", "data-align": "L"},
        {"name": "capital", "width": 40, "title": "Capital", "data-align": "L"},
        {"name": "area", "width": 45, "title": "Area (sq km)", "title-align": "C", "data-align": "R"},
        {"name": "pop", "width": 50, "title": "Pop. (thousands)", "title-align": "C", "data-align": "R"}
    ]

- `name` -- column name
- `width` -- column width
- `title` -- column title
- `title-align` -- column title alignment
- `data-align` -- data column alignment


__Table Style__
The table `style` property allow the user to define how the table should looks.

    "style": {
        "border-color": "50,55,200",
        "title-row": {
            "text-color": "200,100,50",
            "fill-color": "100,50,50",
        },
        "data-row": {
            "height": 8,
            "text-color": "0,0,0",
            "fill-color": "224,235,255",
            "striped": true
        }
    }

- `border-color` -- specify the border color of the table.

- `title-row` -- define how the title row should be rendered.
    - `height`  -- row height
    - `text-color`
    - `fill-color`
    - `font`
    - `font-style`
    - `font-size`

- `data-row` -- define how the data row should be rendered.
    - `height`  -- row height
    - `text-color`
    - `fill-color`
    - `striped`
    - `font`
    - `font-style`
    - `font-size`

----
##Utility functions

 - `snakeToCamel`
 - `deepMerge` or `deep_merge`

