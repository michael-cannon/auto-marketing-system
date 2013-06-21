<style type="text/css">

@import url(htmlarea.css);

</style>



<script type="text/javascript" src="htmlarea.js"></script>

<script type="text/javascript" src="en.js"></script>

<script type="text/javascript" src="dialog.js"></script>



<script type="text/javascript">



var editor = null;

function initEditor() {

  editor = new HTMLArea("c[template_code]");



  // comment the following two lines to see how customization works

  editor.generate();

  return false;



}



function insertHTML() {

  var html = prompt("Enter some HTML code here");

  if (html) {

    editor.insertHTML(html);

  }

}

function highlight() {

  editor.surroundHTML('','');

}

</script>