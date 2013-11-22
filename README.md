JsonPDF
=======

__JsonPDF__ is a wrapper class for [FPDF](http://www.fpdf.org) to allow creating PDF document from [JSON](http://en.wikipedia.org/wiki/JSON).


##Installation
aaa


----
##Example

bbbb

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
        "data":     [..array of data..]
    }


 Sections     | Description
:-------------|:-------------------------------------------
__header__    | Contains array of objects to be rendered in the header section on every pages of the PDF document.
__footer__    | Contains array of objects to be rendered in the footer section on every pages of the PDF document.
__body__      | Contains array of objects to be rendered in the body section of the PDF docuemnt.
__settings__  | Defines the properties and the default settings of the PDF document.
__fonts__     | List of fonts to be added before rendering the PDF document. These fonts will be embedded to the document by default.
__data__      | Array of data to be binded to the text object while rendering the PDF document.


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

- `y` _(optional)_       
    starting position of the line.  

- `font` _(optional)_    

- `font-style` _(optional)_     

- `font-size` _(optional)_      

- `text-color` _(optional)_    

- `draw-color` _(optional)_    

- `fill-color` _(optional)_    

- `line-width` _(optional)_    



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

- `draw-color` _(optional)_   
    line color. If omitted, the current color will be used.     

- `line-width` _(optional)_   
    line width. If omitted, the default value (0.2mm as specified in the [FPDF document](http://www.fpdf.org/en/doc/setlinewidth.htm)) will be used.    


### Rect Object     
The `rect` object uses to draw a rectangle on the document using the specified `x`, `y`, `width`, `height` properties.

__properties__      

- `x`, `y`    
    starting position of the line.  

- `width`   
- `height`  
- `style` _(optional)_   
- `line-width` _(optional)_     
- `draw-color` _(optional)_    
- `fill-color` _(optional)_    


### Image Object    
The `image` object uses to draw the given image on the document at the speicifed location, `width` and `height`.

__properties__      

- `url`     
- `x` _(optional)_      
- `y` _(optional)_         
    starting position of the line.  

- `width` _(optional)_         
- `height` _(optional)_        


### Table Object    
The `table` object uses to draw a data table on the document. The data must be present in the `data` section of the JSON.

    

__properties__      

- `columns`     
    Array of column properties, see Table Column below.
    
- `data`    
    Name of data to be used, as defined in the `data` section of the JSON.

- `x` _(optional)_      

- `y` _(optional)_      

- `style` _(optional)_      
    Various properties to style the table, see Table Style below. The `style` properties is optional and if omitted default value will be used.

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
    
- `title-row` -- define how the title row should be drawn.
    - `height`  
    - `text-color`  
    - `fill-color`  
    - `font`    
    - `font-style`  
    - `font-size`   
    
- `data-row`    
    - `height`  
    - `text-color`  
    - `fill-color`  
    - `striped` 
    - `font`    
    - `font-style`  
    - `font-size`   

----
##Settings
BBBB

    "settings": {
        "title": "Test PDF Document",
        "author": "Rati Wannapanop",
        "creator": "JsonPDF"
    }

__properties__      

- `aliasNbPages`     
    AAA

- `leftMargin`  
- `topMargin`   
- `rightMargin` 
- `autoPagebreak`   
- `autoPagebreakMargin` 
- `compression` 
- `zoom`    
- `layout`  
- `defaultFont` 
- `utf8`    
- `author`  
- `title`   
- `subject` 
- `keywords`    
- `creator` 


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
ddd 

    "data": {
        "name": "Rati Wannapanop",
        "date": "17/11/2556",
        "table1": [
            ["country": "Austria", "capital": "Vienna", "area": "83,859", "pop": "8,075"],
            ["country": "Belgium", "capital": "Brussels", "area": "30,518", "pop": "10,192"],
            ["country": "Denmark", "capital": "Copenhagen", "area": "43,094", "pop": "5,295"]
        ]
    }
