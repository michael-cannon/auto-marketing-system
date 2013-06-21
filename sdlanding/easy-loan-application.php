<?php include_once( 'config.php' ); ?>
<html>
<head>
	<title><?php echo MI_DEALER_NAME_LOC ?> - <?php echo MI_SALE_TEXT ?> Easy Loan Application</title>
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
	  <h1><?php echo MI_SALE_TEXT ?> Easy Loan Application</h1>
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
	  <form action="includes/process.php" method="post" enctype="multipart/form-data" tmt:validate="true" tmt:callback="displayError">
		<table class="register">
			<tr>
			<td colspan="2" align="center">
			<h4>Complete Your <?php echo MI_SALE_TEXT ?> Easy Loan Application Below</h4>
			<div id="errorDisplay"><span></span></div>
			</td>
			<td style="width: 150px; vertical-align: top; text-align: left;" rowspan="11">
				<p>Fill out our fast and easy application and we'll find you the
				best auto loan at the lowest possible rate at this <?php echo MI_SALE_TEXT ?>.</p>
				<p>Have bad credit? Don't worry, we can help you find an auto loan
				that's right for you! Let us will help you re-establish ask for our
				<b>FREE Credit Repair Kit</b>.</p>
				<p style="text-align: center;"><img border="0" src="images/Secure&#32;Site.gif" width="88" height="50" /></p>
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
					<label>Home Phone</label>
				</td>
				<td>
					<input name="Home_Phone" type="text" size="13" maxlength="12" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="Home phone as required" tmt:pattern="usPhone" />
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
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="right">
					<label>Date of Birth</label>
				</td>
				<td>
					<input name="Birth_Month" type="text" size="2" maxlength="2"
					class="required" tmt:required="true"
					tmt:errorclass="invalid" tmt:message="Birth month required" tmt:minlength="1" tmt:maxlength="2" tmt:pattern="integer" />
					/
					<input name="Birth_Day" type="text" size="2" maxlength="2"
					class="required" tmt:required="true"
					tmt:errorclass="invalid" tmt:message="Birth day required" tmt:minlength="1" tmt:maxlength="2" tmt:pattern="integer" />
					/
					<input name="Birth_Year" type="text" size="4" maxlength="4"
					class="required" tmt:required="true"
					tmt:errorclass="invalid" tmt:message="Birth year required" tmt:minlength="4" tmt:maxlength="4" tmt:pattern="integer" />
					<br />
					<span class="small">
						September 1, 1972 would be 9/1/1972
						<br />
						Minimum age is 18 years
					</span>
				</td>
			</tr>
			<tr>
				<td class="right">
					<label>Gross Monthly Income</label>
				</td>
				<td>
					$<input name="Monthly_Gross_Income" type="text" size="10"
					maxlength="10"
					class="required" tmt:required="true" tmt:errorclass="invalid"
					tmt:message="Gross monthly income required"
					tmt:pattern="money" />
					<br />
					<span class="small">
						Gross income is income before taxes and bills
						<br />
						Minimum gross monthly income is $ 1,200
					</span>
				</td>
			</tr>
			<tr>
				<td class="right">
					<label>Other Monthly Income</label>
				</td>
				<td>
					$<input name="Monthly_Other_Income" type="text" size="10"
					maxlength="10"
					tmt:errorclass="invalid"
					tmt:message="Other monthly income required"
					tmt:pattern="money" />
				</td>
			</tr>
			<tr>
				<td class="right">
					<label>Social Security Number</label>
				</td>
				<td>
					<input name="Social_Security_Number" type="text" size="11" maxlength="11" class="required" tmt:required="true" tmt:errorclass="invalid" tmt:message="Social security number required" tmt:pattern="ssn" />
					<br />
					<span class="small">
						Please format as NNN-NN-NNNN
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
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
				<label>Years at Address</label>
			</td>
				<td>
				<input name="AddressYears" type="text" size="4" maxlength="4"
				class="required" tmt:required="true" tmt:errorclass="invalid"
				tmt:message="Number of years at address required"
				tmt:minlength="1" tmt:maxlength="4" tmt:pattern="number" />
				</td>
			</tr>
			<tr>
				<td class="right">
						Own or Rent Home?
				</td>
				<td>
					<label>
						<input name="AddressRentOwn" type="radio" value="Own" tmt:required="true" tmt:message="Choose own, rent, or other" />
						Own</label>
					<label>
						<input name="AddressRentOwn" type="radio" value="Rent" />
						Rent</label>
					<label>
						<input name="AddressRentOwn" type="radio" value="Other" />
						Other</label>
		 			<span class="required">&nbsp;&nbsp;&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="right">
					Have Trade In?
				</td>
				<td>
					<label>
						<input name="HaveTradeIn" type="radio" value="Yes"
						tmt:required="true" tmt:message="Choose trade-in yes or no" />
						Yes</label>
					<label>
						<input name="HaveTradeIn" type="radio" value="No" />
						No</label>
		 			<span class="required">&nbsp;&nbsp;&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td class="right">
					<label>Loan Down Payment</label>
				</td>
				<td>
					$<input name="LoanDownPayment" type="text" size="10"
					maxlength="10"
					value="0"
					class="required" tmt:required="true" tmt:errorclass="invalid"
					tmt:message="Loan down payment required"
					tmt:pattern="money" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td class="right">
					<label>Privacy Notice</label>
				</td>
				<td>
                	<textarea name="Privacy" rows="7" cols="40" class="small">I hereby authorize the dealership to investigate my credit and employment history, and the release of that information to help attain financing. 

In connection with your transaction we may obtain nonpublic person information about you and that information is handled as stated in this notice. This does not apply to information obtained in a non-financial transaction. 

We collect personal nonpublic information about you from the following sources. 

Information we receive from you on applications or other forms in connection with a financial transaction. 

Information about your transaction with us, our affiliates or others; and, Information we receive from a consumer reporting agency.
  
We may disclose all of the information we collect, as described above, to companies that perform marketing services or other functions on our behalf or to other financial institutions with whom we have joint marketing agreements.
  
We may also disclose nonpublic personal information about you, as a consumer, customer or former customer, to non-affiliated third parties as permitted by law.
  
Further, we restrict access to your nonpublic personal information to only those employees who need to know that information to provide products or services to you. Employees cannot use your information for any other purpose. For your protection, we maintain physical, electronic and procedural safeguards that comply with federal regulations to further guard your nonpublic information.
  
I hereby authorize my insurance carrier to provide my non-public personal insurance information to the above named dealership solely for the purpose of fulfilling and completing this transaction. The dealership named above is authorized to provide a copy of this document to obtain such information.

I certify I have attained the age of majority.</textarea>
				</td>
			</tr>
			<tr>
				<td class="right">
					&nbsp;
				</td>
				<td>
	 				<label>
						<input name="Privacy_Notice" type="checkbox" value="Agreed"
						tmt:minchecked="1" tmt:maxchecked="1"
						tmt:message="Please agree to the Privacy Notice" />
						I agree with the Privacy Notice
						</label>
		 				<span class="required">&nbsp;&nbsp;&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td class="right">
					<label>Signature</label>	
				</td>
				<td>
					<input name="Electronic_Signature" type="text"
					class="required" tmt:required="true"
					tmt:errorclass="invalid" tmt:message="Electronic signature required" />
					<br />
					<span class="small">
						Insert your full name to act as an electronic signature
					</span>
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
			</tr>
			</table>
		</form>
</div>
<?php include_once( 'footer.php' ); ?>