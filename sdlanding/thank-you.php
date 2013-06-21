<?php include_once( 'config.php' ); ?>
<html>
<head>
	<title><?php echo MI_DEALER_NAME_LOC ?> - <?php echo MI_SALE_TEXT ?> <?php echo MI_SALE_FORM ?> Validated</title>
	<base href="<?php echo MI_SALE_DOMAIN ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="css/<?php echo MI_SALE_CSS ?>" rel="stylesheet" type="text/css">
</head>
<body class="sub">
<div id="main">
	  <h1><?php echo MI_SALE_TEXT ?> <?php echo MI_SALE_FORM ?> Validated</h1>
	  <h2><?php echo MI_DEALER_NAME_LOC ?></h2>

      <?php if(MI_VIDEO_THANKS && file_exists(MI_VIDEO_THANKS)) { ?>
      <p>
          <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="338" height="226" title="Sale introduction video">
              <param name="movie" value="<?php echo MI_VIDEO_THANKS ?>">
              <param name="quality" value="high"><param name="LOOP" value="false">
              <embed src="<?php echo MI_VIDEO_THANKS ?>" width="338" height="226" loop="false" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
          </object>
      </p>
      <?php } ?>      

	  <h3>Sale is <?php echo MI_SALE_DATES ?></h3>
	  <p>You'll receive priority access when you arrive at the delaership as well as a special inventory list of dealer cost and their incentives.</p>
	  <p><em>Remember it's not how much the dealers are trying to make, rather how much they are willing to lose.</em> </p>
	  <p>Get to <?php echo MI_DEALER_NAME_LOC ?> early for best selection.</p>
	  <p>Thanks,</p>
	  <h4><a href="<?php echo MI_DEALER_MAP_URL ?>" target="_mi"><?php echo MI_DEALER_NAME ?><br>
<?php echo MI_DEALER_STREET ?><br />
<?php echo MI_DEALER_CSZ ?></a></h4>
<?php if ( MI_DEALER_MAP_IMAGE ) { ?>
	  <p><a href="<?php echo MI_DEALER_MAP_URL ?>" target="_mi"><img src="<?php echo MI_DEALER_MAP_IMAGE ?>" alt="<?php echo MI_DEALER_NAME ?>, <?php echo MI_DEALER_STREET ?>, <?php echo MI_DEALER_CSZ ?>" border="0"></a></p>
<?php }
else
{ ?>
	  <h4><a href="<?php echo MI_DEALER_MAP_URL ?>" target="_mi">Map to <?php
	  echo MI_DEALER_NAME ?></a></h4>
<?php } ?>
</div>
<?php include_once( 'footer.php' ); ?>