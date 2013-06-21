/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/


%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

IMPORTANT - DO "NOT" MODIFY THE INDEX.PHP FILE IN THE ROOT DIRECTORY OF YOUR
INSTALLATION. THIS IS A GEOCLASSIFIEDS SOFTWARE FILE, AND "NOT" A TYPICAL
WEBSITE'S INDEX.HTML FILE. "ALL" CHANGES TO YOUR SITE ARE DONE THROUGH THE
VARIOUS ADMINISTRATION SETTINGS AND TEMPLATES MENU.

%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%



********************************************************
HTML TEMPLATE BASED SYSTEM
********************************************************

Thank you for buying the GeoClassifieds Enterprise program. Now that the
software is installed on your server, you are ready to begin the setup. To
do this, we recommend that you follow the documentation covered within the
pages of the administration, and also the user manual that can found on the
support pages of our site. 

This document is intended to help you get your site up and running as quickly
as possible, by discussing the default html templates that came with your
software. These templates can be found on the 'templates' admin tool of your
site's admin. Since the Enterprise version is an html template based system,
all pages of the site must have a template assigned to them. The html
template serves as the 'vehicle' that delivers the content of your site to
the visitor's browser. This is done in a couple of different ways: 

Firstly, the overall genre of your site is governed by the template html
through images, text, etc. The template html operates just like any other
html page that you would ordinarily use on a typical website. Therefore, you
can insert javascripts, flash, banner ad systems, etc. into your templates
and that information will be displayed on the front user side.

Secondly, each template will use a tag to display 'dynamic' content from the
database to its respective page. Essentially, these tags will display the
their respective information wherever you have placed them in the template.
In some instances you will use only the <MAINBODY> tag, which will display
the appropriate information for that page. In other cases, you may want to
place additional 'module' tags within your template to display other dynamic
features, such as a 'hottest ads' table, or a special 'featured ads' table.

The template system, and how it works, is covered in more detail within this
product's user manual.




********************************************************
CASCADING STYLESHEETS INTRODUCTION
********************************************************

We have designed this version of our software using Cascading Stylesheets in
our default templates. This was intended to help you set up your site much
more quickly than with past versions of the software. If you are not familiar
with Cascading Stylesheets there are some great articles on the internet
discussing their functionality and value to website developers and owners
alike. For instance, the use of Cascading Stylesheets has been shown to
increase search engine optimization since it reduces the amount of html tags
used within your html templates. Another value is that you can change the
background colors, text colors/fonts, and border colors by opening a single
document. These changes will then reflect throughout your entire site with
only a minute or two of work.

We want to make a VERY IMPORTANT css distinction that you need to be aware of
when setting up your site. There are 'two' sets of css controls you will be
working with:

The 'first set' of css controls is built directly into the coding of the
software to help you administer the 'css' for each page's 'dynamic' content.
Meaning, any text located in the 'pages' or 'modules' tools in the admin is
controlled by the 'font management' tool for that particular page or module.
For example, in the admin, wherever you see an 'edit text' button, you
should also see a 'font management' button to change the css for that
particular text.

The 'second set' of css controls is governed by a css stylesheet document
that was distributed with this software and is located at the root directory
of your site's classifieds files. This stylesheet controls the text fonts
and other various table colors in the html templates assigned within the
'templates' tool in your site's admin.

So, keep in mind, when you want to change a font or table color, you need to
first determine if the css for that text or table is controlled by the
'pages' tool in the admin, or the css stylesheet document.
 
Certainly, the option is still yours. You can use your own html templates by
simply replacing ours with your own if you don't want to use the stylesheet
or templates that came with the program.


********************************************************
CASCADING STYLESHEETS IMPLEMENTATION
********************************************************

Open up any html template ('templates' menu in the admin). You will notice
that within the template are 'class' tags being used throughout the html.
Every 'class' tag controls the information following that tag. Some
developers prefer to set the properties for these 'class' tags within that
template's <head> area. However, we have chosen to reference an external
stylesheet and simply 'point' every html template to reference a single css
document. We have named this css document "geoclassent1.css", which can be
found at the root directory of your installation. All changes to the tag
properties within "geoclassent1.css" will affect those corresponding 'class'
tags in every html template of your site. 

You will notice that there is a 'css' folder in the root directory of your
installation. This folder contains several css documents identical to the
one that is located in the root directory, except we have changed some of
the colors of the fonts and background colors. You will also notice that we
kept the same name for each document (geoclassent1.css). This is because
in the <head> area of each template there is a line of code that points
to the css document being used. In this case, every template points to 
'geoclassent1.css'. You can rename the css document in the root directory
if you want to, but you will also have to rename that line code in every
template to point to your renamed css document.


********************************************************
CASCADING STYLESHEETS SUMMARY
********************************************************

That's all there is to it. If you would like to implement more class tags,
you can do so by adding them to the 'geoclassent1.css' document and
referencing them in your template's html.

NOTE - The 'geoclassent1.css' document only controls the css within your
html 'templates'. Dynamic content displayed on your site is still controlled
by each page's unique font settings in the admin 'pages' tool.





Please send all support related questions to:

                   geosupport@geodesicsolutions.com

 
 
