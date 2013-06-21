<?php include_once( 'config.php' ); ?>
<html>
<head>
	<title><?php echo MI_DEALER_NAME_LOC ?> - <?php echo MI_SALE_TEXT ?> Validation</title>
	<base href="<?php echo MI_SALE_DOMAIN ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script src="includes/script_tmt_validator.js" type="text/javascript"></script>
	<script src="includes/script_tmt_validator.cb.js" type="text/javascript"></script>
    <script src="includes/simpleswap.js " type="text/javascript"></script>
	<link href="css/<?php echo MI_SALE_CSS ?>" rel="stylesheet" type="text/css">
</head>
<body class="sub">
<div id="main">
	  <?php echo MI_SALE_LOGO ?>
	  <h1><?php echo MI_SALE_TEXT ?> Validation </h1>
	  <h2><?php echo MI_DEALER_NAME_LOC ?></h2>

      <?php if(MI_VIDEO_REGISTER && file_exists(MI_VIDEO_REGISTER)) { ?>
      <p style="text-align: center;">
          <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="338" height="226" title="Sale introduction video">
              <param name="movie" value="<?php echo MI_VIDEO_REGISTER ?>">
              <param name="quality" value="high"><param name="LOOP" value="false">
              <embed src="<?php echo MI_VIDEO_REGISTER ?>" width="338" height="226" loop="false" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
          </object>
          <br />
      </p>
      <?php } ?>

	  <h3>Sale is <?php echo MI_SALE_DATES ?></h3>
	  <?php if ( ! MI_AUCTION_URL ) { ?>
	  <form action="includes/process.php" method="post" enctype="multipart/form-data" tmt:validate="true" tmt:callback="displayError">
			<table class="register">
			<tr>
			<td colspan="2" align="center">
			<h4>Validate Your <?php echo MI_SALE_FORM ?> Below</h4>
			<div id="errorDisplay"><span></span></div>
			</td>
			<td class="smaller" width="150" rowspan="11" align="left" valign="top">
					<p>Fill out the form at left to validate your invitation to the <?php echo MI_SALE_TEXT ?>.</p>
					<p>You'll receive priority access when you arrive at the delaership as well as a special inventory list of dealer cost and their incentives.</p>
					<p><em>Remember it's not how much the dealers are trying to make, rather how much they are willing to lose.</em></p>
			</td>
			</tr>
			<tr>
			<td class="right">
					<label>Email</label>			
			</td>
			<td>
					<input type="text" name="Email" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="Valid email required" tmt:pattern="email" />
			</td>
			</tr>
			<tr>
			<td class="right">
					<label>First Name</label>	
			</td>
			<td>
					<input name="First_Name" type="text" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="First name required" />
			</td>
			</tr>
			<tr>
			<td class="right">
					<label>Last Name</label>		
			</td>
			<td>
					<input name="Last_Name" type="text" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="Last name required" />
			</td>
			</tr>
			<tr>
			<td class="right">
					<label>Address</label> 			
			</td>
			<td>
					<input type="text" name="Address" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="Mailing address required" />
			</td>
			</tr>
			<tr>
			<td class="right">
					<label>Address Line 2</label> 			
			</td>
			<td>
					<input type="text" name="Address_2" />
			</td>
			</tr>
			<tr>
						<td class="right">
					<label>City</label>
			</td>
						<td>
					<input type="text" name="City" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="City required" />
			</td>
			</tr>
			<tr>
						<td class="right">
					<label>State</label>
			</td>
						<td>
					<input name="State" type="text" size="3" maxlength="2" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="2-letter state code required" tmt:minlength="2" tmt:maxlength="2" tmt:pattern="lettersonly" />
			</td>
			</tr>
			<tr>
						<td class="right">
					<label>Zip</label>
			</td>
						<td>
					<input name="Zip" type="text" size="6" maxlength="5" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="5-digit zip code required" tmt:minlength="5" tmt:maxlength="5" tmt:pattern="integer" />
			</td>
			</tr>
			<tr>
						<td class="right">
					<label>Home Phone</label>
					</td>
					<td>
					<input name="Home_Phone" type="text" size="13" maxlength="12" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="Home phone required" tmt:pattern="usPhone" />
					<br />
					<span class="small">
						Please insert as NNN-NNN-NNNN
					</span>
			</td>
			</tr>
			<tr>
						<td class="right">
					<label>Work Phone</label>
			</td>
			<td>
								<input name="Work_Phone" type="text" size="13" maxlength="12" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="Work phone as required" tmt:pattern="usPhone" />
					<label>Ext.
					<input name="Work_Phone_Ext" type="text" size="6" maxlength="6" tmt:errorclass="invalid" tmt:message="Numeric work phone extension required" tmt:pattern="integer" />
					</label>
			</td>
			</tr>
			<tr>
			<td class="right">
					Are you trading a vehicle? 
			</td>
			<td>
					<label>
					<input name="Trading_Vehicle" type="radio" value="Yes">
					Yes</label>
					<label>
					<input name="Trading_Vehicle" type="radio" value="No">
					No</label>
			</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
                    <input name="Submit" type="image" value="Submit"
                    src="images/form-submit.gif"
                    oversrc="images/form-submit-over.gif"
                    downsrc="images/form-submit-down.gif"
                    alt="Submit" /> 
				</td>
				<td>&nbsp;</td>
			</tr>
			</table>
		</form>
	<?php }

		// Grab registration form auction site
		else
		{
// call up registration
$registerRealUrl				= MI_AUCTION_URL . 'register.php';
$registration					= file_get_contents( $registerRealUrl );

// parse it down to core
$regStart						= '<!# CENTER COLUMN - MAIN TABLE START-->';
$regStart						= preg_quote( $regStart );

$regEnd							= '<!# CENTER COLUMN - MAIN TABLE END-->';
$regEnd							= preg_quote( $regEnd );

$registration					= preg_replace( "/.*$regStart/s"
									, ''
									, $registration
								);

$registration					= preg_replace( "/$regEnd.*/s"
									, ''
									, $registration
								);
	?>
		<table class="register">
			<tr>
				<td>
					<?php echo $registration ?>
				</td>
				<td style="vertical-align: top;">
					<br><h3>Already Registered?</h3>
					<p>Please <a href="<?php echo MI_AUCTION_URL ?>index.php?a=10">login</a>.</p>
					<p><small><a href="<?php echo MI_AUCTION_URL
					?>index.php?a=18">Forgot Password</a>?</small></p>
				</td>
			</tr>
		</table>
	<?php } ?>
</div>
<?php include_once( 'footer.php' ); ?>