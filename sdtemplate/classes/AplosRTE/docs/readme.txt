AplosRTE WYSIWYG Text Editor

author:    MT Jordan <mtjo@aplosmedia.com>
version:   0.0.1
link:      http://aplosmedia.com/products/rteeditor
copyright: 2004 AplosMedia
license:   LGPL <http://opensource.org/licenses/lgpl-license.php>

-------------------------------------------------------------------------------------------------------
 
1. Open popups/rte_image.php and edit where commented to the /path/to/public/image/folder.

 ie: $list_img = new imgDir( '/path/to/public/image/folder/',
                             'http://yourdomain.com/path/to/public/image/folder/' );

3. In the script where you are replacing the textarea, paste the following code:

include_once '/path/to/rte.php';

$editor = new rteEditor( /path/to/AplosRTE/,
                         $enable_insert_local_image = true/false,
                         $rte_editor_theme = default/blue/silver/green );

$editor->initRTE( fieldname,
                  default content/db content,
                  width,
                  height,
                  $showeditor = true,
                  $readonly = false,
                  'textarea_CSS' );

----------------------Add a second editor

$editor->initRTE( fieldname2,
                  default content/db content,
                  width,
                  height,
                  $showeditor = true,
                  $readonly = false,
                  'textarea_CSS' );


4. In your form tag, add the following event:

onsubmit="return submitForm();"

Note: See test_editor.php for a working example

5. If magic_quotes_gpc = On via the php.ini, your $_POST data will need to be escaped using the stripslashes() function. You will also need to use rawurldecode() to convert entities produced by IE in the insert link functions.

ie: $myOutput = rawurldecode( stripslashes( $_POST['test'] ) );

6. If you want to make modifications to the editor UI, edit rteEngine_source.js. You will need to change the filename in rte.php at line 90 from rteEngine_comp.js to rteEngine_source.js. 

Notes: This editor is IE/Gecko only. All other browsers will see just a textarea. If you need to add styles to the textarea to match your theme for non-IE/Gecko browsers, edit rte_blue.css, rte_default.css rte_seamist or rte_silver.css using the .rteTextarea{} class.


