<?//browse_vote.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class browse_vote extends Site {
    var $error_vote = 0;
    var $debug_vote = 0;

//########################################################################

    function Browse_vote($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$filter_id=0,$product_configuration=0)
    {
        if ($category_id)
            $this->site_category = $category_id;
        elseif ($classified_id)
        {
            $show = $this->get_classified_data($db,$classified_id);
            $this->site_category = $show->CATEGORY;
            $this->classified_id = $classified_id;
        }
        else
            $this->site_category = 0;
        if ($limit)
            $this->browse_limit = $limit;
        $this->Site(&$db,1,$language_id,$classified_user_id,$product_configuration);
        $this->get_ad_configuration(&$db);
        if ($page)
            $this->page_result = $page;
        else
            $this->page_result = 1;

        $this->filter_id = $filter_id;
    } //end of function Browse_vote

//###########################################################

    function voting_form($db,$classified_id=0)
    {
    	if ($this->debug_vote)
    	{
    		echo "<br>TOP OF VOTING_FORM<bR>\n";
		echo $this->configuration_data['voting_system']." is voting system<BR>\n";
		echo $this->classified_user_id." is user id<bR>";
		echo $classified_id." is classified id<bR>";
    	}
        if (($classified_id) && ($this->configuration_data['voting_system']))
        {
            $this->page_id = 116;
            $this->get_text(&$db);
            if ($this->classified_user_id)
            {
                $classified_data = $this->get_classified_data($db,$classified_id);
                $user_data = $this->get_user_data($db,$classified_data->SELLER);
                
                $this->body .="<form action=".$this->configuration_data['classifieds_url']."?a=26&b=".$classified_id." method=post>\n\t";
                $this->body .="<table width=100% border=0 cellpadding=3 cellspacing=1>\n";
                $this->body .="<TR class=page_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[1984]);
                $this->body .="\n\t</td>\n</tr>\n";
                $this->body .="<TR class=page_description>\n\t<TD colspan=2>\n\t".urldecode($this->messages[1985])."\n\t</td>\n</tr>\n";

                if ($this->error_vote > 0)
                    $this->body .="<TR class=error_message>\n\t<TD colspan=2>\n\t".urldecode($this->messages[1986])."\n\t</td>\n</tr>\n";

                // title of ad
                $this->body .="<TR><TD ALIGN=right class=field_labels>".urldecode($this->messages[1987])."\n\t\t</TD>\n\t\t";
                $this->body .="<TD class=field_data>".stripslashes(urldecode($classified_data->TITLE))."\n\t\t</TD>\n\t</TR>\n\t";

                //how do you rate this ad
                $this->body .="<TR><TD ALIGN=right class=field_labels>".urldecode($this->messages[1988])."\n\t\t</TD>\n\t\t";
                $this->body .="<TD class=field_data>";
                $this->body .=" <input type=radio name=c[vote] value=1 class=field_data>".urldecode($this->messages[1989])."<Br>";
                $this->body .=" <input type=radio name=c[vote] value=2 class=field_data>".urldecode($this->messages[1990])."<Br>";
                $this->body .=" <input type=radio name=c[vote] value=3 class=field_data>".urldecode($this->messages[1991]);
                $this->body .="\n\t\t</TD>\n\t</TR>\n\t";

                //comment title
                $this->body .="<TR><TD ALIGN=right class=field_labels>".urldecode($this->messages[1993])."\n\t\t</TD>\n\t\t";
                $this->body .="<TD class=field_data><input type=text size=30 maxsize=30 name=c[vote_title] class=field_data>\n\t\t</TD>\n\t</TR>\n\t";

                //comment
                $this->body .="<TR>\n\t\t<TD ALIGN=right VALIGN=TOP class=field_labels>\n\t\t".urldecode($this->messages[1992])."\n\t\t</TD>\n\t\t";
                $this->body .="<TD class=field_data>\n\t\t<TEXTAREA NAME=c[vote_comments] COLS=50 ROWS=6 class=field_data></TEXTAREA>\n\t\t</td>\n\t</tr>\n\t";

                //submit button
                $this->body .="<tr>\n\t\t<td align=center colspan=2>\n\t\t";
                $this->body .="<INPUT TYPE=submit NAME=submit value=\"".urldecode($this->messages[1994])."\" class=submit_button>";
                $this->body .="<INPUT TYPE=reset NAME=reset class=submit_button>\n\t\t</TD>\n\t</TR>\n\t</TABLE>\n";
                $this->body .="</FORM>";

                $this->display_page($db);
                return true;
            }
            else
            {
                //must be logged in to vote
                $this->body .="<table width=100% border=0 cellpadding=3 cellspacing=1>\n";
                $this->body .="<TR class=page_title>\n\t<TD>\n\t".urldecode($this->messages[1984]);
                $this->body .="\n\t</td>\n</tr>\n";
                $this->body .="<TR class=page_description>\n\t<TD colspan=2>\n\t".urldecode($this->messages[1985])."\n\t</td>\n</tr>\n";


                $this->body .="<TR class=error_message>\n\t<TD>\n\t".urldecode($this->messages[1995])."\n\t</td>\n</tr>\n";

                $this->body .="<TR class=back_to_current_ad_link>\n\t\t<TD>\n\t\t<a href=".$this->configuration_data['classified_file_name']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[1996])."</a>\n\t\t</TD>\n\t</tr>";
                $this->body .= "</table>\n";
                $this->display_page($db);
                return true;
            }
        }
        else
        {
            return false;
        }

    } //end of voting_form

//########################################################################3

    function collect_vote($db,$classified_id=0,$info=0)
    {
    	if ($this->debug_vote)
    	{
    		echo "<BR>TOP OF COLLECT_VOTE<Br>\n";
    		echo $classified_id." is classified_id<BR>\n";
    		echo $info." is info<BR>\n";
    		echo $this->configuration_data['voting_system']." is configuration_data[voting_system]<Br>\n";
    		echo $this->classified_user_id." is classified_user_id<Br>\n";
    		echo $info["vote_title"]." is info-vote_title<BR>\n";
    		echo $info["vote_comments"]." is info-vote_comments<BR>\n";
    		echo $info["vote"]." is info-vote<BR>\n";
    		
    	}
        if (($classified_id) && ($info) && ($this->configuration_data['voting_system']))
        {
            $this->page_id = 116;
            $this->get_text(&$db);
            $classified_data = $this->get_classified_data($db,$classified_id);
    	if ($this->debug_vote)
    	{
    		echo $classified_data->SELLER." is seller<bR>\n";
    	}
            if ($classified_data->SELLER != $this->classified_user_id)
            {
            	if ((strlen(trim($info["vote_title"])) > 0) && (strlen(trim($info["vote_comments"])) > 0) && ($info["vote"]))
		{
			if ($this->configuration_data['voting_system'] == 1)
				$this->sql_query = "select * from ".$this->voting_table." where classified_id =".$classified_id." and voter_ip = \"".$_SERVER["REMOTE_ADDR"]."\"";
			elseif ($this->configuration_data['voting_system'] == 2)
				$this->sql_query = "select * from ".$this->voting_table." where classified_id =".$classified_id." and user_id = ".$this->classified_user_id;
			elseif ($this->configuration_data['voting_system'] == 3)
				$this->sql_query = "select * from ".$this->voting_table." where classified_id =".$classified_id." and ((voter_ip = \"".$_SERVER["REMOTE_ADDR"]."\") || (user_id = ".$this->classified_user_id."))";
			else
			{
			    	if ($this->debug_vote)
			    	{
			    		echo "voting_system is not setup<bR>\n";
			    	}				
				return false;
			}
		
		    	if ($this->debug_vote)
		    	{
		    		echo $this->sql_query."<bR>\n";
		    	}
			$number_of_votes_result = &$db->Execute($this->sql_query);
		
			if (!$number_of_votes_result)
			{
			    	if ($this->debug_vote)
			    	{
			    		echo $this->sql_query."<bR>\n";
			    	}
				$this->error_message = $this->messages[1998];
				return false;
			}
			elseif ($number_of_votes_result->RecordCount() > 0)
			{
			    //this user has already voted for this ad
			    $this->body .="<table width=100% border=0 cellpadding=3 cellspacing=1>\n";
			    $this->body .="<TR class=page_title>\n\t<TD>\n\t".urldecode($this->messages[1984]);
			    $this->body .="\n\t</td>\n</tr>\n";
		
			    //$this->body .="<TR class=page_description>\n\t<TD>\n\t".urldecode($this->messages[1985]);
			    //$this->body .="\n\t</td>\n</tr>\n";
			    $this->body .="<TR class=error_message>\n\t<TD>\n\t".urldecode($this->messages[1997])."\n\t</td>\n</tr>\n";
		
			    $this->body .="<TR class=back_to_current_ad_link>\n\t\t<TD>\n\t\t<a href=".$this->configuration_data['classified_file_name']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[1996])."</a>\n\t\t</TD>\n\t</tr>";
			    $this->body .= "</table>\n";
			    $this->display_page($db);
			    return true;
		
			}
			else
			{
			    //this user has not voted for this ad yet
			    //add their vote
			    $this->sql_query = "insert into ".$this->voting_table."
				(classified_id,user_id,voter_ip,vote,vote_title,vote_comments,date_entered)
				values
				(".$classified_id.",\"".$this->classified_user_id."\",\"".$_SERVER["REMOTE_ADDR"]."\",".$info["vote"].",\"".addslashes($info["vote_title"])."\",\"".addslashes($info["vote_comments"])."\",".$this->shifted_time($db).")";
			    $register_vote_result = &$db->Execute($this->sql_query);
			    if ($this->debug_vote) echo $this->sql_query."<br>\n";
			    if (!$register_vote_result)
			    {
				if ($this->debug_vote) echo $this->sql_query."<br>\n";
				$this->error_message = $this->messages[80];
				return false;
			    }
			    else
			    {
				//update the stats in the classified ad itself
				if ($info["vote"] == 1)
				{
				    $which_vote = "ONE_VOTES";
				    $field_vote = "one_votes";
				}
				elseif ($info["vote"] == 2)
				{
				    $which_vote = "TWO_VOTES";
				    $field_vote = "two_votes";
				}
				elseif ($info["vote"] == 3)
				{
				    $which_vote = "THREE_VOTES";
				    $field_vote = "three_votes";
				}
				else
				    $which_vote = "";
				//echo $which_vote." is which vote<br>\n";
				if (strlen($which_vote) > 0)
				{
				    $this->sql_query = "update ".$this->classifieds_table." set
					".$field_vote." = ".($classified_data->$which_vote + 1).",
					vote_total = ".($classified_data->VOTE_TOTAL + 1)."
					where id = ".$classified_id;
				   $update_vote_result = &$db->Execute($this->sql_query);
				    if ($this->debug_vote) echo $this->sql_query."<br>\n";
				    if (!$update_vote_result)
				    {
					if ($this->debug_vote) echo $this->sql_query."<br>\n";
					$this->error_message = $this->messages[80];
					return false;
				    }
				}
				$this->body .="<table width=100% border=0 cellpadding=3 cellspacing=1>\n";
				$this->body .="<TR class=page_title>\n\t<TD>\n\t".urldecode($this->messages[1984]);
				$this->body .="\n\t</td>\n</tr>\n";
			
			       // $this->body .="<TR class=page_description>\n\t<TD>\n\t".urldecode($this->messages[1985]);
			       // $this->body .="\n\t</td>\n</tr>\n";
	
				$this->body .="<TR class=error_message>\n\t<TD>\n\t".urldecode($this->messages[1999])."\n\t</td>\n</tr>\n";
	
				$this->body .="<TR class=back_to_current_ad_link>\n\t\t<TD>\n\t\t<a href=".$this->configuration_data['classified_file_name']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[1996])."</a>\n\t\t</TD>\n\t</tr>";
				$this->body .= "</table>\n";
				$this->display_page($db);
				return true;
			    }
			}
			return false;
	    	}
	    	else
		{
			if ($this->debug_vote) echo "<br>\n";					
			$this->error_vote++;
			return false;
        	    	}

        	}
        	else
        	{
        	    return false;
        	}
        }
        else
        {
			//this user has already voted for this ad
			$this->body .="<table width=100% border=0 cellpadding=3 cellspacing=1>\n";
			$this->body .="<TR class=page_title>\n\t<TD>\n\t".urldecode($this->messages[1984]);
			$this->body .="\n\t</td>\n</tr>\n";
	
			$this->body .="<TR class=error_message>\n\t<TD>\n\t".urldecode($this->messages[1997])."\n\t</td>\n</tr>\n";
			$this->body .="<TR class=back_to_current_ad_link>\n\t\t<TD>\n\t\t<a href=".$this->configuration_data['classified_file_name']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[1996])."</a>\n\t\t</TD>\n\t</tr>";
			$this->body .= "</table>\n";
			$this->display_page($db);
			return true;
        }
    } //end of function collect_vote

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

    function vote_success($db,$classified_id)
    {
        $this->page_id = 116;
        $this->get_text(&$db);
        $this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
        $this->body .="<TR class=page_title>\n\t<TD colspan=2>\n\t".urldecode($this->messages[1984]);
        $this->body .="\n\t</td>\n</tr>\n";
        $this->body .="<tr>\n\t<td class=page_description>\n\t".urldecode($this->messages[1999])."\n\t<br>
            <a href=".$this->configuration_data['classifieds_url']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[1996])."</a>\n\t</td>\n</tr>\n";
        $this->body .="</table>\n";
        return true;
    } //end of function vote_success

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

    function browse_vote_comments(&$db,$classified_id=0)
    {
    	if ($classified_id)
    	{
		$this->page_id = 115;
		$this->get_text($db);
		$classified_data = $this->get_classified_data($db,$classified_id);
		$this->sql_query = "select * from ".$this->voting_table." where classified_id = ".$classified_id."
		     order by date_entered desc";
		$this->sql_query .= " limit ".(($this->page_result -1) * $this->configuration_data['number_of_vote_comments_to_display']).",".$this->configuration_data['number_of_vote_comments_to_display'];
		//echo $this->sql_query."<br>\n";
		$result = $db->Execute($this->sql_query);

		$this->count_sql_query = "select count(vote) as total_votes from ".$this->voting_table." where classified_id = ".$classified_id."
		     order by date_entered desc";
		$count_result = $db->Execute($this->count_sql_query);
		//echo $this->count_sql_query."<br>\n";
		if ((!$result) || (!$count_result))
		{
		    //$this->body .=$this->sql_query." is the query<br>\n";
		    $this->error_message = "<font class=error_message>".urldecode($this->messages[33])."</font>";
		    return false;
		}
		elseif (($result->RecordCount() > 0) || ($result->RecordCount() == 0))
		{
		    $total_votes = $count_result->FetchNextObject();

		    $this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100% valign=top>\n\t";
		   // $this->body .= "<tr><td class=back_to_current_ad_link><a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[10036]." ".$classified_data->TITLE)."</a></td></tr>";

		    $this->body .= "<tr><td class=page_title>".urldecode($this->messages[2000])."</td></tr>";
		     $this->body .= "<tr><td class=page_description>".urldecode($this->messages[2288])."</td></tr>";
		    $this->body .= "<tr><td><table cellpadding=2 cellspacing=1 border=0>";
		    $this->body .= "<tr><td class=field_labels>".urldecode($this->messages[2001])."</td>\n\t";
		    $this->body .= "<td class=field_data>";
		    if ($classified_data->VOTE_TOTAL)
		    	$this->body .= sprintf("%01.2f",(($classified_data->ONE_VOTES / $classified_data->VOTE_TOTAL) * 100))." %";
		    else
		    	$this->body .="--";
		    $this->body .= "</td></tr>";
		    $this->body .= "<tr><td class=field_labels>".urldecode($this->messages[2002])."</td>\n\t";
		    $this->body .= "<td class=field_data>";
		    if ($classified_data->VOTE_TOTAL)
		    	$this->body .= sprintf("%01.2f",(($classified_data->TWO_VOTES / $classified_data->VOTE_TOTAL) * 100))." %";
		    else
		    	$this->body .="--";		    	
		    $this->body .= "</td></tr>";
		    $this->body .= "<tr><td class=field_labels>".urldecode($this->messages[2003])."</td>\n\t";
		    $this->body .= "<td class=field_data>";
		    if ($classified_data->VOTE_TOTAL)
		    	$this->body .= sprintf("%01.2f",(($classified_data->THREE_VOTES / $classified_data->VOTE_TOTAL) * 100))." %";
		    else
		    	$this->body .="--";		    	
		    $this->body .=  "</td></tr>";
		    $this->body .= "<tr><td class=field_labels>".urldecode($this->messages[2004])."</td>\n\t";
		    $this->body .= "<td class=field_data>".$classified_data->VOTE_TOTAL."</td></tr>";
		    $this->body .= "</table>";
		    if ($this->configuration_data['number_of_vote_comments_to_display'] < $total_votes->TOTAL_VOTES)
		    {
			//display the link to the next 10
			$number_of_page_results = ceil($total_votes->TOTAL_VOTES / $this->configuration_data['number_of_vote_comments_to_display']);
			$this->body .="<tr class=comment_result_page_links>\n\t<td valign=top><font class=comments_more_results>".urldecode($this->messages[25])." ".$this->page_result."</font>
			    <font class=comments_page_of>".urldecode($this->messages[26]).ceil($total_returned / $this->configuration_data['number_of_ads_to_display'])."</font></td>\n</tr>\n";
		    }

		    $result->Move(0);
		    $this->body .= "<tr><td><table width=100% border=0 cellspacing=1 cellpadding=2>";
		    $this->body .= "<tr><td class=vote_header>".urldecode($this->messages[2005])."</td>
		    	<td class=username_header>".urldecode($this->messages[2006])."</td>
			<td class=comment_header>".urldecode($this->messages[2007])."</td>
			<td class=date_header>".urldecode($this->messages[2008])."</td></tr>";
		    while ($show_comment = $result->FetchNextObject())
		    {
			if (($this->row_count % 2) == 0)
			    $css_row_tag = "vote_result_even_row";
			else
			    $css_row_tag = "vote_result_odd_row";
			$this->body .= "<tr class=".$css_row_tag."><td>";
			if ($show_comment->VOTE == 1)
			    $this->body .= urldecode($this->messages[2009]);
			elseif ($show_comment->VOTE == 2)
			    $this->body .= urldecode($this->messages[2010]);
			elseif ($show_comment->VOTE == 3)
			    $this->body .= urldecode($this->messages[2001]);
			$this->body .= "</td>";
			$this->body .= "<td>".$this->get_user_name($db,$show_comment->USER_ID)."</td>\n";
			$this->body .= "<td>".$show_comment->VOTE_TITLE."<br>";
			$this->body .= "<font class=long_comment>".$show_comment->VOTE_COMMENTS."</font>";
			$this->body .= "</td>";
			$this->body .= "<td>".date("M d",$show_comment->DATE_ENTERED)."</td></tr>";
			$this->row_count++;
		    } //end of while

		    $this->body .= "</table></td></tr>";

		    if ($this->configuration_data['number_of_vote_comments_to_display'] < $total_votes->TOTAL_VOTES)
		    {
			//display the link to the next 10
			$number_of_page_results = ceil($total_votes->TOTAL_VOTES / $this->configuration_data['number_of_vote_comments_to_display']);
			$this->body .="<tr class=more_results>\n\t<td valign=top>".urldecode($this->messages[24])." ";
			if ($number_of_page_results < 10)
			{
			    for ($i = 1;$i <= $number_of_page_results;$i++)
			    {
				if ($this->page_result == $i)
				{
				    $this->body .=" <b>".$i."</b> ";
				}
				else
				{
				    $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=27&b=".$this->site_category."&page=".$i."&c=".$browse_type." class=comment_result_page_links>".$i."</a> ";
				}
			    }
			}
			else
			{
			    $number_of_sections =  ceil($number_of_page_results/10);
			    for ($section = 0;$section < $number_of_sections;$section++)
			    {
				if (($this->page_result > ($section * 10)) && ($this->page_result <= (($section+1) * 10)))
				{
				    //display the individual pages within this section
				    for ($page = (($section * 10) + 1);$page <= (($section+1) * 10);$page++)
				    {
					if ($page <= $number_of_page_results)
					    $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=27&b=".$this->site_category."&page=".$page."&c=".$browse_type." class=comment_result_page_links>".$page."</a> ";
				    }
				}
				else
				{
				    //display the link to the section
				    $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=27&b=".$this->site_category."&page=".(($section*10)+1)."&c=".$browse_type." class=comment_result_page_links>".(($section*10)+1)."</a>";
				}
				if (($section+1) < $number_of_sections)
				    $this->body .= "<font class=comment_result_page_links>..</font>";
			    }
			}
			$this->body .="</td>\n</tr>\n";
		    }
		    $this->body .="<tr class=back_to_current_ad_link>\n\t\t<TD>\n\t\t<a href=".$this->configuration_data['classified_file_name']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[2012])."</a>\n\t\t</td>\n\t</tr>";
		    $this->body .="</table>\n";
		    $this->display_page($db);
		    return true;
		}
		else
		{			
		   /* $this->body .="<table cellpadding=2 cellspacing=1 border=0 valign=top width=100%>\n\t";
		    $this->body .= "<tr><td class=page_title>".urldecode($this->messages[2000])."</td></tr>";
		    $this->body .="<tr class=no_votes_yet>\n\t<td valign=top>".urldecode($this->messages[34])."</td>\n</tr>\n";
		    $this->body .="<tr class=back_to_current_ad_link>\n\t\t<TD>\n\t\t<a href=".$this->configuration_data['classified_file_name']."?a=2&b=".$classified_id." class=back_to_current_ad_link>".urldecode($this->messages[2012])."</a>\n\t\t</td>\n\t</tr>";
		    $this->body .="</table>\n";*/
		    $this->display_page($db);
		    return true;
		}
	}
	else
	{
		return false;
	}
    } // end of function browse_vote_comments

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

} // end class Browse_vote

?>
