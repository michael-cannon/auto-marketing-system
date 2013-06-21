<?// admin_registration_configuration_class.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Registration_configuration extends Admin_site {

	var $internal_error_message = "There was an internal error";
	var $data_error_message = "Not enough data to complete request";
	var $page_text_error_message = "No text connected to this page";
	var $no_pages_message = "No pages to list";

	var $registration_configuration_message;

	var $debug_registration_configuration = 0;

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function Registration_configuration($db, $product_configuration=0)
	{
		$this->Admin_site($db, $product_configuration);
	}

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_registration_configuration_form($db)
	{
		$this->body .= "<SCRIPT language=\"JavaScript1.2\">";
		// Set title and text for tooltip
		$this->body .= "
			Text[1] = [\"use field\", \"Checking this checkbox will display this field during the registration process.\"]\n
			Text[2] = [\"require field\", \"Checking this checkbox will require this field within the registration process. The fields that have this choice are required if used\"]\n
			Text[3] = [\"field length\", \"This value will determine maximum amount of characters or numbers a user can place into this field during registration. The maximum number of characters that can be placed in any field is 100.\"]\n
			Text[4] = [\"email 2\", \"Choose whether to use and/require a 2nd email.  NOTE:The primary email is always a required field.\"]\n
			Text[5] = [\"email address of admin\", \"This is the email address that will receive registration confirmation and success message sent to admin (if you choose to receive them by choosing so below).\"]\n
			Text[6] = [\"url of register.php file\", \"The file \\\"register.php\\\" can be placed where you like or completely eliminated if you want.\"]\n
			Text[7] = [\"secure ssl url to the register.php file\", \"Entering a secure URL into this field will allow your registrants to register on a secure page. This requires a security certificate to be installed on your server. Contact your host for information on security certificates.\"]\n
			Text[8] = [\"use ssl connection for the registration process\", \"If you want to secure the registration process with a security certificate that is already installed on your server, please check here.\"]\n
			Text[9] = [\"send a register complete email to admin\", \"An email with user information will be sent to the admin email listed above whenever a user completes the registration process.\"]\n
			Text[10] = [\"send a register attempt email to admin\", \"An email with username,password and email will be sent to the admin email listed above whenever a user is sent the confirmation email to confirm their email address.\"]\n
			Text[11] = [\"send registration complete email to registrant\", \"An email will be sent to the registrant when they complete the registration process welcoming them to your site.\"]\n
			Text[12] = [\"use email verification system\", \"If you check yes an email will be sent to the registrants email address with a link back to the site once they have entered all of their registration information. They must then click the link within that email which brings them back to the site. If all information was returned correctly the registration is completed.\"]\n
			Text[13] = [\"admin approves all registrations\", \"If yes then the admin will appove all registrations before they become active. Also note that you will need to edit the text displayed on the \\\"Registration Success Page\\\" stating that their registration will require admin approval.\"]\n
			Text[14] = [\"secret hash word\", \"This is a string of characters used to generate the keys that registrants use to confirm their registration (if you use the email verification described in the first step). There is typically no need to change this unless you suspect registration manipulation by an automatic registration script of some kind.\"]\n

			Text[15] = [\"other box\", \"Checking the \\\"other box\\\" field will display an additional text field next to the this optional field for the user to enter their information.\"]\n
			Text[16] = [\"dependent\", \"Checking this box will set this field to \\\"required\\\" if the user has chosen the business or company field earlier in the registration process.\"]\n
			Text[17] = [\"type\", \"Choose the type of entry field you want the user to see when they enter their information.\"]\n
			Text[18] = [\"registration optional field admin name\", \"Keep track of your Registration Optional Fields by giving them a name you choose.  This name will be visible throughout your admin, wherever the field is used.\"]\n
			";

		//".$this->show_tooltip(15,1)."

		// Set style for tooltip
		//echo "Style[0] = [\"white\",\"\",\"\",\"\",\"\",,\"black\",\"#ffffcc\",\"\",\"\",\"\",,,,2,\"#b22222\",2,24,0.5,0,2,\"gray\",,2,,13]\n";
		$this->body .= "Style[1]=[\"white\",\"#000099\",\"\",\"\",\"\",,\"black\",\"#e8e8ff\",\"\",\"\",\"\",,,,2,\"#000099\",2,,,,,\"\",3,,,]\n";
		$this->body .= "var TipId = \"tiplayer\"\n";
		$this->body .= "var FiltersEnabled = 1\n";
		$this->body .= "mig_clay()\n";
		$this->body .= "</script>";

		$sql_query = "select * from ".$this->site_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$show_configuration = $result->FetchRow();
		}

		$sql_query = "select * from ".$this->registration_configuration_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		elseif ($result->RecordCount() == 1)
		{
			$registration_configuration = $result->FetchRow();

			$this->title = "Registration Setup > General Settings";
			$this->description = "Use this page to specify which fields you want registrants to see during the registration process.
			The settings below will be applied on a site-wide basis.";

			$this->body .= "
				<script type=\"text/javascript\">
					function validate(field,max)
					{
						max=(max)?max:100;
						if (!(field.value>=0 && field.value<=max))
						{
							alert('Must be between 0 and '+max+'. Values outside this range as well as invalid characters will not be submitted.');
							field.value=\"\";
							field.focus();
						}
					}
					function check_all(elements,col)
					{
						for(x = 0; x < elements.length; x++)
						{
							if(elements[x].id == col && !elements[x].disabled)
								elements[x].checked=elements[col+'_all'].checked;
							if(elements[x].id == col+'_section' && !elements[x].disabled)
								elements[x].checked=elements[col+'_all'].checked;
						}
					}
				</script>";
				if (!$this->admin_demo())$this->body .= "<form name=fields_to_use action=index.php?a=26&z=1 method=post>";
				$this->body .= "<table cellpadding=0 cellspacing=0 border=0 align=center class=row_color1>";
			if ($this->registration_configuration_message)
				$this->body .= "
					<tr>
						<td colspan=2 class=medium_error_font>".$this->registration_configuration_message."</td>
					</tr>";

			// Block of checkboxes for major settings
			$this->body .= "
					<tr>
						<td colspan=2><table width=100% cellpadding=5 cellspacing=1>
							<tr bgcolor=000066>
								<td colspan=100% class=large_font_light>
									<b>Standard Fields to use during Registration Process</b>
								</td>
							</tr>
							<tr bgcolor=000066>
								<td colspan=100% class=large_font_light>
									<span class=medium_font_light>Below is a list of pre-built fields you can use
									during the registration process.  You can choose which fields will be required for every registrant
									during registration.\n\t</span>
								</td>
							</tr>
							<tr class=row_color_black>
								<td align=center class=medium_font_light><b>registration field</b></td>
								<td align=center class=medium_font_light><b>use</b>&nbsp;".$this->show_tooltip(1,1)."</td>
								<td align=center class=medium_font_light><b>require</b>&nbsp;".$this->show_tooltip(2,1)."</td>
								<td align=center class=medium_font_light><b>length</b>&nbsp;".$this->show_tooltip(3,1)."</td>
							</tr>";
			$this->row_count=0;

			//First Name Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>First Name</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_firstname_field] value=1 "
									.(($registration_configuration['use_registration_firstname_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_firstname_field] value=1 "
									.(($registration_configuration['require_registration_firstname_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[firstname_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['firstname_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Last Name Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Last Name</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_lastname_field] value=1 "
									.(($registration_configuration['use_registration_lastname_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_lastname_field] value=1 "
									.(($registration_configuration['require_registration_lastname_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[lastname_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['lastname_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Company Name Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Company Name</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_company_name_field] value=1 "
									.(($registration_configuration['use_registration_company_name_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_company_name_field] value=1 "
									.(($registration_configuration['require_registration_company_name_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[company_name_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['company_name_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Business Type Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Business Type</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_business_type_field] value=1 "
									.(($registration_configuration['use_registration_business_type_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_business_type_field] value=1 "
									.(($registration_configuration['require_registration_business_type_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			//Address 1 Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Address 1</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_address_field] value=1 "
									.(($registration_configuration['use_registration_address_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_address_field] value=1 "
									.(($registration_configuration['require_registration_address_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[address_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['address_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Address 2 Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Address 2</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_address2_field] value=1 "
									.(($registration_configuration['use_registration_address2_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_address2_field] value=1 "
									.(($registration_configuration['require_registration_address2_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[address_2_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['address_2_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Phone 1 Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Phone 1</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_phone_field] value=1 "
									.(($registration_configuration['use_registration_phone_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_phone_field] value=1 "
									.(($registration_configuration['require_registration_phone_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[phone_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['phone_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Phone 2 Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Phone 2</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_phone2_field] value=1 "
									.(($registration_configuration['use_registration_phone2_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_phone2_field] value=1 "
									.(($registration_configuration['require_registration_phone2_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[phone_2_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['phone_2_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Fax Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Fax</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_fax_field] value=1 "
									.(($registration_configuration['use_registration_fax_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_fax_field] value=1 "
									.(($registration_configuration['require_registration_fax_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[fax_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['fax_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//City Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>City</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_city_field] value=1 "
									.(($registration_configuration['use_registration_city_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_city_field] value=1 "
									.(($registration_configuration['require_registration_city_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[city_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['city_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//State Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>State</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_state_field] value=1 "
									.(($registration_configuration['use_registration_state_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_state_field] value=1 "
									.(($registration_configuration['require_registration_state_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			//Country Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Country</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_country_field] value=1 "
									.(($registration_configuration['use_registration_country_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_country_field] value=1 "
									.(($registration_configuration['require_registration_country_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			//Zip Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Zip</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_zip_field] value=1 "
									.(($registration_configuration['use_registration_zip_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_zip_field] value=1 "
									.(($registration_configuration['require_registration_zip_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[zip_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['zip_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//URL Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>URL</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_url_field] value=1 "
									.(($registration_configuration['use_registration_url_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_url_field] value=1 "
									.(($registration_configuration['require_registration_url_field']==1) ? "checked" : "").">
								</td>
								<td align=center valign=top>
									<input onkeyup=validate(this) type=text name=b[url_maxlength] size=3 maxsize=3 value="
									.$registration_configuration['url_maxlength'].">
								</td>
							</tr>";$this->row_count++;

			//Email 2 Field
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Email 2</b>&nbsp;".$this->show_tooltip(4,1)."</td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_registration_email2_field] value=1 "
									.(($registration_configuration['use_registration_email2_field']==1) ? "checked" : "").">
								</td>
								<td valign=top align=center class=medium_font>
									<input id=require type=checkbox name=b[require_registration_email2_field] value=1 "
									.(($registration_configuration['require_registration_email2_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			//Accept User Agreement
			$this->body .= "<tr class=".$this->get_row_color().">
								<td align=center valign=top class=medium_font><b>Accept User Agreement</b></td>
								<td align=center valign=top class=medium_font>
									<input id=use type=checkbox name=b[use_user_agreement_field] value=1 "
									.(($registration_configuration['use_user_agreement_field']==1) ? "checked" : "").">
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>";$this->row_count++;

			$this->body .= "<tr class=row_color_black>
								<td align=right class=medium_font_light><b>select all:&nbsp;&nbsp;</b></td>
								<td align=center>
									<input id=use_all onclick=\"javascript:check_all(document.fields_to_use,'use');\" type=checkbox>
								</td>
								<td align=center>
									<input id=require_all onclick=\"javascript:check_all(document.fields_to_use,'require');\" type=checkbox>
								</td>
								<td align=center>
									<input type=\"button\" onclick=\"reset()\" value=\"reset form\">
								</td>
							</tr>
						</table><br>
						<tr bgcolor=000066>
							<td colspan=2 class=large_font_light align=center><b>Email and Security Settings</b></td>
						</tr>";
			$this->row_count=0;

			//admin email address
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>email address of admin:</b>&nbsp;".$this->show_tooltip(5,1)."</td>
							<td valign=top class=medium_font>
								&nbsp;<input type=text name=b[registration_admin_email] size=60 value="
								.$show_configuration["registration_admin_email"].">
							</td>
						</tr>";$this->row_count++;

			//url of register.php
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>url of register.php file:</b>&nbsp;".$this->show_tooltip(6,1)."</td>
							<td valign=top class=medium_font>
								&nbsp;<input type=text name=b[registration_url] size=60 value=".$show_configuration["registration_url"].">
							</td>
						</tr>";$this->row_count++;

			//secure ssl url
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>secure ssl url to the register.php file</b>&nbsp;".$this->show_tooltip(7,1)."<br>
								<span class=small_font>
									enter the ssl url to link directly to register.php (https://www.somesite.com/register.php)
								</span>
							</td>
							<td valign=top class=medium_font>
								&nbsp;<input type=text name=b[registration_ssl_url] size=60 maxsize=100 value=\""
								.$show_configuration["registration_ssl_url"]."\">
							</td>
						</tr>";$this->row_count++;

			//use SSL in registration
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>use ssl connection for the registration process</b>&nbsp;".$this->show_tooltip(8,1)."<br>
								<span class=small_font>
									Running your clients registration information through an ssl (secure connection) will protect their
									information.  An ssl connection is required through your site.
								</span>
							</td>
							<td valign=top class=medium_font>
								<input type=radio name=b[use_ssl_in_registration] value=1 "
								.(($show_configuration["use_ssl_in_registration"] == 1)?"checked":"")."> yes<br>
								<input type=radio name=b[use_ssl_in_registration] value=0 "
								.(($show_configuration["use_ssl_in_registration"] == 0)?"checked":"")."> no
							</td>
						</tr>";$this->row_count++;

			//send_register_complete_email_admin
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>send a register complete email to admin:</b>&nbsp;".$this->show_tooltip(9,1)."
							</td>
							<td valign=top class=medium_font>
								<input type=radio name=b[send_register_complete_email_admin] value=1 "
								.(($show_configuration["send_register_complete_email_admin"] == 1)?"checked":"")."> yes<br>
								<input type=radio name=b[send_register_complete_email_admin] value=0 "
								.(($show_configuration["send_register_complete_email_admin"] == 0)?"checked":"")."> no
							</td>
						</tr>";$this->row_count++;

			//send email to admin when someone attempts to register
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>send a register attempt email to admin:</b>&nbsp;".$this->show_tooltip(10,1)."
							</td>
							<td valign=top class=medium_font>
								<input type=radio name=b[send_register_attempt_email_admin] value=1 "
								.(($show_configuration["send_register_attempt_email_admin"] == 1)?"checked":"")."> yes<br>
								<input type=radio name=b[send_register_attempt_email_admin] value=0 "
								.(($show_configuration["send_register_attempt_email_admin"] == 0)?"checked":"")."> no
							</td>
						</tr>";$this->row_count++;

			//send email to client once they complete registration as verification
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>send registration complete email to registrant:</b>&nbsp;".$this->show_tooltip(11,1)."
							</td>
							<td valign=top class=medium_font>
								<input type=radio name=b[send_register_complete_email_client] value=1 "
								.(($show_configuration["send_register_complete_email_client"] == 1)?"checked":"")."> yes<br>
								<input type=radio name=b[send_register_complete_email_client] value=0 "
								.(($show_configuration["send_register_complete_email_client"] == 0)?"checked":"")."> no
							</td>
						</tr>";$this->row_count++;

			//use email to verify the existence of the users email address
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>use email verification system:</b>&nbsp;".$this->show_tooltip(12,1)."
							</td>
							<td valign=top class=medium_font>
								<input type=radio name=b[use_email_verification_at_registration] value=1 "
								.(($show_configuration["use_email_verification_at_registration"] == 1)?"checked":"")."> yes<br>
								<input type=radio name=b[use_email_verification_at_registration] value=0 "
								.(($show_configuration["use_email_verification_at_registration"] == 0)?"checked":"")."> no
							</td>
						</tr>";$this->row_count++;

			//Admin approves all registrations
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>Admin approves all registrations:</b>&nbsp;".$this->show_tooltip(13,1)."
							</td>
							<td valign=top class=medium_font>
								<input type=radio name=b[admin_approves_all_registration] value=1 "
								.(($show_configuration["admin_approves_all_registration"] == 1)?"checked":"")."> yes<br>
								<input type=radio name=b[admin_approves_all_registration] value=0 "
								.(($show_configuration["admin_approves_all_registration"] == 0)?"checked":"")."> no
							</td>
						</tr>";$this->row_count++;

			//Secret Hash Word
			$this->body .= "
						<tr class=".$this->get_row_color().">
							<td align=right width=50% class=medium_font>
								<b>secret hash word:</b>&nbsp;".$this->show_tooltip(14,1)."
							</td>
							<td valign=top class=medium_font>
								&nbsp;<input type=text name=b[secret_for_hash] value=\"".$show_configuration["secret_for_hash"]."\">
							</td>
						</tr>";$this->row_count++;

			$this->body .= "
						<tr>
							<td colspan=2 height=20></td>
						</tr>
						<tr>
							<td colspan=2>
							     <table cellpadding=5 cellspacing=1 border=0>


							<tr bgcolor=000066>
								<td colspan=100% class=large_font_light>
									<b>Optional Fields to use during Registration Process</b>
								</td>
							</tr>
							<tr>
								<td colspan=100% class=medium_font>
									Registration Optional Fields can be displayed as additional fields to help you collect additional information
									during the Registration Process . If using a Registration Optional Field, you will need to edit the text
									associated with that field on every page that uses the field.  For instance, if you \"use\" Reg Optional Field 1
									(or name of your choosing) you will need to access the appropriate page to change the text for this field.  For a
									complete list of pages have these fields attached, please see the User Manual.<br><br>
									The column and field names can be set up in the text administration of each individual page where they appear.
									Each field is prebuilt to handle up to 100 characters of information.<br><br>
									<b>IMPORTANT: The name of each \"registration optional field admin name\" is only a tool for you to keep track of
									how you	are using the field.  This name will ONLY be visible in your admin.  To change the name of the field
									<i>actually</i> being used,  you must go to the particular page in \"Pages Management\" where the field is used
									and edit it there.</b>
								</td>
							</tr>

							     </table>
							</td>
						</tr>
						<tr>
							<td colspan=2>
								<table width=100% cellspacing=1>
									<tr class=row_color_black>
										<td align=center class=medium_font_light><b>registration optional field admin name (#)</b>".$this->show_tooltip(18,1)."</td>
										<td align=center class=medium_font_light><b>use</b>".$this->show_tooltip(1,1)."</td>
										<td align=center class=medium_font_light><b>require</b>".$this->show_tooltip(2,1)."</td>
										<td align=center class=medium_font_light><b>other box</b>".$this->show_tooltip(15,1)."</td>
										<td align=center class=medium_font_light><b>dependent</b>".$this->show_tooltip(16,1)."</td>
										<td align=center class=medium_font_light><b>length</b>".$this->show_tooltip(3,1)."</td>
										<td align=center class=medium_font_light><b>type</b>".$this->show_tooltip(17,1)."</td>
									</tr>";

			$this->row_count=0;
			//Optional Fields
			for($i=1;$i<11;$i++)
			{
				$this->body .= "	<tr class=".$this->get_row_color().">
										<td valign=top align=left class=medium_font>
											<input type=text size=30 name=b[registration_optional_".$i."_field_name] value=\"".
											$registration_configuration['registration_optional_'.$i.'_field_name']."\">($i)
										</td>
										<td valign=top align=center class=medium_font>
											<input id=optional_use type=checkbox name=b[use_registration_optional_".$i."_field] value=1 ".
											(($registration_configuration["use_registration_optional_".$i."_field"] == 1) ? "checked" : "").">
										</td>
										<td align=center valign=top class=medium_font>
											<input id=optional_require type=checkbox name=b[require_registration_optional_".$i."_field] value=1 ".
											 (($registration_configuration["require_registration_optional_".$i."_field"] == 1) ? "checked" : "").">
										</td>
										<td align=center valign=top class=medium_font>
											<input id=optional_other_box type=checkbox name=b[registration_optional_".$i."_other_box] value=1 ".
											(($registration_configuration["registration_optional_".$i."_other_box"] == 1) ? "checked" : "").">
										</td>
										<td valign=top align=center class=medium_font>
											<input id=optional_dependent type=checkbox name=b[require_registration_optional_".$i."_field_dep] value=1 ".
											(($registration_configuration["require_registration_optional_".$i."_field_dep"] == 1) ? "checked" : "").">
										</td>
										<td align=center valign=top class=small_font>
											<input onkeyup=validate(this) type=text name=b[optional_".$i."_maxlength] size=3 maxsize=3 value="
											.$registration_configuration['optional_'.$i.'_maxlength'].">
										</td>
										<td align=center valign=top class=small_font>
											<select name=b[registration_optional_".$i."_field_type]>
												<option value=0 ".(($registration_configuration["registration_optional_".$i."_field_type"] == 0) ? "selected" : "").">
													blank text box
												</option>";
				$this->sql_query = "select * from ".$this->registration_choices_types_table;
				$types_result = $db->Execute($this->sql_query);
				if (!$types_result)
				{
					$this->error_message = $this->messages[5501];
					$this->site_error($db->ErrorMsg());
					return false;
				}
				elseif ($types_result->RecordCount() > 0)
				{
					while ($show_type = $types_result->FetchRow())
					{
						//show questions as drop down box
						$this->body .= "		<option value=".$show_type['type_id'].
													(($show_type['type_id'] == $registration_configuration['registration_optional_'.$i.'_field_type']) ? " selected" : "").">"
													.$show_type['type_name']."
												</option>";
					} //end of while
				}
				$this->body .= "			</select>
										</td>
									</tr>";$this->row_count++;
			}

			$this->body .= "<tr class=row_color_black>
								<td align=right class=medium_font_light><b>select all:&nbsp;&nbsp;</b></td>
								<td align=center>
									<input id=optional_use_all onclick=\"javascript:check_all(document.fields_to_use,'optional_use');\" type=checkbox>
								</td>
								<td align=center>
									<input id=optional_require_all onclick=\"javascript:check_all(document.fields_to_use,'optional_require');\" type=checkbox></td>
								<td align=center>
									<input id=optional_other_box_all onclick=\"javascript:check_all(document.fields_to_use,'optional_other_box');\" type=checkbox>
								</td>
								<td align=center>
									<input id=optional_dependent_all onclick=\"javascript:check_all(document.fields_to_use,'optional_dependent');\" type=checkbox>
								</td>
								<td>&nbsp;</td>
								<td align=center>
									<input onclick=\"reset()\" type=\"button\" value=\"reset form\">
								</td>
							</tr>";


			if (!$this->admin_demo())
			{
				$this->body .= "<tr>
								<td colspan=9 align=center class=medium_font>
									<input type=submit value=\"Save\" name=submit>
								</td>
							</tr>";
			}
			$this->body .= "		<tr>
								<td colspan=100% align=center>
									<a href=index.php?a=67>
										<span class=medium_font><br><br><b>View Registration Pre-Valued Dropdowns</b></span>
									</a>
								</td>
							</tr>
						</table>
					</form>
				</table>";

			return true;
		}
		else
		{
			//echo $sql_query." is the query<BR>\n";
			return false;
		}

	} //end of function display_registration_configuration_form
//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function registration_configuration_home()
	{
		$this->body .= "<table cellpadding=3 cellspacing=0 width=100% border=0 align=center class=row_color1>\n";
		$this->title = "Registration Configuration";
		$this->description = "In this section you will configure the necessary functional elements
			as well as the applicable registration settings of your site.";
		$this->body .= "<tr class=row_color2>\n\t\t<td align=right valign=top><a href=index.php?a=26&z=1><span class=medium_font><b>general</b></span></a>\n\t\t</td>\n\t\t
			<td class=medium_font>configure the settings for general settings for registration </a>\n\t\t</td></tr>\n\t";
		$this->body .= "<tr>\n\t\t<td align=right valign=top><a href=index.php?a=26&z=2><span class=medium_font><b>block email domains</b></span></a>\n\t\t</td>\n\t\t<td class=medium_font>blocks the email domains from which the user wants to stop registration </a>\n\t\t</td>\n\t</tr>\n\t";
		$this->body .= "<tr class=row_color2>\n\t\t<td align=right valign=top><a href=index.php?a=26&b=1&z=4><span class=medium_font><b>unapproved registrations</b></span></a>\n\t\t</td>\n\t\t<td class=medium_font>allows manual confirmation of a user's account </a>\n\t\t</td>\n\t</tr>\n\t";
		$this->body .= "</table>\n";
		return true;
	} //end of function registration_configuration_home

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_email_domains($db)
	{
		$sql_query = "select * from ".$this->block_email_domains;
		$type_result = $db->Execute($sql_query);
		if($this->configuration_data["debug_admin"])
		{
			$this->debug_display($db, $this->filename, $this->function_name, "block_email_domains", "get email domains from database");
		}
		if (!$type_result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=26&z=2 method=post>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color2  width=100%>\n";
		$this->title = "Registration Setup > Block Email Domains";
		$this->description = "Control the domains from which the user may register based upon the \"email address\" they enter.";

		$this->body .= "<tr class=row_color_black>\n\t<td class=medium_font_light>\n\t<b>Email Domain's Blocked</b>\n\t</td>\n\t";
		$this->body .= "<td class=medium_font_light>\n\t\n\t</td>\n\t";
		$this->body .= "</tr>\n";

		while ($show_types = $type_result->FetchRow())
		{
			$this->body .= "<tr class=".$this->get_row_color().">\n\t<td class=medium_font>\n\t".$show_types["domain"]." \n\t</td>\n\t";
			$this->body .= "<td width=100 align=center><a href=index.php?a=26&z=2&b=".$show_types["serial_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>";
			$this->body .= "</tr>\n";
		}
		if ($show_types > 0)
		{
			if (!$this->admin_demo())
				$this->body .= "<tr>\n\t<td align=center class=medium_font colspan=3>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
		}
		$this->body .= "<tr>\n\t<td colspan=2 align=center>\n\t
			<a href=index.php?a=26&z=3><span class=medium_font><b>add New Domain</b></span></a>\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function display_email_domains

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_email_domain($db,$serial_id=0)
	{
		if ($serial_id)
		{
			$this->function_name = "update_email_domain";

			//email domain to be deleted
			$sql_query = "delete from ".$this->block_email_domains." where
				serial_id = ".$serial_id."";
			$type_result = $db->Execute($sql_query);
			if($this->configuration_data["debug_admin"])
			{
				$this->debug_display($db, $this->filename, $this->function_name, "block_email_domains", "delete email domains");
			}
			if (!$type_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function update_email_domain

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function email_domains_form($db)
	{
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=26&z=3 method=post enctype=multipart/form-data>\n";
		$this->body .= "<input type=hidden name=MAX_FILE_SIZE value=10000000>\n";
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 align=center class=row_color2 width=100%>\n";
		$this->title = "Registration Setup > Block Email Domains > Add New Domain";
		$this->description = "Insert the email domain that needs to be blocked when the registrant uses that domain in the email field
			of the registration process.";

		$this->body .= "<tr class=row_color1>\n\t<td width=50% align=right class=medium_font> <b>Domain Name:</b> \n\t</td>\n\t";
		$this->body .= "<td width=50%>\n\t<input type=text name=b[domain_name] size=60> \n\t</td>\n\t";

		if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td align=center class=medium_font colspan=3>\n\t<input type=submit name=submit value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} // end of function email_domains_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_email_domain($db,$email_domain)
	{
		if ($email_domain)
		{
			$this->function_name = "insert_email_domain";
			$sql_query = "insert into ".$this->block_email_domains."(domain)
						  values ('".$email_domain[domain_name]."')";
			$type_result = $db->Execute($sql_query);
			if($this->configuration_data["debug_admin"])
			{
				$this->debug_display($db, $this->filename, $this->function_name, "block_email_domains", "inserting email domain into database");
			}
			if (!$type_result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} // end of function insert_email_domain


//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_registration_configuration($db,$config_info=0)
	{
		//highlight_string(print_r($config_info,1));
		if ($config_info)
		{
			$site_config_fields = array(
				"send_register_complete_email_admin",
				"send_register_complete_email_client",
				"send_register_attempt_email_admin",
				"use_email_verification_at_registration",
				"registration_admin_email",
				"registration_url",
				"use_ssl_in_registration",
				"registration_ssl_url",
				"admin_approves_all_registration",
				"secret_for_hash"
				);
			$this->sql_query = "update ".$this->site_configuration_table." set ";
			foreach ($site_config_fields as $value)
			{
				if ($value=="registration_admin_email" || $value=="registration_url" || $value=="registration_ssl_url")
					$this->sql_query .= $value." = \"".($config_info[$value] ? $config_info[$value] : "")."\", ";
				else
					$this->sql_query .= $value." = \"".($config_info[$value] ? $config_info[$value] : 0)."\", ";
			}
			$this->sql_query = substr($this->sql_query,0,-2);//strip off comma
			$result = $db->Execute($this->sql_query);
			if ($this->debug_registration_configuration)
				echo $this->sql_query."<bR>\n";
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			$reg_config_fields = array(
				"use_registration_company_name_field",
				"require_registration_company_name_field",
				"use_registration_firstname_field",
				"require_registration_firstname_field",
				"use_registration_lastname_field",
				"require_registration_lastname_field",
				"use_registration_email2_field",
				"require_registration_email2_field",
				"use_registration_phone_field",
				"require_registration_phone_field",
				"use_registration_phone2_field",
				"require_registration_phone2_field",
				"use_registration_fax_field",
				"require_registration_fax_field",
				"use_registration_url_field",
				"require_registration_url_field",
				"use_registration_city_field",
				"require_registration_city_field",
				"use_registration_state_field",
				"require_registration_state_field",
				"use_registration_zip_field",
				"require_registration_zip_field",
				"use_registration_country_field",
				"require_registration_country_field",
				"use_registration_address_field",
				"require_registration_address_field",
				"use_registration_address2_field",
				"require_registration_address2_field",
				"use_registration_business_type_field",
				"require_registration_business_type_field",
				"use_user_agreement_field",
				"firstname_maxlength",
				"lastname_maxlength",
				"company_name_maxlength",
				"address_maxlength",
				"address_2_maxlength",
				"phone_maxlength",
				"phone_2_maxlength",
				"fax_maxlength",
				"city_maxlength",
				"zip_maxlength",
				"url_maxlength"
				);
			for($i=1;$i<11;$i++)
			{
				array_push($reg_config_fields,"registration_optional_".$i."_field_name");
				array_push($reg_config_fields,"use_registration_optional_".$i."_field");
				array_push($reg_config_fields,"require_registration_optional_".$i."_field");
				array_push($reg_config_fields,"require_registration_optional_".$i."_field_dep");
				array_push($reg_config_fields,"registration_optional_".$i."_field_type");
				array_push($reg_config_fields,"registration_optional_".$i."_other_box");
				array_push($reg_config_fields,"optional_".$i."_maxlength");
			}
			$this->sql_query = "update ".$this->registration_configuration_table." set ";
			foreach ($reg_config_fields as $value)
				$this->sql_query .= $value." = \"".($config_info[$value] ? $config_info[$value] : 0)."\", ";
			$this->sql_query = rtrim($this->sql_query, ' ,');//strip off comma

			$result = $db->Execute($this->sql_query);
			if ($this->debug_registration_configuration)
				echo $this->sql_query."<bR>\n";
			if (!$result)
			{
				$this->site_error($db->ErrorMsg());
				return false;
			}
			return true;
		}

	} //end of function update_registration_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function show_all_dropdowns($db)
	{
		$this->sql_query = "select * from ".$this->registration_choices_types_table." order by type_name";
		$result = $db->Execute($this->sql_query);
		//echo $this->sql_query."<br>\n";
		if (!$result)
		{
			$this->error_message = $this->messages[5501];
			return false;
		}
		$this->title = "Registration Setup > General Settings > Registration Pre-Valued Dropdowns";
		$this->description = "This is the current list of Pre-Valued Dropdown choices
			that can be used by any customized registration question. <br><br>
			<b>Note:</b>  These are Registration Pre-Valued Dropdown choices that you attach to registration questions that are then displayed with the category
			they are attached to.  These dropdowns will show up as a choice in the \"choices\" category of the add or edit registration question form.
			So create your dropdowns here first then they will then become a choice to attach to a registration question
			<br><br>All Registration Pre-Valued Dropdowns are administered on this page.";
		$this->body .= "
			<table cellpadding=2 cellspacing=0 border=0 class=row_color1>";

		if ($result->RecordCount() > 0)
		{
			$this->body .= "
				<tr>
					<td>
						<table cellspacing=1 cellpadding=2 border=0 align=center>
							<tr bgcolor=000066>
								<td colspan=3 class=medium_font_light align=center><b>Current Pre-Valued Dropdowns</b></td>
							</tr>
							<tr class=row_color_black>
								<td class=medium_font_light><b>name</b></td>
								<td class=medium_font_light>&nbsp;</td>
								<td class=medium_font_light>&nbsp;</td>
							</tr>";
			$this->row_count = 1;
			while ($show = $result->FetchRow())
			{
				$this->body .= "
							<tr class=".$this->get_row_color().">
								<td class=medium_font>".$show["type_name"]."</td>
								<td align=center width=100><a href=index.php?a=67&c=".$show["type_id"]."><span class=medium_font><img src=admin_images/btn_admin_edit.gif alt=edit border=0></span></a></td>
								<td align=center width=100><a href=index.php?a=67&d=".$show["type_id"]."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a></td>
							</tr>";$this->row_count++;
			}
			$this->body .= "
						</table>
					<td>
				</tr>";
		}
		else
		{
			$this->body .= "
				<tr>
					<td class=medium_font align=center><br><b>There are no current dropdowns.</b><br><br></td>
				</tr>";
		}
		$this->body .= "
				<tr>
					<td><a href=index.php?a=67&e=1><span class=medium_font><b>Add New Registration Pre-Valued Dropdown</b></span></a></td>
				</tr>
				<tr>
					<td align=left><a href=index.php?a=26&z=1><span class=medium_font><b>back to Registration > General Settings</b></span></a></td>
				</tr>
			</table>";

		return true;
	} //end of function show_all_dropdowns

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function new_dropdown_form()
	{
		if (!$this->admin_demo())$this->body .= "<form action=index.php?a=67&e=1 method=post>\n";
		$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1>\n";
		$this->title = "Registration Setup > General Settings > Registration Pre-Valued Dropdowns > New";
		$this->description = "Use this form to add a new dropdown to
			the dropdowns usable with the optional question fields in registration.  Type the name below and click \"enter\".  You will then be able to add values to
			the dropdown you have just created.";
		$this->body .= "<tr>\n\t
			<td align=right class=medium_font>dropdown label: </td>\n\t
			<td class=medium_font><input type=text name=b[dropdown_label] size=35></td>\n</tr>\n";
		if (!$this->admin_demo()) $this->body .= "<tr>\n\t<td colspan=2  align=center class=medium_font><input type=submit name=b[enter] value=\"Save\">\n\t</td>\n</tr>\n";
		$this->body .= "</table>\n";
		$this->body .= "</form>\n";
		return true;
	} //end of function new_dropdown_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function insert_new_dropdown($db,$information=0)
	{
		if ($information)
		{
			if (strlen(trim($information["dropdown_label"])) > 0)
			{
				$this->sql_query = "insert into ".$this->registration_choices_types_table."
					(type_name)
					values
					(\"".$information["dropdown_label"]."\")";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					//echo $this->sql_query."<br>\n";
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	} //end of function insert_new_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function edit_dropdown($db,$dropdown_id=0)
	{
		if ($dropdown_id)
		{
			$this->sql_query = "select * from ".$this->registration_choices_types_table." where type_id = ".$dropdown_id;

			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				//this dropdown exists
				$show_dropdown = $result->FetchRow();
				$this->sql_query = "select * from ".$this->registration_choices_table." where type_id = ".$dropdown_id." order by display_order";
				//echo $this->sql_query."<bR>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				//show the form to edit this dropdown
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=67&c=".$dropdown_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title = "Registration Setup > General Settings > Registration Pre-Valued Dropdowns > Edit";
				$this->description = "Use this form to add or delete values
					appearing in the registration question dropdowns.  Insert a new value by typing the value and then choosing a value for
					display order.  The display order value determines the order the values appear in the dropdown.  Otherwise the order is
					alphabetically.";
				$this->body .= "<tr class=row_color_red>\n\t<td colspan=3 class=medium_font_light align=center><b>Edit Registration Pre-Valued Dropdown:
					".$show_dropdown["type_name"]."</b> </td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td align=center>\n\t<table cellpadding=2 cellspacing=1 border=0 class=row_color2>\n\t";
				$this->body .= "<tr bgcolor=000066>\n\t\t<td class=medium_font_light>\n\t<b>value </b>\n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light align=center>\n\t<b>display order</b> \n\t\t</td>\n\t\t";
				$this->body .= "<td class=medium_font_light>\n\t&nbsp; \n\t\t</td>\n\t</tr>\n\t";
				if ($result->RecordCount() > 0)
				{
					//this dropdown exists
					//show the value in a list
					$this->row_count = 0;
					while ($show = $result->FetchRow())
					{
						$this->body .= "<tr class=".$this->get_row_color().">\n\t\t<td class=medium_font>\n\t".$show["value"]." \n\t\t</td>\n\t\t";
						$this->body .= "<td class=medium_font align=center>\n\t".$show["display_order"]." \n\t\t</td>\n\t\t";
						$this->body .= "<td align=center width=100>\n\t\t<a href=index.php?a=67&g=".$show["value_id"]."&c=".$dropdown_id."><span class=medium_font><img src=admin_images/btn_admin_delete.gif alt=delete border=0></span></a>\n\t\t</td>\n\t\t";
						$this->row_count++;
					}
				}
				$this->body .= "<tr>\n\t<td class=medium_font>\n\t<input type=text name=b[value] size=25 maxsize=50> \n\t</td>\n\t
					<td class=medium_font>\n\t<select name=b[display_order]>\n\t\t\t";
				for ($i=1;$i < 51;$i++)
				{
					$this->body .= "<option>".$i."</option>\n\t\t\t";
				}
				$this->body .= "</select> \n\t</td>\n\t";
				if (!$this->admin_demo())
					$this->body .= "<td align=center class=medium_font>\n\t<input type=submit name=submit value=\"Save\"> \n\t</td>\n\t";
				$this->body .= "</tr>\n\t";
				$this->body .= "</table>\n\t</td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td><a href=index.php?a=67><span class=medium_font><br><br><b>back to Registration Pre-Valued Dropdowns</b></span></a></td>\n</tr>\n";
				$this->body .= "<tr>\n\t<td><a href=index.php?a=26&z=1><span class=medium_font><b>back to Registration > General Settings</b></span></a></td>\n</tr>\n";
				$this->body .= "</td>\n</tr>\n</table>\n";
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	} //end of function edit_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function add_dropdown_value($db,$dropdown_id=0,$information=0)
	{
		if (($information) && ($dropdown_id))
		{
			if (strlen(trim($information["value"])) > 0)
			{
				$this->sql_query = "insert into ".$this->registration_choices_table."
					(type_id,value,display_order)
					values
					(".$dropdown_id.",\"".$information["value"]."\",".$information["display_order"].")";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
			}
			else
			{
				$this->sql_query = "insert into ".$this->registration_choices_table."
					(type_id,value,display_order)
					values
					(".$dropdown_id.",\"".$information["value"]."\",".$information["display_order"].")";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				$id = $db->Insert_ID();
				return $id;
				return false;
			}
		}
		else
		{
			return false;
		}
	} //end of function add_dropdown_value

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_dropdown_value($db,$value_id=0)
	{
		if ($value_id)
		{
			$this->sql_query = "delete from ".$this->registration_choices_table." where value_id = ".$value_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			return true;
		}
		else
		{
			return false;
		}
	} //end of function delete_dropdown_value

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_dropdown_intermediate($db,$dropdown_id=0)
	{
		if ($dropdown_id)
		{
			$this->sql_query = "select * from ".$this->registration_choices_types_table." where type_id = ".$dropdown_id;
			$result = $db->Execute($this->sql_query);
			if (!$result)
			{
				return false;
			}
			elseif ($result->RecordCount() == 1)
			{
				if (!$this->admin_demo())$this->body .= "<form action=index.php?a=67&d=".$dropdown_id." method=post>\n";
				$this->body .= "<table cellpadding=2 cellspacing=0 border=0 class=row_color1 width=100%>\n";
				$this->title = "Registration Setup > General Settings > Registration Pre-Valued Dropdowns > Delete";
				$this->description = "If the registration question dropdown you are trying to delete
					is attached to existing questions you will need to re-attach them to another.";
				$show_dropdown = $result->FetchRow();
				if (!$this->admin_demo())
				{
					$this->body .= "<tr>\n\t<td class=medium_font_light align=center>\n\t<input type=submit name=z[type_of_submit]
						value=\"delete dropdown\"> \n\t</td>\n</tr>\n";
				}
				$this->body .= "</table>\n";

				//show the delete from db (and everywhere else
				return true;
			}
			else
			{
				return false;
			}

		}
		else
		{
			return false;
		}
	} //end of function delete_dropdown_intermediate

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function delete_dropdown($db,$dropdown_id=0,$information=0)
	{
		//echo "hello from delete dropdown<br>\n";
		if (($dropdown_id) && ($information))
		{
			//echo $information["type_of_submit"]." delete<br>\n";
			if ($information["type_of_submit"] == "delete dropdown")
			{
				$this->sql_query = "delete from ".$this->registration_choices_table." where type_id = ".$dropdown_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}

				$this->sql_query = "delete from ".$this->registration_choices_types_table." where type_id = ".$dropdown_id;
				//echo $this->sql_query."<br>\n";
				$result = $db->Execute($this->sql_query);
				if (!$result)
				{
					return false;
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			//echo "not enough info<br>\n";
			return false;
		}
	} //end of function delete_dropdown

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function display_registration_confirmation_form($db)
	{
		$sql_query = "select * from ".$this->confirm_table;
		$result = $db->Execute($sql_query);
		if (!$result)
		{
			$this->site_error($db->ErrorMsg());
			return false;
		}
		$this->body .= "<table cellpadding=3 cellspacing=1 border=0 width=100%>\n";
		$this->title = "Registration Setup > Unapproved Registrations";
		$this->description = "The table below displays a list of those users who have registered but have not yet confirmed/finalized
				their registration process. This may be because they never received the registration confirmation email due to an invalid email
				address or due to spam filtering. You can manually confirm each user below individually.";

		if($result->RecordCount() != 0)
		{
			$this->body .= "<tr class=row_color_black><td align=center width=33% class=medium_font_light>Name</td><td align=center width=33% class=medium_font_light>E-mail Address</td><td width=33%></td></tr>\n";

			$this->row_count = 0;
			// Start displaying data
			while($show_confirmation = $result->FetchRow())
			{
				$this->body .= "<tr class=".$this->get_row_color().">\n\t<td align=center width=33% class=medium_font>\n\t";
				$this->body .= $show_confirmation["firstname"]." ".$show_confirmation["lastname"]."<br>";
				$this->body .= "username: ".$show_confirmation["username"]."<br>\n";
				$this->body .= "address: ".$show_confirmation["address"]." ".$show_confirmation["address_2"]."<br>\n";
				$this->body .= $show_confirmation["city"]." ".$show_confirmation["state"]." ".$show_confirmation["country"]." ".$show_confirmation["zip"]."<br>\n";
				if (strlen($show_confirmation["phone"]) > 0) $this->body .= "phone: ".$show_confirmation["phone"]."<br>\n";
				if (strlen($show_confirmation["phone_2"]) > 0) $this->body .= "phone 2: ".$show_confirmation["phone_2"]."<br>\n";
				if (strlen($show_confirmation["fax"]) > 0) $this->body .= "fax: ".$show_confirmation["fax"]."<br>\n";
				if (strlen($show_confirmation["company_name"]) > 0) $this->body .= "company name: ".$show_confirmation["company_name"]."<br>\n";
				if (strlen($show_confirmation["url"]) > 0) $this->body .= "url: ".$show_confirmation["url"]."<br>\n";
				if (strlen($show_confirmation["optional_field_1"]) > 0) $this->body .= "optional field 1: ".$show_confirmation["optional_field_1"]."<br>\n";
				if (strlen($show_confirmation["optional_field_2"]) > 0) $this->body .= "optional field 2: ".$show_confirmation["optional_field_2"]."<br>\n";
				if (strlen($show_confirmation["optional_field_3"]) > 0) $this->body .= "optional field 3: ".$show_confirmation["optional_field_3"]."<br>\n";
				if (strlen($show_confirmation["optional_field_4"]) > 0) $this->body .= "optional field 4: ".$show_confirmation["optional_field_4"]."<br>\n";
				if (strlen($show_confirmation["optional_field_5"]) > 0) $this->body .= "optional field 5: ".$show_confirmation["optional_field_5"]."<br>\n";
				if (strlen($show_confirmation["optional_field_6"]) > 0) $this->body .= "optional field 6: ".$show_confirmation["optional_field_6"]."<br>\n";
				if (strlen($show_confirmation["optional_field_7"]) > 0) $this->body .= "optional field 7: ".$show_confirmation["optional_field_7"]."<br>\n";
				if (strlen($show_confirmation["optional_field_8"]) > 0) $this->body .= "optional field 8: ".$show_confirmation["optional_field_8"]."<br>\n";
				if (strlen($show_confirmation["optional_field_9"]) > 0) $this->body .= "optional field 9: ".$show_confirmation["optional_field_9"]."<br>\n";
				if (strlen($show_confirmation["optional_field_10"]) > 0) $this->body .= "optional field 10: ".$show_confirmation["optional_field_10"]."<br>\n";

				$this->body .= "</td>\n\t";
				$this->body .= "<td align=center width=33% class=medium_font>\n\t".$show_confirmation["email"]."</td>\n\t";
				$this->body .= "<td align=center width=33% class=medium_font>\n\t<a href=\"index.php?a=26&b=2&c=".$show_confirmation["username"]."&z=4\">Confirm User</a></td>";
				$this->body .= "</tr>";
				$this->row_count++;
			}

			$this->body .= "</table>";
		}
		else
		{
			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100%>\n";
			$this->body .= "<tr>\n\t<td align=center class=medium_font>\n\t";
			$this->body .= '<center><b>No new registrations are waiting to be confirmed.</b></center>';
			$this->body .= '</td></tr></table>';
		}

		return true;

	} //end of function display_registration_configuration_form

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function update_registration_confirmation($db,$confirmation_info=0)
	{
		if ($confirmation_info)
		{
			include("../classes/site_class.php");
			include("../classes/register_class.php");

			$language_id = $HTTP_COOKIE_VARS["language_id"];
			$register = new Register($db,$language_id,$auction_session,$this->product_configuration);

			$register->confirm($db, $confirmation_info["id"], $confirmation_info["username"]);

			$this->title = "Registration Confirmation";
			$sql_query = "delete from geodesic_confirm where username='".$confirmation_info["username"]."'";
			if(!($db->Execute($sql_query)))
				$this->body .= 'Error deleting from confirm table.<br>';

			$this->body .= "<table cellpadding=3 cellspacing=0 border=0 width=100%>\n";
			$this->body .= "<tr class=row_color1>\n\t<td align=center class=medium_font>\n\t";
			$this->body .= "User <b>". $confirmation_info["username"] . "</b> has been confirmed.<br>";
			$this->body .= "<a href=index.php?a=26&b=1&z=4>Back to Unapproved Registrations</a>";
			$this->body .= "</td></tr></table>";
		}
	} //end of function update_registration_configuration

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} //end of class Registration_configuration


?>
