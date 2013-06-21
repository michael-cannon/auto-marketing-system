<?
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

if($this->is_classifieds())
	$software_name = "GeoClassifieds";
elseif ($this->is_class_auctions())
	$software_name = "GeoClassAuctions";
else
	$software_name = "GeoAuctions";

$this->sql_query = "select * from ".$this->version_table;
$result = $db->Execute($this->sql_query);
ECHO $menu;
if (!$result)
{
//echo "there is no version table<Br>\n";
//$this->site_error($this->sql_query,$db->ErrorMsg());
//return false;
}
elseif ($result->RecordCount() > 0)
{
	$show = $result->FetchRow();
	$this->footer_html .= "
			</td>
		</tr>
		<tr align=center bgcolor=000066>
			<td colspan=100% class=medium_font_light>
				".$software_name." created by <a href=http://www.geodesicsolutions.com><span class=medium_font_light><font color=ffffff>Geodesic Solutions LLC</font></span></a><br>
				<span class=medium_font_light>".$software_name." Enterprise Database Version ".$show["db_version"]."</span>
			</td>
		</tr>
	</table>
</html>";
}
else
{
	//echo "there is no version record within version table<Br>\n";
	//$this->site_error($this->sql_query,$db->ErrorMsg());
	//return false;
}
?>
