<?php include_once( 'config.php' ); ?>
<html>
<head>
	<base href="<?php echo MI_SALE_DOMAIN ?>" />
	<meta http-equiv="Content-Language" content="en-us">
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<title><?php echo MI_DEALER_NAME_LOC ?> - <?php echo MI_SALE_TEXT ?> Welcome</title>
</head>

<body bgcolor="#C0C0C0">

<table id="table137" cellSpacing="0" cellPadding="0" width="769" align="center">
	<tr>
		<td bgColor="#99ccfe">
		<table id="table138" cellSpacing="0" cellPadding="0" width="100%" border="0">
			<tr>
				<td background="index.6.gif" bgColor="#100973" colSpan="9" height="68">
				<table id="table139" height="33" cellSpacing="0" cellPadding="0" width="100%" border="0">
					<tr>
						<td vAlign="top" width="206" background="index.10.gif">
						<p align="center"><b><font face="Arial" color="#FFFFFF">
						<?php echo MI_SALE_TEXT ?></font></b></td>
						<td vAlign="top" width="62" background="index.10.gif">
						<b><u>
								<font face="Arial" size="2">
								<a href="validate.php">
								<font color="#FFFF00">Members</font></a></font></u></b></td>
						<td vAlign="top" width="481" background="index.10.gif">
						<p align="center" style="margin-top: 0; margin-bottom: 0"><b>
								<font face="Arial" color="#FFFFFF">
								<?php echo MI_DEALER_NAME ?>
								</font></b>
						</td>
						<td vAlign="top" width="20" background="index.10.gif">
						&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td width="45" bgColor="#ffffff" height="19">&nbsp;</td>
				<td width="135" bgColor="#ffffff" height="19"><b>
				<font style="font-size: 9pt" face="Arial">
				<a href="validate.php">View Bank 
				Inventory</a></font></b></td>
				<td width="54" bgColor="#ffffff" height="19">&nbsp;</td>
				<td width="118" bgColor="#ffffff" height="19">
				<p align="center"><b>
				<font style="font-size: 9pt" face="Arial" color="#000000">
				<a href="validate.php">Search By Year</a></font></b></td>
				<td width="51" bgColor="#ffffff" height="19">&nbsp;</td>
				<td width="149" bgColor="#ffffff" height="19">
				<p align="center"><b>
				<font style="font-size: 9pt" face="Arial" color="#000000">
				<a href="validate.php">Search By Make</a></font></b></td>
				<td width="48" bgColor="#ffffff" height="19">&nbsp;</td>
				<td width="112" bgColor="#ffffff" height="19">
				<p align="center"><b><font style="font-size: 9pt" face="Arial">
				<a href="easy-loan-application.php">
				Apply for a loan </a></font></b></td>
				<td bgColor="#ffffff" height="19">&nbsp;</td>
			</tr>
			<tr>
				<td background="index.5.gif" bgColor="#ffffff" colSpan="9">
				<font color="#d8d9dc"><span style="font-size: 5.5pt">m</span></font></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td bgColor="#d8d9dc" height="1">
		<img height="1" src="images/clear.gif" width="6"></td>
	</tr>
	<tr>
		<td bgColor="#ffffff">
		<table id="table141" cellSpacing="0" cellPadding="0" width="100%" border="0">
			<tr>
				<td width="94%" bgColor="#100973" height="29">&nbsp;<table id="table142" cellSpacing="0" cellPadding="0" width="769" border="0">
					<tr>
						<td vAlign="top" width="349" rowSpan="24">
						<div align="center">
				<?php if(MI_VIDEO_WELCOME && file_exists(MI_VIDEO_WELCOME)) { ?>
	  					<p>
		  					<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="338" height="226" title="Sale introduction video">
			  					<param name="movie" value="<?php echo MI_VIDEO_WELCOME ?>">
			  					<param name="quality" value="high"><param name="LOOP" value="false">
			  					<embed src="<?php echo MI_VIDEO_WELCOME ?>" width="420" height="240" loop="false" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>
		  					</object>
	  					</p>
				<?php } ?>
							<table id="table143" cellSpacing="0" cellPadding="0" width="100%" border="0">
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
									<td align="left" width="283" colSpan="2" bgcolor="#100973">
									<p class="MsoNormal" style="line-height: 150%">
									<b>
									<span style="font-size: 10pt; color: #FFFFFF; line-height: 150%; font-family: Webdings">
									=</span><font color="#FFFFFF"><span style="font-size: 10pt; line-height: 150%; font-family: Arial"> </span>
									</font><span style="font-size: 10pt; color: windowtext; line-height: 150%; font-family: Arial">
									<a style="color: #FFFF00; text-decoration: underline" href="validate.php">
									In and Out in Twenty Minutes</a></span></b></td>
								</tr>
								<tr>
									<td width="63" height="27" bgcolor="#100973">&nbsp;</td>
									<td align="left" width="283" colSpan="2" height="27" bgcolor="#100973">
									<b>
									<span style="font-size: 10pt; color: #FFFFFF; line-height: 150%; font-family: Webdings">
									=</span><font color="#FFFFFF"><span style="font-size: 10pt; line-height: 150%; font-family: Arial"> </span>
									</font><span style="font-size: 10pt; color: windowtext; line-height: 150%; font-family: Arial">
									<a style="color: #FFFF00; text-decoration: underline" href="validate.php">
									Over <?php echo MI_DEALER_INV_COUNT; ?> Vehicles to Liquidate</a></span><font color="#FFFFFF"><span style="font-size: 10pt; line-height: 150%; font-family: Arial"> </span>
									</font>
									</b></td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
									<td align="left" width="283" colSpan="2" bgcolor="#100973"><b>
									<span style="font-size: 10pt; color: #FFFFFF; line-height: 150%; font-family: Webdings">
									=</span><font color="#FFFFFF"><span style="font-size: 10pt; line-height: 150%; font-family: Arial"> </span>
									</font><span style="font-size: 10pt; color: windowtext; line-height: 150%; font-family: Arial">
									<a style="color: #FFFF00; text-decoration: underline" href="validate.php">
									No Offer Will Be Rejected</a></span></b></td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
									<td width="283" colSpan="2" bgcolor="#100973">&nbsp;</td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
									<td width="189" rowSpan="5" bgcolor="#100973">
									<p class="MsoNormal" align="center">
									<b><span style="font-family: Arial">
									<font size="4" color="#FFFFFF">No Hassles</font></span></b></p>
									<p class="MsoNormal" align="center">
									<b><span style="font-family: Arial">
									<font size="4" color="#FFFFFF">No Gimmicks</font></span></b></p>
									<p align="center"><b>
									<span style="font-family: Arial">
									<font size="4" color="#FFFFFF">No Waiting</font></span></b></td>
									<td width="92" rowSpan="5" bgcolor="#100973">
									<p align="center">
									<img border="0" src="images/tv.gif" width="98" height="81"></td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
									<td width="283" colSpan="2" bgcolor="#100973">&nbsp;</td>
								</tr>
								<tr>
									<td align="middle" width="63" bgcolor="#100973">&nbsp;</td>
									<td align="middle" width="283" colSpan="2" bgcolor="#100973">
									<p align="justify">
									<font face="Arial" size="2" color="#FFFFFF">With competition 
									heating up and prices dropping, there has 
									never been a better time to buy a used car - 
									especially through a re-marketing group. 
									Autos lose an uneven percentage of their 
									market value in their first years on the 
									road. Buying almost any late-model used car 
									in good condition is most always, a smart 
									move.</font></p>
									<p align="justify">
									<span style="font-size: 9pt; color: #FFFFFF; font-family: Arial">
									Vehicles are in excellent running condition 
									with current safety certificates and 
									original factory warranties reapplied.&nbsp;
									</span></td>
								</tr>
								<tr>
									<td width="63" bgcolor="#100973">&nbsp;</td>
									<td width="283" colSpan="2" bgcolor="#100973">&nbsp;</td>
								</tr>
							</table>
						</div>
						</td>
					</tr>
					<tr>
						<td width="12">&nbsp;</td>
						<td align="middle" colSpan="4">
						<p class="MsoNormal" align="left"><font color="#FFFFFF"><b>
						<span style="font-family: Arial">Willing to take a loss
						<?php echo MI_SALE_DATES ?>
						</span></b></font></td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" height="128" bgcolor="#100973">&nbsp;</td>
						<td width="173" height="128" bgcolor="#100973">
						<p class="MsoNormal" align="center">
						<font color="#FFFFFF">
						<img height="116" src="images/new_pa1.jpg" width="154" align="left" border="0"></font></td>
						<td width="4" height="128" bgcolor="#100973">&nbsp;</td>
						<td width="221" height="128" bgcolor="#100973">
						<p align="justify">
						<span style="font-size: 9pt; font-family: Arial">
						<font color="#FFFFFF">Remember: It’s not how much they are trying to make 
						rather how much are they willing to lose before all 
						vehicles go to auction.
						</font>
						<a style="color: #FFFF00; text-decoration: underline" href="validate.php">
						All vehicles will go to auction</a><font color="#FFFFFF"> Saturday night. 
						Arrive to the dealership early for best selection </font> </span>
						</td>
						<td width="5" height="128">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" height="20" bgcolor="#100973">&nbsp;</td>
						<td width="173" height="20" bgcolor="#100973">&nbsp;</td>
						<td width="4" height="20" bgcolor="#100973">&nbsp;</td>
						<td width="221" height="20" bgcolor="#100973">&nbsp;</td>
						<td width="5" height="20">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="173" bgcolor="#100973">
						<font face="Arial" color="#FFFFFF"><b>Lenders Onsite</b></font></td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="173" bgcolor="#100973">
						<p align="justify">
						<span style="font-size: 9pt; font-family: Arial">
						<font color="#FFFFFF">A.I.G. Inc has made arrangements with National Lenders 
						for this event. Even most buyers with less than perfect 
						credit will be able to obtain On-The-Spot credit 
						approval. </font></span>
						<span style="font-size: 9pt; color: windowtext; font-family: Arial">
						<a style="color: #FFFF00; text-decoration: underline" href="easy-loan-application.php">
						Get Approved Now</a></span></td>
						<td width="4" bgcolor="#100973">&nbsp;</td>
						<td width="221" bgcolor="#100973">
						<p align="center">
						<font color="#FFFFFF">
						<img height="125" src="images/new_pa5.jpg" width="167" border="0"></font></td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="173" bgcolor="#100973">&nbsp;</td>
						<td width="4" bgcolor="#100973">&nbsp;</td>
						<td width="221" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td colSpan="3" bgcolor="#100973"><b>
						<font face="Arial" color="#FFFFFF">
						Warranties Reapplied</font></b></td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td vAlign="top" colSpan="3" rowSpan="16" bgcolor="#100973">
						<p align="left">
						<font face="Arial" size="2" color="#FFFFFF">Vehicles are 
						in excellent running condition with current safety 
						certificates and original factory warranties reapplied. 
						Clear titles - No Flood or Salvaged Titles </font></p>
						<p align="center">
						<font color="#FFFFFF">
						<img height="209" src="images/new_pa3.jpg" width="353" align="left" border="0"></font></td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" height="20" bgcolor="#100973">&nbsp;</td>
						<td width="5" height="20">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
					<tr>
						<td width="12" bgcolor="#100973">&nbsp;</td>
						<td width="5">&nbsp;</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</table>
<div class="small" id="footer-index">
	<p align="center"><font face="Arial"><font color="#ffffff" size="2">
	<?php echo MI_COPYRIGHT . MI_OWNER_NAME; ?>. All Rights Reserved<b>. </b></font><b>
	<a target="_blank" href="privacy.php"><font size="2">Privacy Policy</font></a></b></font></p>
</body>
</html>