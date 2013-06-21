<? //browse_ads.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

class Browse_ads extends Site {
	var $subcategory_array = array();
	var $notify_data = array();
	var $debug_browse = 0;
	var $lcv = 0;
    var $total_count_returned_result = 0;
    var $no_classifieds = '';

//########################################################################

	function Browse_ads($db,$classified_user_id,$language_id,$category_id=0,$page=0,$classified_id=0,$filter_id=0,$state_filter=0,$zip_filter=0,$zip_filter_distance=0,$product_configuration=0)
	{
		if ($category_id)
			$this->site_category = $category_id;
		elseif ($classified_id)
		{
			$show = $this->get_classified_data($db,$classified_id);
			$this->site_category = $show['category'];
		}
		else
			$this->site_category = 0;
		if ($limit)
			$this->browse_limit = $limit;
		$this->Site($db,1,$language_id,$classified_user_id, $product_configuration);
		if ($page)
			$this->page_result = $page;
		else
			$this->page_result = 1;

		$this->filter_id = $filter_id;
		$this->state_filter = $state_filter;
		$this->zip_filter = $zip_filter;
		$this->zip_filter_distance = $zip_filter_distance;

		if ($this->debug_browse)
		{
			echo $this->zip_filter." is zip_filter in browse_ads<BR>\n";
			echo $this->zip_filter_distance." is zip_filter_distance in browse_ads<BR>\n";
		}
	} //end of function Browse_ads

//###########################################################

    //My function 20.03.2006
    function browse_all($db,$browse_type)
    {
    	//$this->configuration_data['number_of_ads_to_display'] = 50;
        $this->debug = 0;
        $this->debug_browse = 0;
        $this->page_id = 3;
        $this->get_text($db, 0, 10003);
        $this->no_classifieds = "";

        $this->sql_query = "select * from ".$this->classifieds_table."
        where live = 1 ";

        if (($browse_type == 0) && ($this->configuration_data['default_display_order_while_browsing']))
        {
            if (($this->site_category) && ($this->configuration_data['default_display_order_while_browsing'] == 1000))
            {
                //the default display order is set on a category by category basis
                $this->category_order_sql_query = "select * from ".$this->categories_table." where category_id = ".$this->site_category;
                $category_order_result = $db->Execute($this->category_order_sql_query);
                if ($this->debug_browse) echo $this->category_order_sql_query."<br>\n";
                if (!$category_order_result)
                {
                    if ($this->debug_browse) echo $this->category_order_sql_query."<br>\n";
                    return false;
                }
                elseif ($category_order_result->RecordCount() == 1)
                {
                    $show_category_default = $category_order_result->FetchRow();
                    if ($show_category_default["default_display_order_while_browsing_category"])
                    {
                        if ($this->debug_browse) echo "using category specific order setting: ".$show_category_default["default_display_order_while_browsing_category"]."<bR>\n";
                        $browse_type = $show_category_default["default_display_order_while_browsing_category"];
                    }
                    else
                    {
                        if ($this->debug_browse) echo "problem is category order setting 1<Br>\n";
                        $browse_type = 0;
                    }
                }
                else
                {
                    //error....category does not exist
                    if ($this->debug_browse) echo "problem is category order setting 2<Br>\n";
                    $browse_type = 0;
                }

            }
            else
            {
                //use the site wide setting as the default browse by type
                if ($this->debug_browse) echo "using site wide order setting: ".$this->configuration_data['default_display_order_while_browsing']."<bR>\n";
                $browse_type = $this->configuration_data['default_display_order_while_browsing'];
            }
        }

        $this->browse_type = $browse_type;

        switch ($browse_type)
        {
            case 0: //nothing
                 $order_by = "order by better_placement desc,date desc ";
                break;
            case 1: //price desc
                $order_by = " order by price desc, current_bid desc, better_placement desc ";
                break;
            case 2: //price asc
                $order_by = " order by price asc, current_bid asc, better_placement desc ";
                break;
            case 3: //date earliest to latest
                $order_by = " order by date asc, better_placement desc ";
                break;
            case 4: //date latest to earliest
                $order_by = " order by date desc, better_placement desc ";
                break;
            case 5: //title asc
                $order_by = " order by title asc, better_placement desc ";
                break;
            case 6: //title desc
                $order_by = " order by title desc, better_placement desc ";
                break;
            case 7: //city asc
                $order_by = " order by location_city asc, better_placement desc ";
                break;
            case 8: //city desc
                $order_by = " order by location_city desc, better_placement desc ";
                break;
            case 9: //state asc
                $order_by = " order by location_state asc, better_placement desc ";
                break;
            case 10: //state desc
                $order_by = " order by location_state desc, better_placement desc ";
                break;
            case 11: //country asc
                $order_by = " order by location_country asc, better_placement desc ";
                break;
            case 12: //country desc
                $order_by = " order by location_country desc, better_placement desc ";
                break;
            case 13: //zip asc
                $order_by = " order by location_zip asc, better_placement desc ";
                break;
            case 14: //zip desc
                $order_by = " order by location_zip desc, better_placement desc ";
                break;
            case 15: //optional field 1 asc
                $order_by = " order by optional_field_1 asc, better_placement desc ";
                break;
            case 16: //optional field 1 desc
                $order_by = " order by optional_field_1 desc, better_placement desc ";
                break;
            case 17: //optional field 2 asc
                $order_by = " order by optional_field_2 asc, better_placement desc ";
                break;
            case 18: //optional field 2 desc
                $order_by = " order by optional_field_2 desc, better_placement desc ";
                break;
            case 19: //optional field 3 asc
                $order_by = " order by optional_field_3 asc, better_placement desc ";
                break;
            case 20: //optional field 3 desc
                $order_by = " order by optional_field_3 desc, better_placement desc ";
                break;
            case 21: //optional field 4 asc
                $order_by = " order by optional_field_4 asc, better_placement desc ";
                break;
            case 22: //optional field 4 desc
                $order_by = " order by optional_field_4 desc, better_placement desc ";
                break;
            case 23: //optional field 5 asc
                $order_by = " order by optional_field_5 asc, better_placement desc ";
                break;
            case 24: //optional field 5 desc
                $order_by = " order by optional_field_5 desc, better_placement desc ";
                break;
            case 25: //optional field 6 asc
                $order_by = " order by optional_field_6 asc, better_placement desc ";
                break;
            case 26: //optional field 6 desc
                $order_by = " order by optional_field_6 desc, better_placement desc ";
                break;
            case 27: //optional field 7 asc
                $order_by = " order by optional_field_7 asc, better_placement desc ";
                break;
            case 28: //optional field 7 desc
                $order_by = " order by optional_field_7 desc, better_placement desc ";
                break;
            case 29: //optional field 8 asc
                $order_by = " order by optional_field_8 asc, better_placement desc ";
                break;
            case 30: //optional field 8 desc
                $order_by = " order by optional_field_8 desc, better_placement desc ";
                break;
            case 31: //optional field 9 asc
                $order_by = " order by optional_field_9 asc, better_placement desc ";
                break;
            case 32: //optional field 9 desc
                $order_by = " order by optional_field_9 desc, better_placement desc ";
                break;
            case 33: //optional field 10 asc
                $order_by = " order by optional_field_10 asc, better_placement desc ";
                break;
            case 34: //optional field 10 desc
                $order_by = " order by optional_field_10 desc, better_placement desc ";
                break;
            case 35: //city asc
                $order_by = " order by location_city asc, better_placement desc ";
                break;
            case 36: //city desc
                $order_by = " order by location_city desc, better_placement desc ";
                break;
            case 37: //state asc
                $order_by = " order by location_state asc, better_placement desc ";
                break;
            case 38: //state desc
                $order_by = " order by location_state desc, better_placement desc ";
                break;
            case 39: //country asc
                $order_by = " order by location_country asc, better_placement desc ";
                break;
            case 40: //country desc
                $order_by = " order by location_country desc, better_placement desc ";
                break;
            case 41: //zip asc
                $order_by = " order by location_zip asc, better_placement desc ";
                break;
            case 42: //zip desc
                $order_by = " order by location_zip desc, better_placement desc ";
                break;
            case 43: //business_type asc
                $order_by = " order by business_type asc, better_placement desc ";
                break;
            case 44: //business_type desc
                $order_by = " order by business_type desc, better_placement desc ";
                break;
            case 45: //optional field 11 asc
                $order_by = " order by optional_field_11 asc, better_placement desc ";
                break;
            case 46: //optional field 11 desc
                $order_by = " order by optional_field_11 desc, better_placement desc ";
                break;
            case 47: //optional field 12 asc
                $order_by = " order by optional_field_12 asc, better_placement desc ";
                break;
            case 48: //optional field 12 desc
                $order_by = " order by optional_field_12 desc, better_placement desc ";
                break;
            case 49: //optional field 13 asc
                $order_by = " order by optional_field_13 asc, better_placement desc ";
                break;
            case 50: //optional field 13 desc
                $order_by = " order by optional_field_13 desc, better_placement desc ";
                break;
            case 51: //optional field 14 asc
                $order_by = " order by optional_field_14 asc, better_placement desc ";
                break;
            case 52: //optional field 14 desc
                $order_by = " order by optional_field_14 desc, better_placement desc ";
                break;
            case 53: //optional field 15 asc
                $order_by = " order by optional_field_15 asc, better_placement desc ";
                break;
            case 54: //optional field 15 desc
                $order_by = " order by optional_field_15 desc, better_placement desc ";
                break;
            case 55: //optional field 16 asc
                $order_by = " order by optional_field_16 asc, better_placement desc ";
                break;
            case 56: //optional field 16 desc
                $order_by = " order by optional_field_16 desc, better_placement desc ";
                break;
            case 57: //optional field 17 asc
                $order_by = " order by optional_field_17 asc, better_placement desc ";
                break;
            case 58: //optional field 17 desc
                $order_by = " order by optional_field_17 desc, better_placement desc ";
                break;
            case 59: //optional field 18 asc
                $order_by = " order by optional_field_18 asc, better_placement desc ";
                break;
            case 60: //optional field 18 desc
                $order_by = " order by optional_field_18 desc, better_placement desc ";
                break;
            case 61: //optional field 19 asc
                $order_by = " order by optional_field_19 asc, better_placement desc ";
                break;
            case 62: //optional field 19 desc
                $order_by = " order by optional_field_19 desc, better_placement desc ";
                break;
            case 63: //optional field 20 asc
                $order_by = " order by optional_field_20 asc, better_placement desc ";
                break;
            case 64: //optional field 20 desc
                $order_by = " order by optional_field_20 desc, better_placement desc ";
                break;
            default:
                $order_by = "order by better_placement desc,date desc ";
                break;
        }
		$this->sql_query_classifieds_count = $this->sql_query;
        $result_count = $db->Execute($this->sql_query_classifieds_count);
        $total_returned = $result_count->RecordCount();
        $this->total_count_returned_result = $total_returned;

        $this->sql_query_classifieds = $this->sql_query . $order_by .  "limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
        $result = $db->Execute($this->sql_query_classifieds);

        $this->browsing_configuration = $this->configuration_data;

        if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
        {
        	//display the link to the next 10
            $number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
            $this->body .="<tr class=browsing_result_page_links>\n\t<td ><font class=more_results>".urldecode($this->messages[25])." ".$this->page_result."</font><font class=page_of>".urldecode($this->messages[26])."</font> <font class=more_results>".ceil($total_returned / $this->configuration_data['number_of_ads_to_display'])."</font>&nbsp;&nbsp;&nbsp;";
        }

                if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
                {
                    //display the link to the next 10
                    $number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
                    $this->body .=urldecode($this->messages[24])." ";
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
                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$i;
                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                $this->body .=" class=browsing_result_page_links>".$i."</a> ";
                            }
                        }
                    }
                    elseif($number_of_page_results < 100)
                    {
                        $number_of_sections =  ceil($number_of_page_results/10);
                        for ($section = 0;$section < $number_of_sections;$section++)
                        {
                            if (($this->page_result > ($section * 10)) && ($this->page_result <= (($section+1) * 10)))
                            {
                                //display the individual pages within this section
                                for ($page = (($section * 10) + 1);$page <= (($section+1) * 10);$page++)
                                {
                                    if($page == $this->page_result)
                                    {
                                        $this->body .=" <b>".$page."</b> ";
                                        continue;
                                    }
                                    if ($page <= $number_of_page_results)
                                    {
                                        $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$page;
                                        if ($browse_type) $this->body .= "&c=".$browse_type;
                                        $this->body .= " class=browsing_result_page_links>".$page."</a> ";
                                    }
                                }

                            }
                            else
                            {
                                //display the link to the section
                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".(($section*10)+1);
                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                $this->body .= " class=browsing_result_page_links>".(($section*10)+1)."</a>";
                            }
                            if (($section+1) < $number_of_sections)
                                $this->body .= " <font class=browsing_result_page_links>..</font> ";
                        }
                    }
                    else
                    {
                        $number_of_sections =  ceil($number_of_page_results/100);
                        for ($section = 0;$section < $number_of_sections;$section++)
                        {
                            if (($this->page_result > ($section * 100)) && ($this->page_result <= (($section+1) * 100)))
                            {
                                //display tens
                                for ($page = (($section * 100) + 1);$page <= (($section+1) * 100);$page+=10)
                                {

                                    if (($this->page_result >= $page) && ($this->page_result <= ($page+9)))
                                    {
                                        //display ones
                                        for ($page_link = $page;$page_link <= ($page+9);$page_link++)
                                        {
                                            if($page_link == $this->page_result)
                                            {
                                                $this->body .=" <b>".$page_link."</b> ";
                                                continue;
                                            }
                                            if ($page_link <= $number_of_page_results)
                                            {
                                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$page_link;
                                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                                $this->body .= " class=browsing_result_page_links>".$page_link."</a> ";
                                            }
                                        }

                                    }else
                                    {
                                        if($page == $this->page_result)
                                        {
                                            $this->body .=" <b>".$page."</b> ";
                                            continue;
                                        }
                                        if ($page <= $number_of_page_results)
                                        {
                                            $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$page;
                                            if ($browse_type) $this->body .= "&c=".$browse_type;
                                            $this->body .= " class=browsing_result_page_links>".$page."</a> ";
                                            if (($section+1) < $number_of_sections)
                                                $this->body .= " <font class=browsing_result_page_links>..</font> ";
                                        }
                                    }
                                }
                            }
                            else
                            {
                                //display hundreds
                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".(($section*100)+1);
                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                    $this->body .= " class=browsing_result_page_links>".(($section*100)+1)."</a>";
                                if (($section+1) < $number_of_sections)
                                    $this->body .= " <font class=browsing_result_page_links>..</font> ";
                            }
                        }
                	}
                }
///
        //Has been added on 23.03.2006
        $bottomPaging = $this->body.'<br><br><br>';
        //$result->Move(0);
		// MLC client doens't want bids displayd
        $this->browsing_configuration['display_number_bids'] = 0;
        $this->browsing_configuration['display_time_left'] = 1;
        $this->browsing_configuration['classified_time_left'] = 1;
        $this->browsing_configuration['display_price'] = 1;
        $this->browsing_configuration['display_browsing_city_field'] = 0;
        $this->browsing_configuration['display_browsing_state_field'] = 0;
        $this->configuration_data["url_rewrite"] = 1;    

        $this->display_browse_result($db,$result,"browsing_result_table_header");
        $this->body .= $bottomPaging;
        $this->body = "<table border=0 width=100% cellpadding=0 cellspacing=0><tr><td>" . $this->body . "</td></tr></table>";
        $this->site_category = 0;

        $this->display_page($db);
        return true;
    }

    function browse($db,$browse_type=0)
    {
        $this->browse_type = $browse_type;
        $this->page_id = 3;
        $this->get_text($db, 0, 10003);
        if ($this->filter_id)
        {
            //add filter association to end of sql_query
            $filter_in_statement = $this->get_sql_filter_in_statement($db);
            $this->sql_filter_in_statement = " and filter_id ".$filter_in_statement." ";
        }
        if ($this->state_filter)
        {
            //add state to end of sql_query
            $this->sql_state_filter_statement = " and location_state = \"".$this->state_filter."\" ";
        }
        if (($this->zip_filter_distance) && ($this->zip_filter))
        {
            //add zip code in statement to end of sql_query
            $zip_filter_in_statement = $this->get_sql_zip_filter_in_statement($db);
            $this->sql_zip_filter_in_statement = "and ".$zip_filter_in_statement." ";
            if ($this->debug_browse) $this->sql_zip_filter_in_statement." is the zip filter in statement<bR>\n";
        }
        $this->body .="<table cellpadding=2 cellspacing=1 border=0 width=100%>\n";

        if ($this->site_category)
        {
            if ($this->configuration_data['display_sub_category_ads'])
            {
                $this->get_sql_in_statement($db,$this->site_category);
            }
            else
            {
                $this->in_statement = " in (".$this->site_category.") ";
            }
        }

        //$this->sql_query = "select * from ".$this->classifieds_table." left outer join ".$this->images_urls_table."
        //    on ".$this->classifieds_table.".id = ".$this->images_urls_table.".classified_id
        //    where (".$this->classifieds_table.".category ".$this->in_statement." and live = 1 and (".$this->images_urls_table.".display_order = 1 or ".$this->images_urls_table.".display_order = null)) ";
        $this->sql_query = "select * from ".$this->classifieds_table."
            where category ".$this->in_statement." and live = 1 ";
        if ($this->product_configuration->is_auctions())
        {
            $this->sql_query .= " and item_type = 2 ";
        }
        elseif ($this->product_configuration->is_classifieds())
        {
            $this->sql_query .= " and item_type = 1 ";
        }

        if ($this->filter_id)
            $this->sql_query .= $this->sql_filter_in_statement;
        if ($this->state_filter)
            $this->sql_query .= $this->sql_state_filter_statement;
        if (($this->zip_filter_distance) && ($this->zip_filter))
            $this->sql_query .= $this->sql_zip_filter_in_statement;
        if ($this->debug_browse) echo $this->sql_query." at first<bR>\n";

        if (($browse_type == 0) && ($this->configuration_data['default_display_order_while_browsing']))
        {
            if (($this->site_category) && ($this->configuration_data['default_display_order_while_browsing'] == 1000))
            {
                //the default display order is set on a category by category basis
                $this->category_order_sql_query = "select * from ".$this->categories_table." where category_id = ".$this->site_category;
                $category_order_result = $db->Execute($this->category_order_sql_query);
                if ($this->debug_browse) echo $this->category_order_sql_query."<br>\n";
                if (!$category_order_result)
                {
                    if ($this->debug_browse) echo $this->category_order_sql_query."<br>\n";
                    return false;
                }
                elseif ($category_order_result->RecordCount() == 1)
                {
                    $show_category_default = $category_order_result->FetchRow();
                    if ($show_category_default["default_display_order_while_browsing_category"])
                    {
                        if ($this->debug_browse) echo "using category specific order setting: ".$show_category_default["default_display_order_while_browsing_category"]."<bR>\n";
                        $browse_type = $show_category_default["default_display_order_while_browsing_category"];
                    }
                    else
                    {
                        if ($this->debug_browse) echo "problem is category order setting 1<Br>\n";
                        $browse_type = 0;
                    }
                }
                else
                {
                    //error....category does not exist
                    if ($this->debug_browse) echo "problem is category order setting 2<Br>\n";
                    $browse_type = 0;
                }

            }
            else
            {
                //use the site wide setting as the default browse by type
                if ($this->debug_browse) echo "using site wide order setting: ".$this->configuration_data['default_display_order_while_browsing']."<bR>\n";
                $browse_type = $this->configuration_data['default_display_order_while_browsing'];
            }
        }

        switch ($browse_type)
        {
            case 0: //nothing
                 $order_by = "order by better_placement desc,date desc ";
                break;
            case 1: //price desc
                $order_by = " order by price desc, current_bid desc, better_placement desc ";
                break;
            case 2: //price asc
                $order_by = " order by price asc, current_bid asc, better_placement desc ";
                break;
            case 3: //date earliest to latest
                $order_by = " order by date asc, better_placement desc ";
                break;
            case 4: //date latest to earliest
                $order_by = " order by date desc, better_placement desc ";
                break;
            case 5: //title asc
                $order_by = " order by title asc, better_placement desc ";
                break;
            case 6: //title desc
                $order_by = " order by title desc, better_placement desc ";
                break;
            case 7: //city asc
                $order_by = " order by location_city asc, better_placement desc ";
                break;
            case 8: //city desc
                $order_by = " order by location_city desc, better_placement desc ";
                break;
            case 9: //state asc
                $order_by = " order by location_state asc, better_placement desc ";
                break;
            case 10: //state desc
                $order_by = " order by location_state desc, better_placement desc ";
                break;
            case 11: //country asc
                $order_by = " order by location_country asc, better_placement desc ";
                break;
            case 12: //country desc
                $order_by = " order by location_country desc, better_placement desc ";
                break;
            case 13: //zip asc
                $order_by = " order by location_zip asc, better_placement desc ";
                break;
            case 14: //zip desc
                $order_by = " order by location_zip desc, better_placement desc ";
                break;
            case 15: //optional field 1 asc
                $order_by = " order by optional_field_1 asc, better_placement desc ";
                break;
            case 16: //optional field 1 desc
                $order_by = " order by optional_field_1 desc, better_placement desc ";
                break;
            case 17: //optional field 2 asc
                $order_by = " order by optional_field_2 asc, better_placement desc ";
                break;
            case 18: //optional field 2 desc
                $order_by = " order by optional_field_2 desc, better_placement desc ";
                break;
            case 19: //optional field 3 asc
                $order_by = " order by optional_field_3 asc, better_placement desc ";
                break;
            case 20: //optional field 3 desc
                $order_by = " order by optional_field_3 desc, better_placement desc ";
                break;
            case 21: //optional field 4 asc
                $order_by = " order by optional_field_4 asc, better_placement desc ";
                break;
            case 22: //optional field 4 desc
                $order_by = " order by optional_field_4 desc, better_placement desc ";
                break;
            case 23: //optional field 5 asc
                $order_by = " order by optional_field_5 asc, better_placement desc ";
                break;
            case 24: //optional field 5 desc
                $order_by = " order by optional_field_5 desc, better_placement desc ";
                break;
            case 25: //optional field 6 asc
                $order_by = " order by optional_field_6 asc, better_placement desc ";
                break;
            case 26: //optional field 6 desc
                $order_by = " order by optional_field_6 desc, better_placement desc ";
                break;
            case 27: //optional field 7 asc
                $order_by = " order by optional_field_7 asc, better_placement desc ";
                break;
            case 28: //optional field 7 desc
                $order_by = " order by optional_field_7 desc, better_placement desc ";
                break;
            case 29: //optional field 8 asc
                $order_by = " order by optional_field_8 asc, better_placement desc ";
                break;
            case 30: //optional field 8 desc
                $order_by = " order by optional_field_8 desc, better_placement desc ";
                break;
            case 31: //optional field 9 asc
                $order_by = " order by optional_field_9 asc, better_placement desc ";
                break;
            case 32: //optional field 9 desc
                $order_by = " order by optional_field_9 desc, better_placement desc ";
                break;
            case 33: //optional field 10 asc
                $order_by = " order by optional_field_10 asc, better_placement desc ";
                break;
            case 34: //optional field 10 desc
                $order_by = " order by optional_field_10 desc, better_placement desc ";
                break;
            case 35: //city asc
                $order_by = " order by location_city asc, better_placement desc ";
                break;
            case 36: //city desc
                $order_by = " order by location_city desc, better_placement desc ";
                break;
            case 37: //state asc
                $order_by = " order by location_state asc, better_placement desc ";
                break;
            case 38: //state desc
                $order_by = " order by location_state desc, better_placement desc ";
                break;
            case 39: //country asc
                $order_by = " order by location_country asc, better_placement desc ";
                break;
            case 40: //country desc
                $order_by = " order by location_country desc, better_placement desc ";
                break;
            case 41: //zip asc
                $order_by = " order by location_zip asc, better_placement desc ";
                break;
            case 42: //zip desc
                $order_by = " order by location_zip desc, better_placement desc ";
                break;
            case 43: //business_type asc
                $order_by = " order by business_type asc, better_placement desc ";
                break;
            case 44: //business_type desc
                $order_by = " order by business_type desc, better_placement desc ";
                break;
            case 45: //optional field 11 asc
                $order_by = " order by optional_field_11 asc, better_placement desc ";
                break;
            case 46: //optional field 11 desc
                $order_by = " order by optional_field_11 desc, better_placement desc ";
                break;
            case 47: //optional field 12 asc
                $order_by = " order by optional_field_12 asc, better_placement desc ";
                break;
            case 48: //optional field 12 desc
                $order_by = " order by optional_field_12 desc, better_placement desc ";
                break;
            case 49: //optional field 13 asc
                $order_by = " order by optional_field_13 asc, better_placement desc ";
                break;
            case 50: //optional field 13 desc
                $order_by = " order by optional_field_13 desc, better_placement desc ";
                break;
            case 51: //optional field 14 asc
                $order_by = " order by optional_field_14 asc, better_placement desc ";
                break;
            case 52: //optional field 14 desc
                $order_by = " order by optional_field_14 desc, better_placement desc ";
                break;
            case 53: //optional field 15 asc
                $order_by = " order by optional_field_15 asc, better_placement desc ";
                break;
            case 54: //optional field 15 desc
                $order_by = " order by optional_field_15 desc, better_placement desc ";
                break;
            case 55: //optional field 16 asc
                $order_by = " order by optional_field_16 asc, better_placement desc ";
                break;
            case 56: //optional field 16 desc
                $order_by = " order by optional_field_16 desc, better_placement desc ";
                break;
            case 57: //optional field 17 asc
                $order_by = " order by optional_field_17 asc, better_placement desc ";
                break;
            case 58: //optional field 17 desc
                $order_by = " order by optional_field_17 desc, better_placement desc ";
                break;
            case 59: //optional field 18 asc
                $order_by = " order by optional_field_18 asc, better_placement desc ";
                break;
            case 60: //optional field 18 desc
                $order_by = " order by optional_field_18 desc, better_placement desc ";
                break;
            case 61: //optional field 19 asc
                $order_by = " order by optional_field_19 asc, better_placement desc ";
                break;
            case 62: //optional field 19 desc
                $order_by = " order by optional_field_19 desc, better_placement desc ";
                break;
            case 63: //optional field 20 asc
                $order_by = " order by optional_field_20 asc, better_placement desc ";
                break;
            case 64: //optional field 20 desc
                $order_by = " order by optional_field_20 desc, better_placement desc ";
                break;
            default:
                $order_by = "order by better_placement desc,date desc ";
                break;
        }
        $this->sql_query_classifieds = $this->sql_query." and item_type = 1 ".$order_by." limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
        $result = $db->Execute($this->sql_query_classifieds);
        $this->sql_query_auctions = $this->sql_query." and item_type = 2 ".$order_by." limit ".(($this->page_result -1) * $this->configuration_data['number_of_ads_to_display']).",".$this->configuration_data['number_of_ads_to_display'];
        $result_auctions = $db->Execute($this->sql_query_auctions);

        if ($this->debug_browse) echo $this->sql_query." is the query<br>\n";
        $this->sql_query_count = "select count(id) as total from ".$this->classifieds_table." where
            category ".$this->in_statement." and live = 1 ";
        if ($this->filter_id)
            $this->sql_query_count .= $this->sql_filter_in_statement;
        if ($this->state_filter)
            $this->sql_query_count .= $this->sql_state_filter_statement;
        if (($this->zip_filter_distance) && ($this->zip_filter))
            $this->sql_query_count .= $this->sql_zip_filter_in_statement;
        $this->sql_query_count .= " group by item_type";
        if ($this->debug_browse)  echo $this->sql_query_count." at top<br>\n";
        if ($this->debug_browse) echo $this->sql_query."<Br>\n";
        if (!$result)
        {
            $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
            return false;
        }
        else
        {
            if ($this->sql_query_count)
            {
                $total_count_result = $db->Execute($this->sql_query_count);
                if ($this->debug_browse)  echo $this->sql_query_count." is the query<br>\n";
                if ($total_count_result)
                {
                    $show_total = $total_count_result->FetchRow();
                    $total_returned_ads = $show_total['total'];
                    $show_total = $total_count_result->FetchRow();
                    $total_returned_auctions = $show_total['total'];
                    if($total_returned_ads>$total_returned_auctions)
                        $total_returned = $total_returned_ads;
                    else
                        $total_returned = $total_returned_auctions;
                    if ($this->debug_browse) $total_returned." is the total returned<br>\n";
                }
            }
            //get this categories name
            if ($this->site_category)
            {
                if ($this->browse_type)
                {
                    $this->body .="<tr>\n\t<td  class=back_to_normal_browsing>
                        <a href=".$this->configuration_data['classifieds_url']."?a=5&b=".$this->site_category." class=back_to_normal_browsing>";
                    $this->body .= urldecode($this->messages[876])."</a></td>\n<tr>\n";
                }
                if ($this->get_category_configuration($db,$this->site_category))
                {
                    if (!$this->category_configuration['use_site_default'])
                    {
                        $this->browsing_configuration = $this->configuration_data;
                        if ($this->debug_browse)
                        {
                            echo "using site defaults<br>\n";
                            echo $this->browse_configuration["display_ad_description"]." is display_ad_description<BR>\n";
                        }
                    }
                    else
                    {
                        $this->browsing_configuration = $this->category_configuration;
                        if ($this->debug_browse)
                        {
                            echo "using category specific settings<br>\n";
                            echo $this->browse_configuration["display_ad_description"]." is display_ad_description<BR>\n";
                        }
                    }
                }
                else
                {
                    if ($this->debug_browse) echo "no category<br>\n";
                    $this->browsing_configuration = $this->configuration_data;
                }
                $category_name_result = $this->get_category_name($db,$this->site_category);
                $current_category_name = $category_name_result->CATEGORY_NAME;
                if (($this->category_configuration['cache_expire'] > $this->shifted_time($db)) && ($this->configuration_data['use_category_cache']) && ($this->category_configuration['cache_expire'] != 0) && (!$this->filter_id))
                {
                    //use the cache
                    if ($this->debug_browse)  echo "using category cache<br>\n";
                    $this->body .= $this->category_configuration['category_cache'];
                }
                else
                {
                    if ($this->configuration_data['display_category_tree'])
                    {
                        if (($this->configuration_data['category_tree_display'] == 1) || ($this->configuration_data['category_tree_display'] == 2) || ($this->configuration_data['category_tree_display'] == 0))
                        {
                            $category_tree = $this->get_category_tree($db,$this->site_category);
                            reset ($this->category_tree_array);
                            if ($category_tree)
                            {
                                //category tree
                                $this->current_category_tree .="<tr class=main>\n\t<td  height=20 class=browsing_category_tree>\n\t";
                                $this->current_category_tree .=urldecode($this->messages[680])." <a href=".$this->configuration_data['classifieds_file_name']."?a=5";
                                if ($browse_type) $this->current_category_tree .= "&c=".$browse_type;
                                $this->current_category_tree .= " class=main>".urldecode($this->messages[18])."</a> > ";
                                if (is_array($this->category_tree_array))
                                {
                                    $i = 0;
                                    //$categories = array_reverse($this->category_tree_array);
                                    $i = count($this->category_tree_array);
                                    while ($i > 0 )
                                    {
                                        //display all the categories
                                        $i--;
                                        if ($i == 0)
                                            $this->current_category_tree .=$this->category_tree_array[$i]["category_name"];
                                        else
                                        {
                                            $this->current_category_tree .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->category_tree_array[$i]["category_id"];
                                            if ($browse_type) $this->current_category_tree .= "&c=".$browse_type;
                                            $this->current_category_tree .= " class=browsing_category_tree>".$this->category_tree_array[$i]["category_name"]."</a> > ";
                                        }
                                    }
                                }
                                else
                                {
                                    $this->current_category_tree .=$category_tree;
                                }
                                $this->current_category_tree .="\n\t</td>\n</tr>\n";
                            }
                        }
                    }

                    $this->category_cache = "";
                    if ((($this->configuration_data['category_tree_display'] == 1) || ($this->configuration_data['category_tree_display'] == 2))
                        && ($this->configuration_data['display_category_tree']))
                        $this->category_cache .= $this->current_category_tree;

                    if ($this->configuration_data['display_category_navigation'])
                    {
                        //get the categories inside of this category
                        $this->sql_query = "select ".$this->categories_table.".category_id,
                            ".$this->categories_table.".category_image,
                            ".$this->categories_table.".category_count,
                            ".$this->categories_table.".auction_category_count,
                            ".$this->categories_languages_table.".category_name,
                            ".$this->categories_languages_table.".description
                            from ".$this->categories_table.",".$this->categories_languages_table." where
                            parent_id = ".$this->site_category." and
                            ".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
                            ".$this->categories_languages_table.".language_id = ".$this->language_id."
                            order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";
                        $category_result = $db->Execute($this->sql_query);
                        if ($this->debug_browse) echo $this->sql_query." is the query<br>\n";
                        if (!$category_result)
                        {
                            if ($this->debug_browse) echo $this->sql_query." is the query<br>\n";
                            $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
                            return false;
                        }
                        else
                        {

                            if ($category_result->RecordCount() > 0)
                            {
                                $this->category_cache .="<tr>\n\t<td  height=20>\n\t<table cellpadding=2 cellspacing=1 border=0 width=100%>\n\t";
                                switch ($this->configuration_data['number_of_browsing_subcategory_columns'])
                                {
                                    case 1: $column_width = "100%"; break;
                                    case 2: $column_width = "50%"; break;
                                    case 3: $column_width = "33%"; break;
                                    case 4: $column_width = "25%"; break;
                                    case 5: $column_width = "20%"; break;
                                } //end of switch
                                while ($show_category = $category_result->FetchRow())
                                {
                                    //display the sub categories of this category
                                    $this->category_cache .="<tr><td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
                                    if ($browse_type)
                                        $this->category_cache .="&c=".$browse_type;
                                    $this->category_cache .= ">";
                                    if (strlen(trim($show_category['category_image'])) > 0)
                                    {
                                        $this->category_cache .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
                                    }
                                    //$category_name = $this->get_category_name($db,$show_category['category_id']);
                                    $this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category['category_name']));
                                    {
                                        $category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
                                        $category_count['ad_count'] = $show_category['category_count'];
                                        $category_count['auction_count'] = $show_category['auction_category_count'];
                                        $this->category_cache .= "<font class=browsing_subcategory_count>";
                                        $this->category_cache .= $this->display_category_count($db, $show_category['category_id'], -1, 0, 0, $category_count);
                                        $this->category_cache .= "</font>";
                                    }
                                    $this->category_cache .= "</font>";
                                    if ($this->configuration_data['display_category_count'])
                                    if ($this->configuration_data['category_new_ad_limit'])
                                        $this->check_category_new_ad_icon_use($db,$show_category['category_id'],1);
                                    if ($this->configuration_data['display_category_description'])
                                        $this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category['description']))."</font>";
                                    $this->category_cache .="</td>";
                                    if ($this->configuration_data['number_of_browsing_subcategory_columns'] > 1)
                                    {
                                        if ($show_category = $category_result->FetchRow())
                                        {
                                            $this->category_cache .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
                                            if ($browse_type)
                                                $this->category_cache .= "&c=".$browse_type;
                                            $this->category_cache .= ">";
                                            if (strlen(trim($show_category['category_image'])) > 0)
                                            {
                                                $this->category_cache .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
                                            }
                                            //$category_name = $this->get_category_name($db,$show_category['category_id']);
                                            $this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category['category_name']));
                                            $this->category_cache .= "</font>";
                                            if ($this->configuration_data['display_category_count'])
                                            {
                                                $category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
                                                $category_count['ad_count'] = $show_category['category_count'];
                                                $category_count['auction_count'] = $show_category['auction_category_count'];
                                                $this->category_cache .="<font class=browsing_subcategory_count>";
                                                $this->category_cache .= $this->display_category_count($db, $show_category['category_id'], -1, 0, 0, $category_count);
                                                $this->category_cache .="</font>";
                                            }
                                            if ($this->configuration_data['category_new_ad_limit'])
                                                $this->check_category_new_ad_icon_use($db,$show_category['category_id'],1);
                                            if ($this->configuration_data['display_category_description'])
                                                $this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category['description']))."</font>";
                                            $this->category_cache .="</td>";
                                        }
                                        else
                                        {
                                            $this->category_cache .="<td  width=".$column_width.">&nbsp;</td>";
                                        }
                                        if ($this->configuration_data['number_of_browsing_subcategory_columns'] > 2)
                                        {
                                            if ($show_category = $category_result->FetchRow())
                                            {
                                                $this->category_cache .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
                                                if ($browse_type)
                                                    $this->category_cache .="&c=".$browse_type;
                                                $this->category_cache .= ">";
                                                if (strlen(trim($show_category['category_image'])) > 0)
                                                {
                                                    $this->category_cache .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
                                                }
                                                //$category_name = $this->get_category_name($db,$show_category['category_id']);
                                                $this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category['category_name']));
                                                $this->category_cache .= "</font>";
                                                if ($this->configuration_data['display_category_count'])
                                                {
                                                    $category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
                                                    $category_count['ad_count'] = $show_category['category_count'];
                                                    $category_count['auction_count'] = $show_category['auction_category_count'];
                                                    $this->category_cache .="<font class=browsing_subcategory_count>";
                                                    $this->category_cache .= $this->display_category_count($db, $show_category['category_id'], -1, 0, 0, $category_count);
                                                    $this->category_cache .="</font>";
                                                }
                                                if ($this->configuration_data['category_new_ad_limit'])
                                                    $this->check_category_new_ad_icon_use($db,$show_category['category_id'],1);
                                                if ($this->configuration_data['display_category_description'])
                                                    $this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category['description']))."</font>";
                                                $this->category_cache .="</td>";
                                            }
                                            else
                                            {
                                                $this->category_cache .="<td  width=".$column_width.">&nbsp;</td>";
                                            }
                                            if ($this->configuration_data['number_of_browsing_subcategory_columns'] > 3)
                                            {
                                                if ($show_category = $category_result->FetchRow())
                                                {
                                                    $this->category_cache .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
                                                    if ($browse_type)
                                                        $this->category_cache .="&c=".$browse_type;
                                                    $this->category_cache .= ">";
                                                    if (strlen(trim($show_category['category_image'])) > 0)
                                                    {
                                                        $this->category_cache .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
                                                    }
                                                    //$category_name = $this->get_category_name($db,$show_category['category_id']);
                                                    $this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category['category_name']));
                                                    $this->category_cache .= "</font>";
                                                    if ($this->configuration_data['display_category_count'])
                                                    {
                                                        $category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
                                                        $category_count['ad_count'] = $show_category['category_count'];
                                                        $category_count['auction_count'] = $show_category['auction_category_count'];
                                                        $this->category_cache .="<font class=browsing_subcategory_count>";
                                                        $this->category_cache .= $this->display_category_count($db, $show_category['category_id'], -1, 0, 0, $category_count);
                                                        $this->category_cache .="</font>";
                                                    }
                                                    if ($this->configuration_data['category_new_ad_limit'])
                                                        $this->check_category_new_ad_icon_use($db,$show_category['category_id'],1);
                                                    if ($this->configuration_data['display_category_description'])
                                                        $this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category['description']))."</font>";
                                                    $this->category_cache .="</td>";
                                                }
                                                else
                                                {
                                                    $this->category_cache .="<td  width=".$column_width.">&nbsp;</td>";
                                                }
                                                if ($this->configuration_data['number_of_browsing_subcategory_columns'] > 4)
                                                {
                                                    if ($show_category = $category_result->FetchRow())
                                                    {
                                                        $this->category_cache .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show_category['category_id'];
                                                        if ($browse_type)
                                                            $this->category_cache .="&c=".$browse_type;
                                                        $this->category_cache .= ">";
                                                        if (strlen(trim($show_category['category_image'])) > 0)
                                                        {
                                                            $this->category_cache .="<img src=\"".$show_category['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
                                                        }
                                                        //$category_name = $this->get_category_name($db,$show_category['category_id']);
                                                        $this->category_cache .="<font class=browsing_subcategory_name>".urldecode(stripslashes($show_category['category_name']));
                                                        $this->category_cache .= "</font>";
                                                        if ($this->configuration_data['display_category_count'])
                                                        {
                                                            $category_count['listing_count'] = $show_category['auction_category_count'] + $show_category['category_count'];
                                                            $category_count['ad_count'] = $show_category['category_count'];
                                                            $category_count['auction_count'] = $show_category['auction_category_count'];
                                                            $this->category_cache .="<font class=browsing_subcategory_count>";
                                                            $this->category_cache .= $this->display_category_count($db, $show_category['category_id'], -1, 0, 0, $category_count);
                                                            $this->category_cache .="</font>";
                                                        }
                                                        if ($this->configuration_data['category_new_ad_limit'])
                                                            $this->check_category_new_ad_icon_use($db,$show_category['category_id'],1);
                                                        if ($this->configuration_data['display_category_description'])
                                                            $this->category_cache .="</a><br><font class=browsing_subcategory_description>".urldecode(stripslashes($show_category['description']))."</font>";
                                                        $this->category_cache .="</td>";
                                                    }
                                                    else
                                                    {
                                                        $this->category_cache .="<td  width=".$column_width.">&nbsp;</td>";
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $this->category_cache .="</tr>";
                                }
                                $this->category_cache .="</table>\n\t</td>\n</tr>\n";
                            }
                            else
                            {
                                if ($this->configuration_data['display_no_subcategory_message'])
                                    $this->category_cache .="<tr class=no_subcategories_to>\n\t<td  height=20>\n\t".urldecode($this->messages[20])." ".$current_category_name."\n\t</td>\n</tr>\n";
                            }

                            if ($this->configuration_data['use_category_cache'])
                            {
                                $recache_time = $this->shifted_time($db) + (3600 * $this->configuration_data['use_category_cache']);
                                $this->sql_query = "update ".$this->categories_languages_table." set
                                    category_cache = \"".addslashes(urlencode($this->category_cache))."\",
                                    cache_expire = \"".$recache_time."\"
                                    where category_id = ".$this->site_category." and language_id = ".$this->language_id;
                                if ($this->debug_browse) echo $this->sql_query." is the query<br>\n";
                                $cache_result = $db->Execute($this->sql_query);
                                if (!$cache_result)
                                {
                                    if ($this->debug_browse) echo $this->sql_query." is the query<br>\n";
                                    $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
                                    return false;
                                }
                            }
                        }
                    }
                    if (($this->configuration_data['category_tree_display'] == 0) || ($this->configuration_data['category_tree_display'] == 2))
                        $this->category_cache .= $this->current_category_tree;
                    $this->body .= $this->category_cache;
                }

                /*
                //commented out as this is a duplicate to the code used immediately after this
                //that checks whether auctions or classifieds are able to be placed within this specific category
                if($this->is_classifieds()||$this->is_class_auctions())
                {
                    //featured ads
                    if (($this->configuration_data['use_featured_feature']) && ($this->page_result == 1) && ($this->configuration_data['number_of_featured_ads_to_display']))
                    {
                        //check to see if there are any featured ads
                        $seed = rand();
                        //$this->sql_query = "select * from ".$this->classifieds_table.",".$this->images_urls_table." where
                        //    ".$this->classifieds_table.".id = ".$this->images_urls_table.".classified_id and ".$this->images_urls_table.".display_order = 1 ";

                        if ($this->configuration_data['display_sub_category_ads'])
                        {
                            $this->get_sql_in_statement($db,$this->site_category);
                        }
                        else
                        {
                            $this->in_statement = " in (".$this->site_category.") ";
                        }

                        $this->sql_query = "select * from ".$this->classifieds_table." where (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) and live = 1 and category ".$this->in_statement." and item_type = 1 ";
                        //$this->sql_query .= "and featured_ad = 1 and live = 1 and category ".$this->in_statement." ";
                        if ($this->filter_id)
                            $this->sql_query .= $this->sql_filter_in_statement." ";
                        if ($this->state_filter)
                            $this->sql_query .= $this->sql_state_filter_statement;
                        if (($this->zip_filter_distance) && ($this->zip_filter))
                            $this->sql_query .= $this->sql_zip_filter_in_statement;
                        //$this->sql_query .= $order_by." rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($browse_type)
                            $this->sql_query .= $order_by." limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        else
                            $this->sql_query .= " order by rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($this->debug_browse) echo $this->sql_query." - featured 1<br>\n";
                        $featured_result = $db->Execute($this->sql_query);
                        if (!$featured_result)
                        {
                            if ($this->debug_browse)
                            {
                                echo $this->sql_query."<br>\n";
                                echo $db->ErrorMsg()."<Br>\n";
                            }
                            $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
                            return false;
                        }
                        elseif ($featured_result->RecordCount() > 0)
                        {
                            $this->body .="<tr class=featured_ad_title>\n\t<td >".urldecode($this->messages[28])." ".$current_category_name;
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=9&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[873])."</a> ";
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=8&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[872])."</a> ";
                            $this->body .= "</td>\n</tr>\n";
                            $this->body .="<tr>\n\t<td><table cellpadding=1 cellspacing=0 border=0 width=100%>";
                            $this->display_browse_result($db,$featured_result,"browsing_result_table_header",1);
                            $this->body .="</table></td>\n</tr>\n";
                        }
                    }

                    if ($result->RecordCount() > 0)
                        $this->body .="<tr class=normal_results_header>\n\t<td>".urldecode($this->messages[200109])." ".$current_category_name."</td></tr>";
                    $result->Move(0);
                    $this->display_browse_result($db,$result,"browsing_result_table_header");
                }
                if($this->is_auctions()||$this->is_class_auctions())
                {
                    //featured auctions
                    if (($this->configuration_data['use_featured_feature']) && ($this->page_result == 1) && ($this->configuration_data['number_of_featured_ads_to_display']))
                    {
                        //check to see if there are any featured ads
                        $seed = rand();
                        //$this->sql_query = "select * from ".$this->classifieds_table.",".$this->images_urls_table." where
                        //    ".$this->classifieds_table.".id = ".$this->images_urls_table.".classified_id and ".$this->images_urls_table.".display_order = 1 ";

                        if ($this->configuration_data['display_sub_category_ads'])
                        {
                            $this->get_sql_in_statement($db,$this->site_category);
                        }
                        else
                        {
                            $this->in_statement = " in (".$this->site_category.") ";
                        }

                        $this->sql_query = "select * from ".$this->classifieds_table." where (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) and live = 1 and category ".$this->in_statement." and item_type = 2 ";
                        //$this->sql_query .= "and featured_ad = 1 and live = 1 and category ".$this->in_statement." ";
                        if ($this->filter_id)
                            $this->sql_query .= $this->sql_filter_in_statement." ";
                        if ($this->state_filter)
                            $this->sql_query .= $this->sql_state_filter_statement;
                        if (($this->zip_filter_distance) && ($this->zip_filter))
                            $this->sql_query .= $this->sql_zip_filter_in_statement;
                        //$this->sql_query .= $order_by." rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($browse_type)
                            $this->sql_query .= $order_by." limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        else
                            $this->sql_query .= " order by rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($this->debug_browse) echo $this->sql_query." - featured 2<br>\n";
                        $featured_result = $db->Execute($this->sql_query);
                        if (!$featured_result)
                        {
                            if ($this->debug_browse)
                            {
                                echo $this->sql_query."<br>\n";
                                echo $db->ErrorMsg()."<Br>\n";
                            }
                            $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
                            return false;
                        }
                        elseif ($featured_result->RecordCount() > 0)
                        {
                            $this->body .="<tr class=featured_ad_title>\n\t<td >".urldecode($this->messages[100028])." ".$current_category_name;
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=9&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[873])."</a> ";
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=8&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[872])."</a> ";
                            $this->body .= "</td>\n</tr>\n";
                            $this->body .="<tr>\n\t<td><table cellpadding=1 cellspacing=0 border=0 width=100%>";
                            $this->display_browse_result($db,$featured_result,"browsing_result_table_header",1,1);
                            $this->body .="</table></td>\n</tr>\n";
                        }
                    }
                    if ($result_auctions->RecordCount() > 0)
                        $this->body .="<tr class=normal_results_header>\n\t<td>".urldecode($this->messages[200110])." ".$current_category_name."</td></tr>";
                    $this->display_browse_result($db,$result_auctions,"browsing_result_table_header",0,1);
                }
                */

                if ($this->debug_browse)
                {
                    echo $this->is_class_auctions()." is is_class_auctions()<bR>\n";
                    echo $this->is_classifieds()." is is_classifieds()<bR>\n";
                    echo $this->category_configuration['listing_types_allowed']." is listing_types_allowed<BR>\n";
                }

                if (((($this->category_configuration['listing_types_allowed'] == 0) || ($this->category_configuration['listing_types_allowed'] == 1))  && ($this->is_class_auctions()))
                    || ($this->is_classifieds()))
                {
                    if ($this->debug_browse)
                    {
                        echo "displaying classfieds<BR>\n";
                    }
                    //featured ads
                    if (($this->configuration_data['use_featured_feature']) && ($this->page_result == 1) && ($this->configuration_data['number_of_featured_ads_to_display']))
                    {
                        //check to see if there are any featured ads
                        $seed = rand();
                        //$this->sql_query = "select * from ".$this->classifieds_table.",".$this->images_urls_table." where
                        //    ".$this->classifieds_table.".id = ".$this->images_urls_table.".classified_id and ".$this->images_urls_table.".display_order = 1 ";

                        if ($this->configuration_data['display_sub_category_ads'])
                        {
                            $this->get_sql_in_statement($db,$this->site_category);
                        }
                        else
                        {
                            $this->in_statement = " in (".$this->site_category.") ";
                        }

                        $this->sql_query = "select * from ".$this->classifieds_table." where (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) and live = 1 and category ".$this->in_statement." and item_type = 1 ";
                        //$this->sql_query .= "and featured_ad = 1 and live = 1 and category ".$this->in_statement." ";
                        if ($this->filter_id)
                            $this->sql_query .= $this->sql_filter_in_statement." ";
                        if ($this->state_filter)
                            $this->sql_query .= $this->sql_state_filter_statement;
                        if (($this->zip_filter_distance) && ($this->zip_filter))
                            $this->sql_query .= $this->sql_zip_filter_in_statement;
                        //$this->sql_query .= $order_by." rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($browse_type)
                            $this->sql_query .= $order_by." limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        else
                            $this->sql_query .= " order by rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($this->debug_browse) echo $this->sql_query." - featured 3<br>\n";
                        $featured_result = $db->Execute($this->sql_query);
                        if (!$featured_result)
                        {
                            if ($this->debug_browse)
                            {
                                echo $this->sql_query."<br>\n";
                                echo $db->ErrorMsg()."<Br>\n";
                            }
                            $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
                            return false;
                        }
                        elseif ($featured_result->RecordCount() > 0)
                        {
                            $this->body .="<tr class=featured_ad_title>\n\t<td >".urldecode($this->messages[28])." ".$current_category_name;
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=9&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[873])."</a> ";
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=8&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[872])."</a> ";
                            $this->body .= "</td>\n</tr>\n";
                            $this->body .="<tr>\n\t<td><table cellpadding=1 cellspacing=0 border=0 width=100%>";
                            if ($this->debug_browse) echo "about to display the featured ads<bR>\n";
                            $this->display_browse_result($db,$featured_result,"browsing_result_table_header",1);
                            $this->body .="</table></td>\n</tr>\n";
                        }
                    }

                    //display the classified ads in this category
                    if ($result->RecordCount() > 0)
                    {
                        $this->body .="<tr class=normal_results_header>\n\t<td>".urldecode($this->messages[200109])." ".$current_category_name."</td></tr>";
                        $result->Move(0);
                        $this->display_browse_result($db,$result,"browsing_result_table_header");
                    }
                    else
                    {
                        $this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[17])."\n\t</td>\n</tr>\n";
                    }
                }

                if(((($this->category_configuration['listing_types_allowed'] == 0) || ($this->category_configuration['listing_types_allowed'] == 2))  && ($this->is_class_auctions()))
                    || ($this->is_auctions()))
                {
                    if ($this->debug_browse)
                    {
                        echo "displaying auctions<BR>\n";
                    }
                    //featured auctions
                    if (($this->configuration_data['use_featured_feature']) && ($this->page_result == 1) && ($this->configuration_data['number_of_featured_ads_to_display']))
                    {
                        //check to see if there are any featured ads
                        $seed = rand();
                        //$this->sql_query = "select * from ".$this->classifieds_table.",".$this->images_urls_table." where
                        //    ".$this->classifieds_table.".id = ".$this->images_urls_table.".classified_id and ".$this->images_urls_table.".display_order = 1 ";

                        if ($this->configuration_data['display_sub_category_ads'])
                        {
                            $this->get_sql_in_statement($db,$this->site_category);
                        }
                        else
                        {
                            $this->in_statement = " in (".$this->site_category.") ";
                        }

                        $this->sql_query = "select * from ".$this->classifieds_table." where (featured_ad = 1 or featured_ad_2 = 1 or featured_ad_3 = 1 or featured_ad_4 = 1 or featured_ad_5 = 1) and live = 1 and category ".$this->in_statement." and item_type = 2 ";
                        //$this->sql_query .= "and featured_ad = 1 and live = 1 and category ".$this->in_statement." ";
                        if ($this->filter_id)
                            $this->sql_query .= $this->sql_filter_in_statement." ";
                        if ($this->state_filter)
                            $this->sql_query .= $this->sql_state_filter_statement;
                        if (($this->zip_filter_distance) && ($this->zip_filter))
                            $this->sql_query .= $this->sql_zip_filter_in_statement;
                        //$this->sql_query .= $order_by." rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($browse_type)
                            $this->sql_query .= $order_by." limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        else
                            $this->sql_query .= " order by rand(".$seed.") limit ".$this->configuration_data['number_of_featured_ads_to_display'];
                        if ($this->debug_browse) echo $this->sql_query." feature 4<br>\n";
                        $featured_result = $db->Execute($this->sql_query);
                        if (!$featured_result)
                        {
                            if ($this->debug_browse)
                            {
                                echo $this->sql_query."<br>\n";
                                echo $db->ErrorMsg()."<Br>\n";
                            }
                            $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
                            return false;
                        }
                        elseif ($featured_result->RecordCount() > 0)
                        {
                            $this->body .="<tr class=featured_ad_title>\n\t<td >".urldecode($this->messages[100028])." ".$current_category_name;
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=9&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[873])."</a> ";
                            $this->body .= " <a href=".$this->configuration_data['classifieds_file_name']."?a=8&b=".$this->site_category."&c=".$browse_type." class=featured_ads_links>".urldecode($this->messages[872])."</a> ";
                            $this->body .= "</td>\n</tr>\n";
                            $this->body .="<tr>\n\t<td><table cellpadding=1 cellspacing=0 border=0 width=100%>";
                            if ($this->debug_browse) echo "about to display the featured auctions<bR>\n";
                            $this->display_browse_result($db,$featured_result,"browsing_result_table_header",1,1);
                            $this->body .="</table></td>\n</tr>\n";
                        }
                    }

                    //display the auctions in this category
                    if ($result_auctions->RecordCount() > 0)
                    {
                        $this->body .="<tr class=normal_results_header>\n\t<td>".urldecode($this->messages[200110])." ".$current_category_name."</td></tr>";
                        $result_auctions->Move(0);
                        $this->display_browse_result($db,$result_auctions,"browsing_result_table_header",0,1);
                    }
                    else
                    {
                        $this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[100017])."\n\t</td>\n</tr>\n";
                    }
                }


                if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
                {
                    //display the link to the next 10
                    $number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
                    $this->body .="<tr class=browsing_result_page_links>\n\t<td ><font class=more_results>".urldecode($this->messages[25])." ".$this->page_result."</font><font class=page_of>".urldecode($this->messages[26])."</font> <font class=more_results>".ceil($total_returned / $this->configuration_data['number_of_ads_to_display'])."</font></td>\n</tr>\n";
                }

                if ($this->debug_browse)
                {
                    echo $total_returned." is total_returned<bR>\n";
                    echo $this->configuration_data['number_of_ads_to_display']." is number_of_ads_to_display<br>\n";
                }

                if ($this->configuration_data['number_of_ads_to_display'] < $total_returned)
                {
                    //display the link to the next 10
                    $number_of_page_results = ceil($total_returned / $this->configuration_data['number_of_ads_to_display']);
                    $this->body .="<tr class=more_results>\n\t<td >".urldecode($this->messages[24])." ";
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
                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$i;
                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                $this->body .=" class=browsing_result_page_links>".$i."</a> ";
                            }
                        }
                    }
                    elseif($number_of_page_results < 100)
                    {
                        $number_of_sections =  ceil($number_of_page_results/10);
                        for ($section = 0;$section < $number_of_sections;$section++)
                        {
                            if (($this->page_result > ($section * 10)) && ($this->page_result <= (($section+1) * 10)))
                            {
                                //display the individual pages within this section
                                for ($page = (($section * 10) + 1);$page <= (($section+1) * 10);$page++)
                                {
                                    if($page == $this->page_result)
                                    {
                                        $this->body .=" <b>".$page."</b> ";
                                        continue;
                                    }
                                    if ($page <= $number_of_page_results)
                                    {
                                        $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$page;
                                        if ($browse_type) $this->body .= "&c=".$browse_type;
                                        $this->body .= " class=browsing_result_page_links>".$page."</a> ";
                                    }
                                }

                            }
                            else
                            {
                                //display the link to the section
                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".(($section*10)+1);
                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                $this->body .= " class=browsing_result_page_links>".(($section*10)+1)."</a>";
                            }
                            if (($section+1) < $number_of_sections)
                                $this->body .= " <font class=browsing_result_page_links>..</font> ";
                        }
                    }
                    else
                    {
                        $number_of_sections =  ceil($number_of_page_results/100);
                        for ($section = 0;$section < $number_of_sections;$section++)
                        {
                            if (($this->page_result > ($section * 100)) && ($this->page_result <= (($section+1) * 100)))
                            {
                                //display tens
                                for ($page = (($section * 100) + 1);$page <= (($section+1) * 100);$page+=10)
                                {

                                    if (($this->page_result >= $page) && ($this->page_result <= ($page+9)))
                                    {
                                        //display ones
                                        for ($page_link = $page;$page_link <= ($page+9);$page_link++)
                                        {
                                            if($page_link == $this->page_result)
                                            {
                                                $this->body .=" <b>".$page_link."</b> ";
                                                continue;
                                            }
                                            if ($page_link <= $number_of_page_results)
                                            {
                                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$page_link;
                                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                                $this->body .= " class=browsing_result_page_links>".$page_link."</a> ";
                                            }
                                        }

                                    }else
                                    {
                                        if($page == $this->page_result)
                                        {
                                            $this->body .=" <b>".$page."</b> ";
                                            continue;
                                        }
                                        if ($page <= $number_of_page_results)
                                        {
                                            $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$page;
                                            if ($browse_type) $this->body .= "&c=".$browse_type;
                                            $this->body .= " class=browsing_result_page_links>".$page."</a> ";
                                            if (($section+1) < $number_of_sections)
                                                $this->body .= " <font class=browsing_result_page_links>..</font> ";
                                        }
                                    }
                                }

                            }
                            else
                            {
                                //display hundreds
                                $this->body .="<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".(($section*100)+1);
                                if ($browse_type) $this->body .= "&c=".$browse_type;
                                    $this->body .= " class=browsing_result_page_links>".(($section*100)+1)."</a>";
                                if (($section+1) < $number_of_sections)
                                    $this->body .= " <font class=browsing_result_page_links>..</font> ";
                            }
                        }
                    }
                    $this->body .="</td>\n</tr>\n";
                }
            }
            else
            {
                if (!$this->browse_main($db))
                {
                    $this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
                    return false;
                }
                else
                {
                    return true;
                }
            }
        }
        $this->body .="</table>\n";
        $this->display_page($db);
        return true;
    } //end of function browse

//####################################################################################


	function display_browse_result($db,$browse_result,$header_css,$featured=0,$auction=0)
	{
		if ($browse_result->RecordCount() > 0)
		{
			$browse_result->Move(0);
			$link_text = "<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category."&page=".$this->page_result."&c=";
			//display the ads inside of this category
			$this->body .="<tr>\n\t<td  height=20>\n\t";
			$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
			$this->body .="<tr class=".$header_css.">\n\t\t";
			if ($this->browsing_configuration['display_business_type'])
			{
				$this->body .="<td class=business_type_column_header nowrap>".$link_text;
				if ($this->browse_type == 43) $this->body .= "44";
				elseif ($this->browse_type == 44) $this->body .= "0";
				else $this->body .= "43";
				$this->body .= " class=business_type_column_header>".urldecode($this->messages[1262])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_photo_icon'])
				$this->body .= "<td  class=photo_column_header nowrap>".urldecode($this->messages[23])."</td>\n\t";
			if ($this->browsing_configuration['display_ad_title'])
			{
				$this->body .="<td  class=title_column_header ";
				if ((($this->browsing_configuration['display_ad_description'])&& ($this->browsing_configuration['display_ad_description_where'])) || (!$this->browsing_configuration['display_ad_description']))
					$this->body .= "width=100%";
				$this->body .= ">".$link_text;
				if ($this->browse_type == 5) $this->body .= "6";
				elseif ($this->browse_type == 6) $this->body .= "0";
				else $this->body .= "5";
				$this->body .= " class=title_column_header>".urldecode($this->messages[19])."</a>";
				if (($this->browsing_configuration['display_ad_description'])&& ($this->browsing_configuration['display_ad_description_where']))
					$this->body .="<br>".urldecode($this->messages[21]);

				$this->body .="</td>\n\t\t";
			}

			if (($this->browsing_configuration['display_ad_description'])&& (!$this->browsing_configuration['display_ad_description_where']))
				$this->body .="<td   class=description_column_header>".urldecode($this->messages[21])."</td>\n\t";

			if ($this->browsing_configuration['display_optional_field_1'])
			{
				$this->body .="<td class=optional_field_header_1 nowrap>".$link_text;
				if ($this->browse_type == 15) $this->body .= "16";
				elseif ($this->browse_type == 16) $this->body .= "0";
				else $this->body .= "15";
				$this->body .= " class=optional_field_header_1>".urldecode($this->messages[922])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_2'])
			{
				$this->body .="<td class=optional_field_header_2 nowrap>".$link_text;
				if ($this->browse_type == 17) $this->body .= "18";
				elseif ($this->browse_type == 18) $this->body .= "0";
				else $this->body .= "17";
				$this->body .= " class=optional_field_header_2>".urldecode($this->messages[923])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_3'])
			{
				$this->body .="<td class=optional_field_header_3 nowrap>".$link_text;
				if ($this->browse_type == 19) $this->body .= "20";
				elseif ($this->browse_type == 20) $this->body .= "0";
				else $this->body .= "19";
				$this->body .= " class=optional_field_header_3>".urldecode($this->messages[924])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_4'])
			{
				$this->body .="<td class=optional_field_header_4 nowrap>".$link_text;
				if ($this->browse_type == 21) $this->body .= "22";
				elseif ($this->browse_type == 22) $this->body .= "0";
				else $this->body .= "21";
				$this->body .= " class=optional_field_header_4>".urldecode($this->messages[925])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_5'])
			{
				$this->body .="<td class=optional_field_header_5 nowrap>".$link_text;
				if ($this->browse_type == 23) $this->body .= "24";
				elseif ($this->browse_type == 24) $this->body .= "0";
				else $this->body .= "23";
				$this->body .= " class=optional_field_header_5>".urldecode($this->messages[926])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_6'])
			{
				$this->body .="<td class=optional_field_header_6 nowrap>".$link_text;
				if ($this->browse_type == 25) $this->body .= "26";
				elseif ($this->browse_type == 26) $this->body .= "0";
				else $this->body .= "25";
				$this->body .= " class=optional_field_header_6>".urldecode($this->messages[927])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_7'])
			{
				$this->body .="<td class=optional_field_header_7 nowrap>".$link_text;
				if ($this->browse_type == 27) $this->body .= "28";
				elseif ($this->browse_type == 28) $this->body .= "0";
				else $this->body .= "27";
				$this->body .= " class=optional_field_header_7>".urldecode($this->messages[928])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_8'])
			{
				$this->body .="<td class=optional_field_header_8 nowrap>".$link_text;
				if ($this->browse_type == 29) $this->body .= "30";
				elseif ($this->browse_type == 30) $this->body .= "0";
				else $this->body .= "29";
				$this->body .= " class=optional_field_header_8>".urldecode($this->messages[929])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_9'])
			{
				$this->body .="<td class=optional_field_header_9 nowrap>".$link_text;
				if ($this->browse_type == 31) $this->body .= "32";
				elseif ($this->browse_type == 32) $this->body .= "0";
				else $this->body .= "31";
				$this->body .= " class=optional_field_header_9>".urldecode($this->messages[930])."</a></td>\n\t";
			}
			if ($this->browsing_configuration['display_optional_field_10'])
			{
				$this->body .="<td class=optional_field_header_10 nowrap>".$link_text;
				if ($this->browse_type == 33) $this->body .= "34";
				elseif ($this->browse_type == 34) $this->body .= "0";
				else $this->body .= "33";
				$this->body .= " class=optional_field_header_10>".urldecode($this->messages[931])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_11'])
			{
				$this->body .="<td class=optional_field_header_11 nowrap>".$link_text;
				if ($this->browse_type == 45) $this->body .= "46";
				elseif ($this->browse_type == 46) $this->body .= "0";
				else $this->body .= "45";
				$this->body .= " class=optional_field_header_11>".urldecode($this->messages[1696])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_12'])
			{
				$this->body .="<td class=optional_field_header_12 nowrap>".$link_text;
				if ($this->browse_type == 47) $this->body .= "48";
				elseif ($this->browse_type == 48) $this->body .= "0";
				else $this->body .= "47";
				$this->body .= " class=optional_field_header_12>".urldecode($this->messages[1697])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_13'])
			{
				$this->body .="<td class=optional_field_header_13 nowrap>".$link_text;
				if ($this->browse_type == 49) $this->body .= "50";
				elseif ($this->browse_type == 50) $this->body .= "0";
				else $this->body .= "49";
				$this->body .= " class=optional_field_header_13>".urldecode($this->messages[1698])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_14'])
			{
				$this->body .="<td class=optional_field_header_14 nowrap>".$link_text;
				if ($this->browse_type == 51) $this->body .= "52";
				elseif ($this->browse_type == 52) $this->body .= "0";
				else $this->body .= "51";
				$this->body .= " class=optional_field_header_14>".urldecode($this->messages[1699])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_15'])
			{
				$this->body .="<td class=optional_field_header_15 nowrap>".$link_text;
				if ($this->browse_type == 53) $this->body .= "54";
				elseif ($this->browse_type == 54) $this->body .= "0";
				else $this->body .= "53";
				$this->body .= " class=optional_field_header_15>".urldecode($this->messages[1700])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_16'])
			{
				$this->body .="<td class=optional_field_header_16 nowrap>".$link_text;
				if ($this->browse_type == 55) $this->body .= "56";
				elseif ($this->browse_type == 56) $this->body .= "0";
				else $this->body .= "55";
				$this->body .= " class=optional_field_header_16>".urldecode($this->messages[1701])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_17'])
			{
				$this->body .="<td class=optional_field_header_17 nowrap>".$link_text;
				if ($this->browse_type == 57) $this->body .= "58";
				elseif ($this->browse_type == 58) $this->body .= "0";
				else $this->body .= "57";
				$this->body .= " class=optional_field_header_17>".urldecode($this->messages[1702])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_18'])
			{
				$this->body .="<td class=optional_field_header_18 nowrap>".$link_text;
				if ($this->browse_type == 59) $this->body .= "60";
				elseif ($this->browse_type == 60) $this->body .= "0";
				else $this->body .= "59";
				$this->body .= " class=optional_field_header_18>".urldecode($this->messages[1703])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_19'])
			{
				$this->body .="<td class=optional_field_header_19 nowrap>".$link_text;
				if ($this->browse_type == 61) $this->body .= "62";
				elseif ($this->browse_type == 62) $this->body .= "0";
				else $this->body .= "61";
				$this->body .= " class=optional_field_header_19>".urldecode($this->messages[1704])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_optional_field_20'])
			{
				$this->body .="<td class=optional_field_header_20 nowrap>".$link_text;
				if ($this->browse_type == 63) $this->body .= "64";
				elseif ($this->browse_type == 64) $this->body .= "0";
				else $this->body .= "63";
				$this->body .= " class=optional_field_header_20>".urldecode($this->messages[1705])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_browsing_city_field'])
			{
				$this->body .="<td class=city_column_header nowrap>".$link_text;
				if ($this->browse_type == 7) $this->body .= "8";
				elseif ($this->browse_type == 8) $this->body .= "0";
				else $this->body .= "7";
				$this->body .= " class=city_column_header>".urldecode($this->messages[1199])."</a></td>\n\t";
			}

			if ($this->browsing_configuration['display_browsing_state_field'])
			{
				$this->body .="<td class=state_column_header nowrap>".$link_text;
				if ($this->browse_type == 37) $this->body .= "38";
				elseif ($this->browse_type == 38) $this->body .= "0";
				else $this->body .= "37";
				$this->body .= " class=state_column_header>".urldecode($this->messages[1200])."</td>\n\t";
			}
			if ($this->browsing_configuration['display_browsing_country_field'])
			{
				$this->body .="<td class=country_column_header nowrap>".$link_text;
				if ($this->browse_type == 39) $this->body .= "40";
				elseif ($this->browse_type == 40) $this->body .= "0";
				else $this->body .= "39";
				$this->body .= " class=country_column_header>".urldecode($this->messages[1201])."</td>\n\t";
			}
			if ($this->browsing_configuration['display_browsing_zip_field'])
			{
				$this->body .="<td class=zip_column_header nowrap>".$link_text;
				if ($this->browse_type == 41) $this->body .= "42";
				elseif ($this->browse_type == 42) $this->body .= "0";
				else $this->body .= "41";
				$this->body .= " class=zip_column_header>".urldecode($this->messages[1202])."</td>\n\t";
			}

///!!!!!!!! Original string: $auction && $this->browsing_configuration['display_number_bids']
			if ($this->browsing_configuration['display_number_bids'])
			{
				$this->body .= "<td class=number_bids_header nowrap>".urldecode($this->messages[103041])."</td>\n\t";
			}

			if ($this->browsing_configuration['display_price'])
			{
				$this->body .="<td  class=price_column_header nowrap>".$link_text;
				//$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$this->site_category;
				if ($this->browse_type == 1) $this->body .= "2";
				elseif ($this->browse_type == 2) $this->body .= "0";
				else $this->body .= "1";
				$this->body .= " class=price_column_header>".urldecode($this->messages[27])."</td>\n\t";
			}

			if ($this->browsing_configuration['display_entry_date']
				&& (!$auction || ($this->browsing_configuration['auction_entry_date'] && $auction)))
				$this->body .="<td  class=entry_date_column_header nowrap>".urldecode($this->messages[22])."</td>\n\t";

			if ($this->browsing_configuration['display_time_left']
				&& ($auction || ($this->browsing_configuration['classified_time_left'] && !$auction)))
			{
				$this->body .=  "<td class=time_left_header nowrap>".urldecode($this->messages[103008])."</td>\n\t";
			}

			//STOREFRONT CODE
			if(file_exists('classes/storefront/store_class.php'))
			{
				if ($this->browsing_configuration['display_storefront_link'])
				{
					$this->body .= "<td class=photo_column_header nowrap>".urldecode($this->messages[500003])."</td>\n\t";
				}
			}
			//STOREFRONT CODE

			if ($this->classified_user_id == 1)
			{
				//this is the admin
				$this->body .="<td>edit</td>\n\t";
				$this->body .="<td >delete</td>\n\t";
			}

			$this->body .="</tr>\n\t";

			//Display Data
			$this->row_count = 0;
			while ($show_classifieds = $browse_result->FetchRow())
			{
				if (($this->row_count % 2) == 0)
				{
					if ($show_classifieds['bolding'])
						$css_class_tag= "browsing_result_table_body_even_bold";
					else
						$css_class_tag=  "browsing_result_table_body_even ";
				}
				else
				{
					if ($show_classifieds['bolding'])
						$css_class_tag=  "browsing_result_table_body_odd_bold";
					else
						$css_class_tag=  "browsing_result_table_body_odd ";
				}
				$this->body .="<tr class=".$css_class_tag.">\n\t\t";
				if ($this->browsing_configuration['display_business_type'])
				{
					$this->body .="<td  nowrap align=center>";
					if ($show_classifieds['business_type'] == 1)
						$this->body .= urldecode($this->messages[1263]);
					elseif ($show_classifieds['business_type'] == 2)
						$this->body .= urldecode($this->messages[1263]);
					else
						$this->body .= "&nbsp;";
					$this->body .= "</td>\n\t";
				}

				if ($this->debug_browse)
				{
					echo $this->configuration_data['photo_or_icon']." is configuration_data[photo_or_icon]<br>\n";
					echo $show_classifieds['image']." is show_classifieds[image]<br>\n";
					echo $featured." is featured<bR>\n";
					echo $this->configuration_data['featured_thumbnail_max_width']." is featured_thumbnail_max_width<Br>\n";
					echo $this->configuration_data['featured_thumbnail_max_height']." is featured_thumbnail_max_height<Br>\n";
				}

				if ($this->browsing_configuration['display_photo_icon'])
				{
					if ($this->configuration_data['photo_or_icon'] == 1)
					{
						if ($show_classifieds['image'] > 0)
						{
							if ($featured)
								$this->display_thumbnail($db,$show_classifieds['id'],$this->configuration_data['featured_thumbnail_max_width'],$this->configuration_data['featured_thumbnail_max_height']);
							else
								$this->display_thumbnail($db,$show_classifieds['id']);
						}
						elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds['image']))
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td align=center><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
							$this->body .="<td>&nbsp;</td>\n\t";
					}
					else
					{
						if ($show_classifieds['image'] > 0)
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['photo_icon_url']." border=0></td>";
							}
						}
						elseif (($this->configuration_data['no_image_url']) && (!$show_classifieds['image']))
						{
							if (($this->configuration_data['popup_while_browsing'])
								&& ($this->configuration_data['popup_while_browsing_width'])
								&& ($this->configuration_data['popup_while_browsing_height']))
							{
								$this->body .= "<td align=center><a href=\"";
								$this->body .= $this->configuration_data['classifieds_file_name'];
								$this->body .= "?a=2&b=".$show_classifieds['id']."\" ";
								$this->body .= "onclick=\"window.open(this.href,'_blank','width=".$this->configuration_data['popup_while_browsing_width'].",height=".$this->configuration_data['popup_while_browsing_height'].",scrollbars=1,location=0,menubar=0,resizable=1,status=0'); return false;\" class=".$css_class_tag.">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
							else
							{
								$this->body .="<td align=center><a href=".$this->configuration_data['classifieds_file_name'];
								$this->body .="?a=2&b=".$show_classifieds['id'].">";
								$this->body .="<img src=".$this->configuration_data['no_image_url']." border=0></td>";
							}
						}
						else
							$this->body .="<td>&nbsp;</td>\n\t";
					}
				}
				if ($this->browsing_configuration['display_ad_title'])
				{
					$this->body .="<td ";
					if ((($this->browsing_configuration['display_ad_description'])&& ($this->browsing_configuration['display_ad_description_where'])) || (!$this->browsing_configuration['display_ad_description']))
						$this->body .= "width=100%";
					$this->body.= ">\n\t\t";
					if (($show_classifieds['sold_displayed']) && (strlen(trim($this->configuration_data['sold_image'])) >0))
						$this->body .= "<img src=".$this->configuration_data['sold_image']." border=0> ";
					if (($this->configuration_data['popup_while_browsing'])
						&& ($this->configuration_data['popup_while_browsing_width'])
						&& ($this->configuration_data['popup_while_browsing_height']))
						$this->body .= "<a href=\"javascript:winimage('".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']."','".$this->configuration_data['popup_while_browsing_width']."','".$this->configuration_data['popup_while_browsing_height']."')\" class=".$css_class_tag.">";
					else
						$this->body .= "<a href=".$this->configuration_data['classifieds_file_name']."?a=2&b=".$show_classifieds['id']." class=".$css_class_tag.">";
					$this->body .= stripslashes(urldecode($show_classifieds['title']))."</a>\n\t\t";
					if($show_classifieds['item_type'] == 2)
					{
						if ((strlen(trim($this->configuration_data['buy_now_image'])) >0))
						{
							$current_bid = $show_classifieds['current_bid'];
							$number_of_bids = $this->get_number_of_bids($db,$show_classifieds['id']);
							if (($show_classifieds['buy_now']!= 0) && (($show_classifieds['current_bid'] == 0) || ($this->configuration_data['buy_now_reserve'] && $show_classifieds['current_bid'] < $show_classifieds['reserve_price'])))
							{
								$this->body .= "<img src=".stripslashes($this->configuration_data['buy_now_image'])." border=0 hspace=2>";
							}
						}
						if (strlen(trim($this->configuration_data['reserve_met_image'])) >0)
						{
							if ( $show_classifieds['reserve_price'] != 0)
							{
								$current_bid = $show_classifieds['current_bid'];
								if ($current_bid >= $show_classifieds['reserve_price'])
								{
									$this->body .= "<img src=".stripslashes($this->configuration_data['reserve_met_image'])." border=0 hspace=2>";
								}
							}
						}
						if (strlen(trim($this->configuration_data['no_reserve_image'])) >0)
						{
							if ($show_classifieds['reserve_price'] == 0.00)
							{
								$this->body .= "<img src=".stripslashes($this->configuration_data['no_reserve_image'])." border=0 hspace=2>";
							}
						}
					}
					if ($show_classifieds['attention_getter'])
					{
						$this->body .= "<img src=\"".$show_classifieds['attention_getter_url']."\" border=0 hspace=2>";
					}
					if (($this->browsing_configuration['display_ad_description'])&& ($this->browsing_configuration['display_ad_description_where']))
					{
						$this->body .="<br>";
						if (!$this->browsing_configuration['display_all_of_description'] ||!$this->browsing_configuration['auctions_display_all_of_description'])
						{
							if (strlen(urldecode($show_classifieds['description'])) > $this->browsing_configuration['length_of_description'])
							{
								$small_string = substr(trim(strip_tags(stripslashes(urldecode($show_classifieds['description'])))),0,$this->browsing_configuration['length_of_description']);
								$position = strrpos($small_string," ");
								$smaller_string = substr($small_string,0,$position);
								$this->body .= $smaller_string."...";
							}
							else
								$this->body .=	stripslashes(urldecode($show_classifieds['description']));
						}
						else
							$this->body .=	stripslashes(urldecode($show_classifieds['description']));
					}
					$this->body .="</td>\n\t\t";
				}

				if (($this->browsing_configuration['display_ad_description'])&& (!$this->browsing_configuration['display_ad_description_where']))
				{
					echo
					$this->body .="<td >";
					if (!$this->browsing_configuration['display_all_of_description'])
					{
						if (strlen(urldecode($show_classifieds['description'])) > $this->browsing_configuration['length_of_description'])
						{
							$small_string = substr(trim(strip_tags(stripslashes(urldecode($show_classifieds['description'])))),0,$this->browsing_configuration['length_of_description']);
							$position = strrpos($small_string," ");
							$smaller_string = substr($small_string,0,$position);
							$this->body .=$smaller_string."...";
						}
						else
							$this->body .=	stripslashes(urldecode($show_classifieds['description']));
					}
					else
						$this->body .=	stripslashes(urldecode($show_classifieds['description']));
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_1'])
				{
					$this->body .="<td  nowrap align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_1']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_1']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_2'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_2']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_2']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_3'])
				{
					$this->body .="<td  nowrap align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_3']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_3']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_4'])
				{
					$this->body .="<td   nowrap align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_4']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_4']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_5'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_5']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_5']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_6'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_6']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_6']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_7'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_7']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_7']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_8'])
				{
					$this->body .="<td   nowrap align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_8']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_8']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_9'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_9']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_9']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_10'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_10']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_10']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_11'])
				{
					$this->body .="<td   nowrap align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_11']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_11']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_12'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_12']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_12']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_13'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_13']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_13']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_14'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_14']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_14']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_15'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_15']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_15']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_16'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_16']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_16']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_17'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_17']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_17']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_18'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_18']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_18']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_19'])
				{
					$this->body .="<td  nowrap align=center >";
						if (strlen(trim(urldecode($show_classifieds['optional_field_19']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_19']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_optional_field_20'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['optional_field_20']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['optional_field_20']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_browsing_city_field'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['location_city']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_city']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_browsing_state_field'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['location_state']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_state']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_browsing_country_field'])
				{
					$this->body .="<td  nowrap  align=center>";
						if (strlen(trim(urldecode($show_classifieds['location_country']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_country']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_browsing_zip_field'])
				{
					$this->body .="<td  nowrap align=center>";
						if (strlen(trim(urldecode($show_classifieds['location_zip']))) > 0)
							$this->body .=stripslashes(urldecode($show_classifieds['location_zip']));
						else
							$this->body .=	"-";
					$this->body .="</td>\n\t";
				}

				//if($show_classifieds['item_type'] == 2)
                if(1)
                {
					if ($this->browsing_configuration['display_number_bids'])
					{
						$this->body .= "<td nowrap align=center >".$this->get_number_of_bids($db,$show_classifieds['id'])." ".urldecode($this->messages[103042])."</td>";
					}
				}

				if (($show_classifieds['item_type'] == 1) && $this->browsing_configuration['display_price'])
				{
					$this->body .="<td  nowrap align=center>";
					if (((strlen(trim(urldecode($show_classifieds['price']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['precurrency']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['postcurrency']))) > 0)) && ($show_classifieds['price'] != 0))
					{
						if (floor($show_classifieds['price']) == $show_classifieds['price'])
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['price'])." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
						else
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['price'],2,".",",")." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
					}
					else
						$this->body .= stripslashes(urldecode($show_classifieds['precurrency']))." - ".stripslashes(urldecode($show_classifieds['postcurrency']));
					$this->body .="</td>\n\t";
				}
				elseif (($show_classifieds['item_type'] == 2) && $this->browsing_configuration['display_price'])
				{
					$this->body .="<td  nowrap align=center>";
					if (((strlen(trim(urldecode($show_classifieds['minimum_bid']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['precurrency']))) > 0)
						|| (strlen(trim(urldecode($show_classifieds['postcurrency']))) > 0)) && ($show_classifieds['minimum_bid'] != 0))
					{
						if (floor($show_classifieds['minimum_bid']) == $show_classifieds['minimum_bid'])
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['minimum_bid'])." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
						else
						{
							$this->body .= stripslashes(urldecode($show_classifieds['precurrency'])). " ".
								number_format($show_classifieds['minimum_bid'],2,".",",")." ".
								stripslashes(urldecode($show_classifieds['postcurrency']));
						}
					}
					else
						$this->body .=	stripslashes(urldecode($show_classifieds['precurrency']))." - ".stripslashes(urldecode($show_classifieds['postcurrency']));
					$this->body .="</td>\n\t";
				}

				if ($this->browsing_configuration['display_entry_date']
					&& ($show_classifieds['item_type'] == 1
						|| ($this->browsing_configuration['auction_entry_date']
							&& $show_classifieds['item_type'] == 2)))
					$this->body .="<td nowrap  align=center>".date(trim($this->configuration_data['entry_date_configuration']),$show_classifieds['date'])."</td>\n\t";

				if($this->browsing_configuration['display_time_left']
					&& (($show_classifieds['item_type'] == 2)
						|| ($this->browsing_configuration['classified_time_left'] && ($show_classifieds['item_type'] == 1))))
				{
					$weeks = $this->DateDifference(w,$this->shifted_time($db),$show_classifieds["ends"]);
					$remaining_weeks = ($weeks * 604800);

					// Find days left
					$days = $this->DateDifference(d,($this->shifted_time($db)+$remaining_weeks),$show_classifieds["ends"]);
					$remaining_days = ($days * 86400);

					// Find hours left
					$hours = $this->DateDifference(h,($this->shifted_time($db)+$remaining_days),$show_classifieds["ends"]);
					$remaining_hours = ($hours * 3600);

					// Find minutes left
					$minutes = $this->DateDifference(m,($this->shifted_time($db)+$remaining_hours),$show_classifieds["ends"]);
					$remaining_minutes = ($minutes * 60);

					// Find seconds left
					$seconds = $this->DateDifference(s,($this->shifted_time($db)+$remaining_minutes),$show_classifieds["ends"]);
					if(($weeks <= 0) && ($days <= 0) && ($hours <= 0) && ($minutes <= 0) && ($seconds <= 0))
					{
						// If closed we want to display closed text
						$this->body .=  "<td  nowrap align=center><div nowrap class=auction_closed>";
						$this->body .= urldecode($this->messages[100051])."</div>";
					}
					else
					{
						$this->body .=  "<td nowrap  align=center>";
					}
					if ($weeks > 0)
					{
						$this->body .= $weeks." ".stripslashes(urldecode($this->messages[103003])).", ".$days." ".stripslashes(urldecode($this->messages[103004]));
					}
					elseif ($days > 0)
					{
						$this->body .= $days." ".stripslashes(urldecode($this->messages[103004])).", ".$hours." ".stripslashes(urldecode($this->messages[103005]));
					}
					elseif ($hours > 0)
					{
						$this->body .= $hours." ".stripslashes(urldecode($this->messages[103005])).", ".$minutes." ".stripslashes(urldecode($this->messages[103006]));
					}
					elseif ($minutes > 0)
					{
						$this->body .= $minutes." ".stripslashes(urldecode($this->messages[103006])).", ".$seconds." ".stripslashes(urldecode($this->messages[103007]));
					}
					elseif ($seconds > 0)
					{
						$this->body .= $seconds." ".stripslashes(urldecode($this->messages[103007]));
					}
					$this->body .= "</td>\n\t";
				}

				//STOREFRONT CODE
				if(file_exists('classes/storefront/store_class.php'))
				{
					if ($this->browsing_configuration['display_storefront_link'])
					{
						include_once('classes/storefront/store_class.php');

						$this->sql_query = "select * from ".Store::get('storefront_subscriptions_table')." where user_id = ".$show_classifieds['seller'];
						$subscriptionResult = $db->Execute($this->sql_query);

						if($subscriptionResult->RecordCount()==1)
						{
							$subscriptionInfo = $subscriptionResult->FetchRow();
							$expiresAt = $this->shifted_time($db) + $subscriptionInfo["expiration"];
							if(time()<=$expiresAt)
							{
								$this->body .= "<td nowrap align=center>\n\t\t<a href=stores.php?store=".$show_classifieds['seller'].">".urldecode($this->messages[500004])."</a></td>";
							}else{
								$this->body .= "<td nowrap align=center>\n\t\t &nbsp;</td>";
							}
						}else
						{
							$this->body .= "<td nowrap align=center>\n\t\t &nbsp;</td>";
						}
					}
				}
				//STOREFRONT CODE

				if ($this->classified_user_id == 1)
				{
					//this is the admin
					$this->body .="<td align=center >\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=4&e=".$show_classifieds['id']."&b=5><img src=images/btn_user_edit.gif border=0></a>\n\t\t</td>\n\t";
					$this->body .="<td  align=center>\n\t\t<a href=".$this->configuration_data['classifieds_file_name']."?a=99&b=".$show_classifieds['id']."&c=".$this->site_category."><img src=images/btn_user_remove.gif border=0></a>\n\t\t</td>\n\t";

				}
				$this->body .="</tr>\n\t";
				$this->row_count++;
			} //end of while
			$this->body .="</table>\n\t</td>\n</tr>\n";
		}
		else
		{
			//no classifieds in this category
            if($_REQUEST['b'] == 'ViewAll') $this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[100017])."\n\t</td>\n</tr>\n";
			elseif(!$auction) $this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[17])."\n\t</td>\n</tr>\n";
            else $this->body .="<tr class=no_ads_in_category>\n\t<td >\n\t".urldecode($this->messages[100017])."\n\t</td>\n</tr>\n";

		}
		return;
	} //end of function display_browse_result

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function DateDifference ($interval, $date1,$date2)
	{
		$difference =  $date2 - $date1;
		switch ($interval)
		{
			case "w":
				$returnvalue  =$difference/604800;
				break;
			case "d":
				$returnvalue  = $difference/86400;
				break;
			case "h":
				$returnvalue = $difference/3600;
				break;
			case "m":
				$returnvalue  = $difference/60;
				break;
			case "s":
				$returnvalue  = $difference;
				break;
	    	}
	    	return intval($returnvalue);
	} //end of function DateDifference

//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	function browse_error ($db)
	{
		//this->error_message is the class variable that will contain the error message
		$this->page_id = 1;
		$this->get_text($db);
		if ($this->debug_browse) echo "browsing error<br>\n";
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".urldecode($this->messages[600])."</td>\n</tr>\n";
		$this->body .="<tr class=display_ad_label>\n\t<td>".urldecode($this->messages[638])."</td>\n</tr>\n";
		$this->body .="<tr class=error_messages>\n\t<td>".urldecode($this->messages[64])."</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		exit;
	 } //end of function browse_error

//####################################################################################

	function main($db)
	{
		$this->page_id = 2;
		$this->get_text($db);
		$this->body .="<table cellpadding=5 cellspacing=1 border=0 width=100%>\n";
		if (strlen(trim($this->messages[29])) > 0)
			$this->body .="<tr class=main_page_title>\n\t<td  height=20>".urldecode($this->messages[29])."</td>\n</tr>\n";
		if (strlen(trim($this->messages[30])) > 0)
			$this->body .="<tr class=main_page_message>\n\t<td  height=20>".urldecode($this->messages[30])."</td>\n</tr>\n";
		$this->body .="<tr>\n\t<td >\n\t";
		if (!$this->browse_main($db))
			$this->browse_error($db);
		$this->body .="</td>\n</tr>\n";
		$this->body .="</table>\n";
		$this->display_page($db);
		return true;
	 } //end of function main

//####################################################################################

	function browse_main($db)
	{
		if ($this->debug_browse)
		{
			echo "<br>TOP OF BROWSE_MAIN<BR>\n";
			echo $this->configuration_data['display_category_navigation']." is display_category_navigation<br>\n";

		}
		if ($this->configuration_data['display_category_navigation'])
		{
			//$this->sql_query = "select * from ".$this->categories_table." where parent_id = 0 order by display_order,category_name";
			$this->sql_query = "select ".$this->categories_table.".category_id,
				".$this->categories_table.".category_image,
				".$this->categories_table.".category_count,
				".$this->categories_table.".auction_category_count,
				".$this->categories_languages_table.".category_name,
				".$this->categories_languages_table.".description
				from ".$this->categories_table.",".$this->categories_languages_table." where
				parent_id = 0 and
				".$this->categories_table.".category_id = ".$this->categories_languages_table.".category_id and
				".$this->categories_languages_table.".language_id = ".$this->language_id."
				order by ".$this->categories_table.".display_order,".$this->categories_languages_table.".category_name";

			$result = $db->Execute($this->sql_query);
			if ($this->debug_browse) echo $this->sql_query."<br>\n";
			if (!$result)
			{
				if ($this->debug_browse) echo $this->sql_query."<br>\n";
				$this->error_message = "<font class=error_message>".urldecode($this->messages[33])."</font>";
				return false;
			}
			elseif  ($result->RecordCount() > 0)
			{
				$this->body .="<table cellpadding=3 cellspacing=1 border=0 width=100% >\n\t";

				switch ($this->configuration_data['number_of_browsing_columns'])
				{
					case 1: $column_width = "100%"; break;
					case 2: $column_width = "50%"; break;
					case 3: $column_width = "33%"; break;
					case 4: $column_width = "25%"; break;
					case 5: $column_width = "20%"; break;
				} //end of switch
				while ($show = $result->FetchRow())
				{
					$this->body .="<tr>\n\t\t<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show['category_id'].">";
					if (strlen(trim($show['category_image'])) > 0)
					{
						$this->body .="<img src=\"".$show['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
					}
					//$category_name = $this->get_category_name($db,$show['category_id']);
					$this->body .="<font class=main_category_name>".urldecode(stripslashes($show['category_name']));
					$this->body .= "</font>";
					if ($this->configuration_data['display_category_count'])
					{

						$css = array("listing_count" => "main_category_count", "auction_count" => "main_category_count", "ad_count" => "main_category_count");
						if ($this->debug_browse)
						{
							echo $show['auction_category_count']." is auction_category_count<br>\n";
							echo $show['category_count']." is category_count<br>\n";
						}
						$category_count['listing_count'] = $show['auction_category_count'] + $show['category_count'];
						$category_count['ad_count'] = $show['category_count'];
						$category_count['auction_count'] = $show['auction_category_count'];
						$this->body .="<font class=main_category_count>";
						//$this->body .= $this->display_category_count($db, $show['category_id'], -1, 0, 0, $category_count);
						$this->body .= $this->display_category_count($db, $show['category_id'], -1, 0, $css, $category_count);
						$this->body .="</font>";
					}
					if ($this->configuration_data['category_new_ad_limit'])
						$this->check_category_new_ad_icon_use($db,$show['category_id']);
					if ($this->configuration_data['display_category_description'])
						$this->body .="</a><br><font class=main_category_description>".urldecode(stripslashes($show['description']))."</font>";
					$this->body .="</td>\n\t";
					if ($this->configuration_data['number_of_browsing_columns'] > 1)
					{
						if ($show = $result->FetchRow())
						{
							//$category_name = $this->get_category_name($db,$show['category_id']);
							$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show['category_id'].">";
							if (strlen(trim($show['category_image'])) > 0)
							{
								$this->body .="<img src=\"".$show['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
							}
							$this->body .="<font class=main_category_name>".urldecode(stripslashes($show['category_name']));
							$this->body .= "</font>";
							if ($this->configuration_data['display_category_count'])
							{
								$category_count['listing_count'] = $show['auction_category_count'] + $show['category_count'];
								$category_count['ad_count'] = $show['category_count'];
								$category_count['auction_count'] = $show['auction_category_count'];
								$this->body .="<font class=main_category_count>";
								$this->body .= $this->display_category_count($db, $show['category_id'], -1, 0, 0, $category_count);
								$this->body .="</font>";
							}
							if ($this->configuration_data['category_new_ad_limit'])
								$this->check_category_new_ad_icon_use($db,$show['category_id']);
							if ($this->configuration_data['display_category_description'])
								$this->body .="</a><br><font class=main_category_description>".urldecode(stripslashes($show['description']))."</font>";
							$this->body .="</td>\n\t";
						}
						else
						{
							$this->body .="<td  width=".$column_width.">&nbsp;</td>\n\t";
						}
						if ($this->configuration_data['number_of_browsing_columns'] > 2)
						{
							if ($show = $result->FetchRow())
							{
								//$category_name = $this->get_category_name($db,$show['category_id']);
								$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show['category_id'].">";
								if (strlen(trim($show['category_image'])) > 0)
								{
									$this->body .="<img src=\"".$show['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
								}
								$this->body .="<font class=main_category_name>".urldecode(stripslashes($show['category_name']));
								$this->body .= "</font>";
								if ($this->configuration_data['display_category_count'])
								{
									$category_count['listing_count'] = $show['auction_category_count'] + $show['category_count'];
									$category_count['ad_count'] = $show['category_count'];
									$category_count['auction_count'] = $show['auction_category_count'];
									$this->body .="<font class=main_category_count>";
									$this->body .= $this->display_category_count($db, $show['category_id'], -1, 0, 0, $category_count);
									$this->body .="</font>";
								}
								if ($this->configuration_data['category_new_ad_limit'])
									$this->check_category_new_ad_icon_use($db,$show['category_id']);
								if ($this->configuration_data['display_category_description'])
									$this->body .="</a><br><font class=main_category_description>".urldecode(stripslashes($show['description']))."</font>";
								$this->body .="</td>\n\t";
							}
							else
							{
								$this->body .="<td  width=".$column_width.">&nbsp;</td>\n\t";
							}
							if ($this->configuration_data['number_of_browsing_columns'] > 3)
							{
								if ($show = $result->FetchRow())
								{
									//$category_name = $this->get_category_name($db,$show['category_id']);
									$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show['category_id'].">";
									if (strlen(trim($show['category_image'])) > 0)
									{
										$this->body .="<img src=\"".$show['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
									}
									$this->body .="<font class=main_category_name>".urldecode(stripslashes($show['category_name']));
									$this->body .= "</font>";
									if ($this->configuration_data['display_category_count'])
									{
										$category_count['listing_count'] = $show['auction_category_count'] + $show['category_count'];
										$category_count['ad_count'] = $show['category_count'];
										$category_count['auction_count'] = $show['auction_category_count'];
										$this->body .="<font class=main_category_count>";
										$this->body .= $this->display_category_count($db, $show['category_id'], -1, 0, 0, $category_count);
										$this->body .="</font>";
									}
									if ($this->configuration_data['category_new_ad_limit'])
										$this->check_category_new_ad_icon_use($db,$show['category_id']);
									if ($this->configuration_data['display_category_description'])
										$this->body .="</a><br><font class=main_category_description>".urldecode(stripslashes($show['description']))."</font>";
									$this->body .="</td>\n\t";
								}
								else
								{
									$this->body .="<td  width=".$column_width.">&nbsp;</td>\n\t";
								}
								if ($this->configuration_data['number_of_browsing_columns'] > 4)
								{
									if ($show = $result->FetchRow())
									{
										//$category_name = $this->get_category_name($db,$show['category_id']);
										$this->body .="<td  width=".$column_width."><a href=".$this->configuration_data['classifieds_file_name']."?a=5&b=".$show['category_id'].">";
										if (strlen(trim($show['category_image'])) > 0)
										{
											$this->body .="<img src=\"".$show['category_image']."\" hspace=2 vspace=0 border=0 align=left>";
										}
										$this->body .="<font class=main_category_name>".urldecode(stripslashes($show['category_name']));
										$this->body .= "</font>";
										if ($this->configuration_data['display_category_count'])
										{
											$category_count['listing_count'] = $show['auction_category_count'] + $show['category_count'];
											$category_count['ad_count'] = $show['category_count'];
											$category_count['auction_count'] = $show['auction_category_count'];
											$this->body .="<font class=main_category_count>";
											$this->body .= $this->display_category_count($db, $show['category_id'], -1, 0, 0, $category_count);
											$this->body .="</font>";
										}
										if ($this->configuration_data['category_new_ad_limit'])
											$this->check_category_new_ad_icon_use($db,$show['category_id']);
										if ($this->configuration_data['display_category_description'])
											$this->body .="</a><br><font class=main_category_description>".urldecode(stripslashes($show['description']))."</font>";
										$this->body .="</td>\n\t";
									}
									else
									{
										$this->body .="<td  width=".$column_width.">&nbsp;</td>\n\t";
									}
								}
							}
						}
					}

					$this->body .="</tr>\n\t";
				}
				$this->body .="</table>\n";
				return true;
			}
			else
			{
				$this->body .="<table cellpadding=5 cellspacing=1 border=0 >\n\t";
				$this->body .="<tr class=no_categories_yet>\n\t<td >".urldecode($this->messages[34])."</td>\n</tr>\n";
				$this->body .="</table>\n";
				return true;
			}
		}
		return true;

	 } //end of function main

//####################################################################################

	function classified_close(&$db)
	{
		$debug_close = 0;
		$current_time = $this->shifted_time($db);

		//delay time for renewal is 10 days
		$removal_date = ($current_time - (30 * 86400));

		if ($debug_close) echo "<br>TOP OF CLASSIFIED_CLOSE<br>";

		$this->sql_query = "select * from ".$this->classifieds_table." where ends < ".$removal_date." and live = 0";
		if ($debug_close) echo $this->sql_query."<br>\n";
		$select_result = $db->Execute($this->sql_query);
		if (!$select_result)
		{
			if ($debug_close) echo "Error in ".$this->sql_query."\n".$db->ErrorMsg();
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}
		elseif ($select_result->RecordCount() > 0)
		{
			//move expired ads to expired table
			while ($show = $select_result->FetchRow())
			{
				if ((($show['final_fee'] == 1) && ($show['final_fee_transaction_number'] > 0) && ($show['current_bid'] >= $show['reserve_price']))
					|| ($show['final_fee'] == 0) || ($this->is_class_auctions()) || ($this->is_classifieds()))
				{
					if ($debug_close)
					{
						echo $show['duration']." is show[duration]<br>\n";
					}
					if ((strlen(trim($show['duration'])) == 0) || (is_null($show['duration'])))
					{
						echo "show[duration] is null or empty<BR>\n";
						$show['duration'] = 0;
					}
					if ($debug_close)
					{
						echo $show['duration']." is show[duration] 2<br>\n";
					}					
					$category_string = $this->get_category_string($db,$show['category']);
					$this->sql_query = "INSERT INTO ".$this->classifieds_expired_table."
						(id,seller,title,date,description,category,
						duration,location_state,location_zip,location_country,ends,search_text,ad_ended,reason_ad_ended,viewed,
						transaction_type,bolding,better_placement,featured_ad,subtotal,tax,total,precurrency,price,postcurrency,
						business_type,optional_field_1,optional_field_2,optional_field_3,optional_field_4,optional_field_5,
						optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,
						optional_field_11,optional_field_12,optional_field_13,optional_field_14,optional_field_15,
						optional_field_16,optional_field_17,optional_field_18,optional_field_19,optional_field_20,phone,phone2,fax,email,auction_type,
						final_fee,final_fee_transaction_number,final_price,item_type)
						VALUES
						(".$show['id'].",
						\"".$show['seller']."\",
						\"".$show['title']."\",
						\"".$show['date']."\",
						\"".$show['description']."\",
						\"".$category_string."\",
						".$show['duration'].",
						\"".$show['location_state']."\",
						\"".$show['location_zip']."\",
						\"".$show['location_country']."\",
						\"".$show['ends']."\",
						\"".urlencode($show['search_text'])."\",
						".$this->shifted_time($db).",
						\"expired\",
						".$show['viewed'].",
						\"".$show['transaction_type']."\",
						\"".$show['bolding']."\",
						\"".$show['better_placement']."\",
						\"".$show['featured_ad']."\",
						\"".$show['subtotal']."\",
						\"".$show['tax']."\",
						\"".$show['total']."\",
						\"".$show['precurrency']."\",
						\"".$show['price']."\",
						\"".$show['postcurrency']."\",
						\"".$show['business_type']."\",
						\"".$show['optional_field_1']."\",
						\"".$show['optional_field_2']."\",
						\"".$show['optional_field_3']."\",
						\"".$show['optional_field_4']."\",
						\"".$show['optional_field_5']."\",
						\"".$show['optional_field_6']."\",
						\"".$show['optional_field_7']."\",
						\"".$show['optional_field_8']."\",
						\"".$show['optional_field_9']."\",
						\"".$show['optional_field_10']."\",
						\"".$show['optional_field_11']."\",
						\"".$show['optional_field_12']."\",
						\"".$show['optional_field_13']."\",
						\"".$show['optional_field_14']."\",
						\"".$show['optional_field_15']."\",
						\"".$show['optional_field_16']."\",
						\"".$show['optional_field_17']."\",
						\"".$show['optional_field_18']."\",
						\"".$show['optional_field_19']."\",
						\"".$show['optional_field_20']."\",
						\"".$show['phone']."\",
						\"".$show['phone2']."\",
						\"".$show['fax']."\",
						\"".$show['email']."\",
						\"".$show['auction_type']."\",
						\"".$show['final_fee']."\",
						\"".$show['final_fee_transaction_number']."\",
						\"".$show['final_price']."\",
						\"".$show["item_type"]."\")";

					$insert_expired_result = $db->Execute($this->sql_query);
					if ($debug_close)
						echo $this->sql_query."<br>\n";
					if (!$insert_expired_result)
					{
						if ($debug_close) 
						{
							echo $db->ErrorMsg()." is the error message<bR>\n";
							echo $this->sql_query."<br>\n";
						}						
						$this->sql_query = "delete from ".$this->classifieds_expired_table." where id = ".$show['id'];
						$delete_bad_result = $db->Execute($this->sql_query);
						if ($debug_close) echo $this->sql_query."<br>\n";
						if (!$delete_bad_result)
						{
							if ($debug_close) 
							{
								echo $db->ErrorMsg()." is the error message<bR>\n";
								echo $this->sql_query."<br>\n";
							}	
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}
						$this->sql_query = "INSERT INTO ".$this->classifieds_expired_table."
							(id,seller,title,date,description,category,
							duration,location_state,location_zip,location_country,ends,search_text,ad_ended,reason_ad_ended,viewed,
							transaction_type,bolding,better_placement,featured_ad,subtotal,tax,total,precurrency,price,postcurrency,
							business_type,optional_field_1,optional_field_2,optional_field_3,optional_field_4,optional_field_5,
							optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,
							optional_field_11,optional_field_12,optional_field_13,optional_field_14,optional_field_15,
							optional_field_16,optional_field_17,optional_field_18,optional_field_19,optional_field_20,phone,phone2,fax,email, auction_type,
							final_fee,final_fee_transaction_number,final_price,item_type)
							VALUES
							(".$show['id'].",
							\"".$show['seller']."\",
							\"".$show['title']."\",
							\"".$show['date']."\",
							\"".$show['description']."\",
							\"".$category_string."\",
							".$show['duration'].",
							\"".$show['location_state']."\",
							\"".$show['location_zip']."\",
							\"".$show['location_country']."\",
							\"".$show['ends']."\",
							\"".urlencode($show['search_text'])."\",
							".$this->shifted_time($db).",
							\"expired\",
							".$show['viewed'].",
							\"".$show['transaction_type']."\",
							\"".$show['bolding']."\",
							\"".$show['better_placement']."\",
							\"".$show['featured_ad']."\",
							\"".$show['subtotal']."\",
							\"".$show['tax']."\",
							\"".$show['total']."\",
							\"".$show['precurrency']."\",
							\"".$show['price']."\",
							\"".$show['postcurrency']."\",
							\"".$show['business_type']."\",
							\"".$show['optional_field_1']."\",
							\"".$show['optional_field_2']."\",
							\"".$show['optional_field_3']."\",
							\"".$show['optional_field_4']."\",
							\"".$show['optional_field_5']."\",
							\"".$show['optional_field_6']."\",
							\"".$show['optional_field_7']."\",
							\"".$show['optional_field_8']."\",
							\"".$show['optional_field_9']."\",
							\"".$show['optional_field_10']."\",
							\"".$show['optional_field_11']."\",
							\"".$show['optional_field_12']."\",
							\"".$show['optional_field_13']."\",
							\"".$show['optional_field_14']."\",
							\"".$show['optional_field_15']."\",
							\"".$show['optional_field_16']."\",
							\"".$show['optional_field_17']."\",
							\"".$show['optional_field_18']."\",
							\"".$show['optional_field_19']."\",
							\"".$show['optional_field_20']."\",
							\"".$show['phone']."\",
							\"".$show['phone2']."\",
							\"".$show['fax']."\",
							\"".$show["email"]."\",
							\"".$show['auction_type']."\",
							\"".$show['final_fee']."\",
							\"".$show['final_fee_transaction_number']."\",
							\"".$show['final_price']."\",
							\"".$show["item_type"]."\")";
						$insert_expired_result = $db->Execute($this->sql_query);
						if ($debug_close) echo $this->sql_query."<br>\n";
						if (!$insert_expired_result)
						{
							if ($debug_close) 
							{
								echo $db->ErrorMsg()." is the error message<bR>\n";
								echo $this->sql_query."<br>\n";
							}
							return false;
						}
					}
					//delete the images
					$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$show['id'];
					$delete_image_result = $db->Execute($this->sql_query);
					if ($debug_close) echo $this->sql_query."<br>\n";
					if (!$delete_image_result)
					{
						if ($debug_close) 
						{
							echo $db->ErrorMsg()." is the error message<bR>\n";
							echo $this->sql_query."<br>\n";
						}	
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}

					//delete from auctions extra questions
					$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id = ".$show['id'];
					$remove_extra_result = $db->Execute($this->sql_query);
					if ($debug_close) echo $this->sql_query."<br>\n";
					if (!$remove_extra_result)
					{
						if ($debug_close) 
						{
							echo $db->ErrorMsg()." is the error message<bR>\n";
							echo $this->sql_query."<br>\n";
						}
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}

					//delete url images
					//get image urls to
					$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$show['id'];
					$get_url_result = $db->Execute($this->sql_query);
					if ($debug_close) echo $this->sql_query."<BR>\n";
					if (!$get_url_result)
					{
						if ($debug_close) 
						{
							echo $db->ErrorMsg()." is the error message<bR>\n";
							echo $this->sql_query."<br>\n";
						}
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					elseif ($get_url_result->RecordCount())
					{
						while ($show_url = $get_url_result->FetchRow())
						{
							if ($show_url['full_filename'])
								unlink($show_url['file_path'].$show_url['full_filename']);
							if ($show_url['thumb_filename'])
								unlink($show_url['file_path'].$show_url['thumb_filename']);
						}
						$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$show['id'];
						$delete_url_result = $db->Execute($this->sql_query);
						if ($debug_close) echo $this->sql_query."<BR>\n";
						if (!$delete_url_result)
						{
							if ($debug_close) 
							{
								echo $db->ErrorMsg()." is the error message<bR>\n";
								echo $this->sql_query."<br>\n";
							}
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}
					}

					if ($show["item_type"] == 2)
					{

						//get high bidder for auction
						$high_bidder = $this->get_high_bidder($db,$show["id"]);
						$this->sql_query = "update ".$this->classifieds_expired_table." set
							high_bidder = ".$high_bidder["bidder"]."
							where id = ".$show["id"];
						$update_bidder_result = $db->Execute($this->sql_query);
						if ($debug_close)  echo $this->sql_query."<br>";
						if (!$update_bidder_result)
						{
							//add high_bidder to expired table and try again
							$this->sql_query = "ALTER TABLE ".$this->classifieds_expired_table." ADD high_bidder INT NOT NULL";
							$alter_expired_result = $db->Execute($this->sql_query);
							if ($debug_close) echo $this->sql_query."<br>";
							$this->sql_query = "update ".$this->classifieds_expired_table." set
								high_bidder = ".$high_bidder["bidder"]."
								where id = ".$show["id"];
							$update_bidder_result = $db->Execute($this->sql_query);
							if ($debug_close)  echo $this->sql_query."<br>";
						}

						//delete from auctions bids table
						$this->sql_query = "delete from ".$this->bid_table." where auction_id = ".$show["id"];
						$remove_bid_result = $db->Execute($this->sql_query);
						if ($debug_close)  echo $this->sql_query."<br>";
						if (!$remove_bid_result)
						{
							if ($debug_close) 
							{
								echo $db->ErrorMsg()." is the error message<bR>\n";
								echo $this->sql_query."<br>\n";
							}
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}

						//delete from auctions autobids table
						$this->sql_query = "delete from ".$this->autobid_table." where auction_id = ".$show["id"];
						$remove_autobid_result = $db->Execute($this->sql_query);
						if ($debug_close)  echo $this->sql_query."<br>";
						if (!$remove_autobid_result)
						{
							if ($debug_close) 
							{
								echo $db->ErrorMsg()." is the error message<bR>\n";
								echo $this->sql_query."<br>\n";
							}
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}
					}

					//delete from classifieds table
					$this->sql_query = "delete from ".$this->classifieds_table." where id = ".$show['id'];
					$remove_result = $db->Execute($this->sql_query);
					if ($debug_close) echo $this->sql_query."<BR>\n";
					if (!$remove_result)
					{
						if ($debug_close) 
						{
							echo $db->ErrorMsg()." is the error message<bR>\n";
							echo $this->sql_query."<br>\n";
						}
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}

					$this->update_category_count($db,$show['category']);
				}
			} //end of while
		}

		if ($this->configuration_data['email_header_break'])
			$separator = "\n";
		else
			$separator = "\r\n";

		//check for ads to take down for possible renewal
		$this->sql_query = "select * from ".$this->classifieds_table." where ends < ".$current_time." and live = 1";
		if ($debug_close) echo $this->sql_query."<BR>\n";
		$result = $db->Execute($this->sql_query);
		if (!$result)
		{
			$this->error_message = urldecode($this->messages[81]);
			if ($debug_close) echo $this->sql_query."<BR>\n";
			return false;
		}
		elseif ($result->RecordCount() > 0)
		{
			//move expired ads to expired table
			if ($debug_close)
			{
				echo $result->RecordCount()." is the number of classifieds to close<BR>\n";
			}
			while ($show = $result->FetchRow())
			{
				if ($debug_close) echo $show['item_type']." is show[item_type]<bR>\n";
				if ($show['item_type'] == 2)
				{
					$high_bidder = $this->get_high_bidder($db,$show['id']);
					if ($debug_close)
					{
						echo $show['current_bid']."  is the current_bid<bR>\n";
						echo $show['id']."  is the id<bR>\n";
						echo $show['title']."  is the title<bR>\n";
						echo $show['seller']."  is the seller<bR>\n";
						echo $high_bidder." is high_bidder<br>\n";
						echo $show['auction_type']." is auction_type<bR>\n";
						echo $show['reserve_price']." is reserver price<br>\n";
						echo $current_time." is current time and ".$show['ends']." is auction ends<BR>\n";
					}

					if ($show['auction_type'] == 1)
					{
						if ($high_bidder['bidder'])
						{
							$this->sql_query = "update ".$this->classifieds_table."
								set live = 0,
								final_price = ".$show['current_bid'].",
								high_bidder = ".$high_bidder['bidder']."
								where id = ".$show['id'];
						}
						else
						{
							$this->sql_query = "update ".$this->classifieds_table."
								set live = 0,
								final_price = ".$show['current_bid']."
								where id = ".$show['id'];
						}
					}
					else
					{
						$this->sql_query = "update ".$this->classifieds_table."
							set live = 0,
							final_price = ".$show['current_bid']."
							where id = ".$show['id'];
					}
					$update_result = $db->Execute($this->sql_query);
					if ($debug_close) echo $this->sql_query."<BR>\n";
					if (!$update_result)
					{
						if ($debug_close) echo $this->sql_query."<BR>\n";
						$this->error_message = urldecode($this->messages[100081]);
						return false;
					}
					if ($debug_close)
					{
						echo $high_bidder." is high_bidder<br>\n";
						echo $high_bidder['bidder']." is the high bidder id<Br>\n";
					}
					if (($show['current_bid'] >= $show['reserve_price']) && ($high_bidder) && ($show['auction_type'] == 1))
					{
						//insert into feedback table
						$this->sql_query = "insert into ".$this->auctions_feedbacks_table."
							(rated_user_id,rater_user_id,date,auction_id)
							values
							(".$show['seller'].",".$high_bidder['bidder'].",".$this->shifted_time($db).",".$show['id'].")";
						$insert_feedback_result = $db->Execute($this->sql_query);
						if ($debug_close) echo $this->sql_query."<BR>\n";
						if (!$insert_feedback_result)
						{
							if ($debug_close) echo $this->sql_query."<BR>\n";
							$this->error_message = urldecode($this->messages[100081]);
							return false;
						}

						$this->sql_query = "insert into ".$this->auctions_feedbacks_table."
							(rated_user_id,rater_user_id,date,auction_id)
							values
							(".$high_bidder['bidder'].",".$show['seller'].",".$this->shifted_time($db).",".$show['id'].")";
						$insert_feedback_result = $db->Execute($this->sql_query);
						if ($debug_close) echo $this->sql_query."<BR>\n";
						if (!$insert_feedback_result)
						{
							if ($debug_close) echo $this->sql_query."<BR>\n";
							$this->error_message = urldecode($this->messages[100081]);
							return false;
						}
					}

					//$this->push_messages_into_array($db,12);//What is the use of this?
					$seller_info = $this->get_user_data($db,$show['seller']);

					if ($show['auction_type'] == 1)
					{
						$this->page_id = 10172;
						$this->get_text($db);

						//standard single item auction
						if ($high_bidder)
							$high_bidder_info = $this->get_user_data($db,$high_bidder['bidder']);
						else
							$high_bidder_info = false;

						//send email to seller and winner
						//send to seller
						// echo $show['reserve_price']." is the reserve<br>".$show->MINIMUM_BID." is the minimum bid<br>".$show['current_bid']." is the current bid\n";
						//$message_data["message"] = $high_bidder_info->USERNAME.",\n";
						$message_data["message"] = $seller_info->USERNAME.",\n\n";
						if (($show['reserve_price'] <= $show['current_bid']) && ($high_bidder) && ($show['current_bid'] != 0))
						{
							//successful body
							$message_data["message"] .= urldecode($this->messages[102764])."\n\n";
							$message_data["message"] .= $high_bidder_info->FIRSTNAME." ".$high_bidder_info->LASTNAME."\n".$high_bidder_info->EMAIL."\n";
							$message_data["message"] .= urldecode($this->messages[102779])." ".$this->configuration_data['precurrency']." ".$show['current_bid']." ".$this->configuration_data['postcurrency']."\n\n";
						}
						else
						{
							//unsuccessful body
							$message_data["message"] .= urldecode($this->messages[102765])."\n\n";
						}
						$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$show['id']."\n\n";
						$message_data["from"] = "From: ".$this->configuration_data['site_email'].$separator;
						$message_data["from"] .= "Reply-to: ".$this->configuration_data['site_email'].$separator;
						$message_data["subject"] = urldecode($this->messages[102766]);
						if ($debug_close)
						{
							echo "SENDING SELLER CLOSE EMAIL REG - ".$show['id']." - ".$show['seller']." - ".$message_data["message"]."<BR><BR>";
						}
						$additional = "-f ".$this->configuration_data['site_email'];
						if ($this->configuration_data['email_configuration'] == 1)
						{
							mail($seller_info->EMAIL,$message_data["subject"],$message_data["message"],$message_data["from"],$additional);
						}
						elseif ($this->configuration_data['email_configuration'] == 2)
							@mail($seller_info->EMAIL,$message_data["subject"],$message_data["message"],$message_data["from"]);
						else
							@mail($seller_info->EMAIL,$message_data["subject"],$message_data["message"]);

						$this->page_id = 10174;
						$this->get_text($db);

						//echo $message_data["message"]." is the message<Br>\n";
						if (($show['reserve_price'] <= $show['current_bid']) && ($high_bidder) && ($show['current_bid'] != 0))
						{
							//send to winning bidder
							//echo "reserve less than minimum<Br>\n";
							$message_data["message"] = $high_bidder_info->USERNAME.",\n\n";
							// Body of message
							$message_data["message"] .= urldecode($this->messages[102767])."\n\n";
							$message_data["message"] .= $seller_info->FIRSTNAME." ".$seller_info->LASTNAME."\n".$seller_info->EMAIL."\n\n";
							$message_data["message"] .= $this->configuration_data['classifieds_url']."?a=2&b=".$show['id']."\n\n";
							$message_data["message"] .= urldecode($this->messages[102770])." ".$this->configuration_data['precurrency']." ".$show['current_bid']." ".$this->configuration_data['postcurrency']."\n\n";
							$message_data["from"] = "From: ".$this->configuration_data['site_email'].$separator;
							$message_data["from"] .= "Reply-to: ".$this->configuration_data['site_email'].$separator;
							$message_data["subject"] = urldecode($this->messages[102768]);
							if ($debug_close)
							{
								echo "SENDING HIGH BIDDER CLOSE EMAIL REG - ".$show['id']." - ".$show['seller']." - ".$message_data["message"]."<BR><BR>";
							}
							if ($this->configuration_data['email_configuration'] == 1)
							{
								mail($high_bidder_info->EMAIL,$message_data["subject"],$message_data["message"],$message_data["from"],$additional);
							}
							elseif ($this->configuration_data['email_configuration'] == 2)
							{
								mail($high_bidder_info->EMAIL,$message_data["subject"],$message_data["message"],$message_data["from"]);
							}
							else
							{
								mail($high_bidder_info->EMAIL,$message_data["subject"],$message_data["message"]);
							}
						}
					}
					else
					{
						$this->page_id = 10166;
						$this->get_text($db);

						//dutch auction
						//get all bids starting with highest first
						$this->sql_query = "select * from ".$this->bid_table." where auction_id=".$show['id']." order by bid desc,time_of_bid asc";
						$bid_result = $db->Execute($this->sql_query);
						if ($debug_close) echo $this->sql_query."<BR>\n";
						if (!$bid_result)
						{
							if ($debug_close) echo $this->sql_query."<BR>\n";
							return false;
						}
						elseif ($bid_result->RecordCount() > 0)
						{
							$total_quantity = $show['quantity'];
							//echo "total items sold - ".$total_quantity."<br>\n";
							$final_dutch_bid = 0;
							$seller_report = "";
							$this->dutch_bidders = array();
							$show_bidder = $bid_result->FetchNextObject();
							do
							{
								if ($show_bidder->BID > $show['reserve_price'])
								{
									$quantity_bidder_receiving = 0;
									if ( $show_bidder->QUANTITY <= $total_quantity )
									{
										$quantity_bidder_receiving = $show_bidder->QUANTITY ;
										if ( $show_bidder->QUANTITY == $total_quantity )
										{
											$final_dutch_bid = $show_bidder->BID;
											//echo $final_dutch_bid." is final bid after total = bid quantity<bR>\n";
										}
										$total_quantity = $total_quantity - $quantity_bidder_receiving;
									}
									else
									{
										$quantity_bidder_receiving = $total_quantity;
										$total_quantity = 0;
										$final_dutch_bid = $show_bidder->BID;
										//echo $final_dutch_bid." is final bid after total < bid quantity<bR>\n";
									}
									//echo $total_quantity." is total quantity after bidder - ".$show_bidder->BIDDER."<br>";
									if ($quantity_bidder_receiving)
									{
										//send an email
										$local_key = count($this->dutch_bidders);
										$this->dutch_bidders[$local_key]["bidder"] = $show_bidder->BIDDER;
										$this->dutch_bidders[$local_key]["quantity"] = $quantity_bidder_receiving;
										$this->dutch_bidders[$local_key]["bid"] = $show_bidder->BID;
										$bidder_info = $this->get_user_data($db,$show_bidder->BIDDER);
										$seller_report .= urldecode($this->messages[102769]).$bidder_info->USERNAME." ( ".$bidder_info->EMAIL." )\n".urldecode($this->messages[633]).$quantity_bidder_receiving."\n".urldecode($this->messages[102770]).$show_bidder->BID."\n\n";
										//echo $seller_report."<br><br>";
									}
								}

							} while (($show_bidder = $bid_result->FetchNextObject()) && ($total_quantity != 0) && ($final_dutch_bid == 0));
							if ($final_dutch_bid == 0)
								$final_dutch_bid = $this->dutch_bidders[$local_key]["bid"];

							//save final dutch bid as final_price
							$this->sql_query = "update ".$this->classifieds_table."
								set final_price = ".$final_dutch_bid."
								where id = ".$show['id'];
							$update_dutch_bid_result = $db->Execute($this->sql_query);
							if ($debug_close) echo $this->sql_query."<BR>\n";
							if (!$update_dutch_bid_result)
							{
								if ($debug_close) echo $this->sql_query."<BR>\n";
								$this->error_message = urldecode($this->messages[100081]);
								return false;
							}

							//send email to winning dutch bidder(s)
							if (count($this->dutch_bidders))
							{
								reset ($this->dutch_bidders);
								foreach ($this->dutch_bidders as $key => $value)
								{
									if ($show['reserve_price'] <= $this->dutch_bidders[$key]["bid"])
									{
										$bidder_info = $this->get_user_data($db,$this->dutch_bidders[$key]["bidder"]);
										$subject  = urldecode($this->messages[102771]).urldecode($show['title']);

										$message = $bidder_info->USERNAME.",\n\n";
										$message .= urldecode($this->messages[102772])."\n\n";
										$message .= urldecode($this->messages[102773]).$this->dutch_bidders[$key]["quantity"]."\n";
										$message .= urldecode($this->messages[102774]).$final_dutch_bid."\n\n";
										$message .= urldecode($this->messages[102775])."\n".$seller_info->FIRSTNAME." ".$seller_info->LASTNAME."\n".$seller_info->USERNAME." ( ".$seller_info->EMAIL." )\n\n";
										$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$show['id']."\n\n";
										$message .= "\n\n".urldecode($this->messages[102776])."\n\n";

										$from = "From: ".$this->configuration_data['site_email'].$separator;
										$from .= "Reply-to: ".$this->configuration_data['site_email'].$separator;
										if ($debug_close)
										{
											echo "SENDING HIGH BIDDERS CLOSE EMAIL DUTCH- ".$show['id']." - ".$this->dutch_bidders[$key]["bidder"]." - ".$message."<BR><BR>";
										}
										$additional = "-f".$this->configuration_data['site_email'];
										if ($this->configuration_data['email_configuration'] == 1)
										{
											mail($bidder_info->EMAIL,$subject,$message,$from,$additional);
										}
										elseif ($this->configuration_data['email_configuration'] == 2)
										{
											mail($bidder_info->EMAIL,$subject,$message,$from);
										}
										else
										{
											mail($bidder_info->EMAIL,$subject,$message);
										}
										//enter ability to make feedback
										$this->sql_query = "insert into ".$this->auctions_feedbacks_table."
											(rated_user_id,rater_user_id,date,auction_id)
											values
											(".$this->dutch_bidders[$key]["bidder"].",".$show['seller'].",".$this->shifted_time($db).",".$show['id'].")";
										$insert_feedback_result = $db->Execute($this->sql_query);
										if ($debug_close) echo $this->sql_query."<BR>\n";
										if (!$insert_feedback_result)
										{
											if ($debug_close) echo $this->sql_query."<BR>\n";
											$this->error_message = urldecode($this->messages[100081]);
											return false;
										}

										$this->sql_query = "insert into ".$this->auctions_feedbacks_table."
											(rated_user_id,rater_user_id,date,auction_id)
											values
											(".$show['seller'].",".$this->dutch_bidders[$key]["bidder"].",".$this->shifted_time($db).",".$show['id'].")";
										$insert_feedback_result = $db->Execute($this->sql_query);
										if ($debug_close) echo $this->sql_query."<BR>\n";
										if (!$insert_feedback_result)
										{
											if ($debug_close) echo $this->sql_query."<BR>\n";
											$this->error_message = urldecode($this->messages[100081]);
											return false;
										}
									}
								}
							}

							//send email to seller
							$subject = urldecode($this->messages[102777]).urldecode($show['title']);
							$message = $seller_info->USERNAME.",\n\n";
							$message .= urldecode($this->messages[102778])."\n\n";
							$message .= urldecode($this->messages[102779]).$final_dutch_bid."\n\n";
							$message .= $seller_report."\n\n";
							$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$show['id']."\n\n";
							$message .= "\n\n".urldecode($this->messages[102780])."\n\n";
							$from = "From: ".$this->configuration_data['site_email'].$separator;
							$from .= "Reply-to: ".$this->configuration_data['site_email'].$separator;
							$additional = "-f".$this->configuration_data['site_email'];
							if ($debug_close)
							{
								echo "SENDING SELLER CLOSE EMAIL DUTCH UNSUCCESSFUL- ".$show['id']." - ".$show['seller']." - ".$message."<BR><BR>";
							}
							if ($this->configuration_data['email_configuration'] == 1)
							{
								mail($seller_info->EMAIL,$subject,$message,$from,$additional);
							}
							elseif ($this->configuration_data['email_configuration'] == 2)
							{
								mail($seller_info->EMAIL,$subject,$message,$from);
							}
							else
							{
								mail($seller_info->EMAIL,$subject,$message);
							}
							//$this->send_communication($db,$show['seller'],$message_data);
						}
						else
						{
							//no bids for this dutch auction
							//send email to seller
							//send email to seller
							$subject = urldecode($this->messages[102450]).urldecode($show['title']);
							$message = $seller_info->USERNAME.",\n\n";
							$message .= urldecode($this->messages[102781])."\n\n";
							$message .= urldecode($show['title'])."\n\n";
							$message .= $this->configuration_data['classifieds_url']."?a=2&b=".$show['id'];
							$from = "From: ".$this->configuration_data['site_email'].$separator;
							$from .= "Reply-to: ".$this->configuration_data['site_email'].$separator;
							$additional = "-f".$this->configuration_data['site_email'];
							if ($debug_close)
							{
								echo "SENDING SELLER CLOSE EMAIL DUTCH SUCCESSFUL- ".$show['id']." - ".$show['seller']." - ".$message."<BR><BR>";
							}
							if ($this->configuration_data['email_configuration'] == 1)
							{
								mail($seller_info->EMAIL,$subject,$message,$from,$additional);
							}
							elseif ($this->configuration_data['email_configuration'] == 2)
							{
								mail($seller_info->EMAIL,$subject,$message,$from);
							}
							else
							{
								mail($seller_info->EMAIL,$subject,$message);
							}
							//$this->send_communication($db,$show['seller'],$message_data);
						}
					}
					$this->update_category_count($db,$show['category']);

					if ($debug_close)
					{
						echo $show['final_fee']." is FINAL_FEE<bR>\n";
						echo $this->configuration_data['use_account_balance']." is USE_ACCOUNT_BALANCE<bR>\n";
						echo $show['transaction_type']." is TRANSACTION_TYPE<bR>\n";
						echo $show['reserve_price']." is RESERVE_PRICE<bR>\n";
						echo $show['final_fee_transaction_number']." is FINAL_FEE_TRANSACTION_NUMBER<br>\n";
						echo $show['current_bid']." is FINAL_PRICE/CURRENT_BID<bR>\n";
						echo $seller_info->ACCOUNT_BALANCE." is ACCOUNT_BALANCE<br>\n";

					}

					if (($show['final_fee'] == 1) && ($this->configuration_data['use_account_balance']) &&
						($show['final_fee_transaction_number'] == 0) && ($show['transaction_type'] == 7) &&
						($show['current_bid'] != 0) && ($show['current_bid'] >= $show['reserve_price']))
					{
						if ($debug_close) echo "taking the final fee out of the account balance<BR>\n";
						$current_account_balance = $seller_info->ACCOUNT_BALANCE;
						//get the final_fee charge
						if ($show['price_plan_id'])
							$auction_price_plan_id = $show['price_plan_id'];
						else
						{
							//get the price plan attached to this seller
							$this->sql_query = "select auction_price_plan_id from ".$this->user_groups_price_plans_table." where
								id = ".$show['seller'];
							$price_plan_result = $db->Execute($this->sql_query);
							if ($debug_close) echo $this->sql_query."<BR>\n";
							if (!$price_plan_result)
							{
								if ($debug_close) echo $this->sql_query."<BR>\n";
								return false;
							}
							elseif  ($price_plan_result->RecordCount() == 1)
							{
								$show_price_plan = $price_plan_result->FetchNextObject();
								$auction_price_plan_id = $show_price_plan->AUCTION_PRICE_PLAN_ID;
							}
							else
							{
								if ($debug_close) echo $this->sql_query." - returned the wrong count<BR>\n";
								return false;
							}

						}

						$this->sql_query = "select charge from ".$this->final_fee_table." where".
							"(low<=".$show['current_bid']." AND high>=".$show['current_bid'].")
							and price_plan_id = ".$auction_price_plan_id." ORDER BY charge DESC limit 1";
						//echo $this->sql_query."<br>\n";
						$increment_result = $db->Execute($this->sql_query);
						if ($debug_close) echo $this->sql_query."<BR>\n";
						if (!$increment_result)
						{
							if ($debug_close) echo $this->sql_query."<BR>\n";
							return false;
						}
						elseif  ($increment_result->RecordCount() == 1)
						{
							$show_increment = $increment_result->FetchNextObject();
							$final_fee_percentage = $show_increment->CHARGE;
							if ($final_fee_percentage > 0)
							{
								if ($debug_close)
								{
									echo $final_fee_percentage." is the final_fee_percentage to charge<Br>\n";
									echo $show['auction_type']." is the auction_type<Br>\n";
								}
								if ($show['auction_type'] == 1)
								{
									$final_fee_charge = sprintf("%01.2f",(($final_fee_percentage * $show['current_bid']) / 100));
									if ($this->configuration_data['positive_balances_only'])
									{
										//this is a balance transaction
										//remove final fee from balance
										if (($current_account_balance > 0) && ($current_account_balance > $final_fee_charge))
										{
											//update user balance with old balance minus final_fee_percentage
											//place transaction in balance transactions table
											//put balance transaction id into final_fee_transaction_number

											$current_account_balance = $current_account_balance - $final_fee_charge;
											$this->sql_query = "update ".$this->userdata_table." set
												account_balance = ".$current_account_balance."
												where id = ".$show['seller'];
											if ($debug_close) echo $this->sql_query."<BR>\n";
											$update_balance_results = $db->Execute($this->sql_query);
											if (!$update_balance_results)
											{
												if ($debug_close) echo $this->sql_query."<BR>\n";
												return false;
											}

											$this->sql_query = "insert into ".$this->balance_transactions."
												(user_id,auction_id,amount,date,cc_transaction_id,invoice_id,final_fee,approved)
												values
												(".$show['seller'].",".$show['id'].",".$final_fee_charge.",".$this->shifted_time($db).",999999999,999999999,".$high_bidder['bidder'].",1)";
											if ($debug_close) echo $this->sql_query."<br>\n";
											$insert_invoice_item_result = $db->Execute($this->sql_query);
											if (!$insert_invoice_item_result)
											{
												if ($debug_close) echo $this->sql_query."<br>\n";
												return false;
											}
											$balance_transaction_id = $db->Insert_ID();

											//repitive but making sure the final fee is removed from consideration to be removed from the site balance
											$this->sql_query = "update ".$this->classifieds_table." set
												final_fee_transaction_number = ".$balance_transaction_id."
												where id  = ".$show['id']." and final_fee = 1 and final_fee_transaction_number = 0 and final_price != 0 and seller = ".$show['seller']."";
											$final_fee_result = $db->Execute($this->sql_query);
											if ($debug_close) echo $this->sql_query."<BR>\n";
											if (!$final_fee_result)
											{
												if ($debug_close) echo $this->sql_query."<BR>\n";
												$this->site_error($db->ErrorMsg());
												return false;
											}

										}
										else
										{
											//there is not enough account balance to cover the final fee
											//do not reset the final fee transaction number
											if ($debug_close) echo "not enough in account balance to cover final fee - ".$show['id']." - ".$show['seller']."<BR>\n";

										}
									}
									else
									{
										//this is an invoice transaction
										//place transaction in balance transactions table
										//put balance transaction id into final_fee_transaction_number
										$this->sql_query = "insert into ".$this->balance_transactions."
											(user_id,auction_id,amount,date,final_fee)
											values
											(".$show['seller'].",".$show['id'].",".$final_fee_charge.",".$this->shifted_time($db).",".$high_bidder['bidder'].")";
										if ($debug_close) echo $this->sql_query."<br>\n";
										$insert_invoice_item_result = $db->Execute($this->sql_query);
										if (!$insert_invoice_item_result)
										{
											if ($debug_close) echo $this->sql_query."<br>\n";
											return false;
										}
										$balance_transaction_id = $db->Insert_ID();

										//repitive but making sure the final fee is removed from consideration to be moved to an invoice
										$this->sql_query = "update ".$this->classifieds_table." set
											final_fee_transaction_number = ".$balance_transaction_id."
											where id  = ".$show['id']." and final_fee = 1 and final_fee_transaction_number = 0 and final_price != 0 and seller = ".$show['seller']."";
										$final_fee_result = $db->Execute($this->sql_query);
										if ($debug_close) echo $this->sql_query."<BR>\n";
										if (!$final_fee_result)
										{
											if ($debug_close) echo $this->sql_query."<BR>\n";
											$this->site_error($db->ErrorMsg());
											return false;
										}
									}

								}
								else
								{

									//this is a dutch auction type
									if (count($this->dutch_bidders))
									{
										reset ($this->dutch_bidders);
										//get total amount of final fees to charge
										//and test to see if above current account balance.
										//if not above the current account balance leave for the next
										//auction placement or until balance is above final fee costs
										if ($this->configuration_data['positive_balances_only'])
										{
											$total_final_fee = 0;
											foreach ($this->dutch_bidders as $key => $value)
											{
												$final_fee_charge = sprintf("%01.2f",(($final_fee_percentage * ($show['current_bid'] * $this->dutch_bidders[$key]["quantity"])) / 100));
												$this->dutch_bidders[$key]["final_fee"] = $final_fee_charge;
												if ($show['reserve_price'] <= $this->dutch_bidders[$key]["bid"])
													$total_final_fee = $total_final_fee + $final_fee_charge;
											}
											if ($debug_close) echo $total_final_fee." is the total_final_fee when POSITIVE_BALANCES_ONLY<br>\n";
										}
										else
										{
											if ($debug_close) echo $total_final_fee." is the total_final_fee when invoices<br>\n";
										}

										if (($total_final_fee <= $current_account_balance) || (!$this->configuration_data['positive_balances_only']))
										{
											reset ($this->dutch_bidders);
											foreach ($this->dutch_bidders as $key => $value)
											{
												if ($debug_close)
												{
													echo $show['reserve_price']." and ".$this->dutch_bidders[$key]["bid"]." is reserve_price and bid<bR>\n";
												}
												if ($show['reserve_price'] <= $this->dutch_bidders[$key]["bid"])
												{
													$final_fee_charge = sprintf("%01.2f",(($final_fee_percentage * ($show['current_bid'] * $this->dutch_bidders[$key]["quantity"])) / 100));
													if ($debug_close)
													{
														echo $final_fee_charge." is the final_fee_charge to bill<BR>\n";
														echo $this->configuration_data['positive_balances_only']." is POSITIVE_BALANCES_ONLY<BR>\n";
													}
													if ($this->configuration_data['positive_balances_only'])
													{
														//this is a balance transaction
														//remove final fee from balance
														if ($debug_close)
														{
															echo $current_account_balance." is current_account_balance in site balance side<BR>\n";
														}
														if (($current_account_balance > 0) && ($current_account_balance > $final_fee_charge))
														{
															$this->sql_query = "insert into ".$this->balance_transactions."
																(user_id,auction_id,amount,date,cc_transaction_id,invoice_id,final_fee)
																values
																(".$show['seller'].",".$show['id'].",".$final_fee_charge.",".$this->shifted_time($db).",999999999,999999999,".$this->dutch_bidders[$key]["bidder"].")";
															if ($debug_close) echo $this->sql_query."<br>\n";
															$insert_invoice_item_result = $db->Execute($this->sql_query);
															if (!$insert_invoice_item_result)
															{
																if ($debug_close) echo $this->sql_query."<br>\n";
																return false;
															}
															$balance_transaction_id = $db->Insert_ID();

															$this->sql_query = "update ".$this->classifieds_table." set
																final_fee_transaction_number = ".$balance_transaction_id."
																where id  = ".$show['id']." and final_fee = 1 and final_fee_transaction_number = 0 and final_price != 0 and seller = ".$show['seller']."";
															$final_fee_result = $db->Execute($this->sql_query);
															if ($debug_close) echo $this->sql_query."<BR>\n";
															if (!$final_fee_result)
															{
																if ($debug_close) echo $this->sql_query."<BR>\n";
																$this->site_error($db->ErrorMsg());
																return false;
															}

															$current_account_balance = $current_account_balance - $final_fee_charge;
															$this->sql_query = "update ".$this->userdata_table." set
																account_balance = ".$current_account_balance."
																where id = ".$show['seller'];
															if ($debug_close) echo $this->sql_query."<BR>\n";
															$update_balance_results = $db->Execute($this->sql_query);
															if (!$update_balance_results)
															{
																if ($debug_close) echo $this->sql_query."<BR>\n";
																return false;
															}
														}
														else
														{
															//not enough within account balance to cover final fee
															//this fee will be moved into next transaction
														}
													}
													else
													{
														//this is an invoice transaction
														//place transaction in balance transactions table
														//put balance transaction id into final_fee_transaction_number
														$this->sql_query = "insert into ".$this->balance_transactions."
															(user_id,auction_id,amount,date,final_fee)
															values
															(".$show['seller'].",".$show['id'].",".$final_fee_charge.",".$this->shifted_time($db).",".$this->dutch_bidders[$key]["bidder"].")";
														if ($debug_close) echo $this->sql_query."<br>\n";
														$insert_invoice_item_result = $db->Execute($this->sql_query);
														if (!$insert_invoice_item_result)
														{
															if ($debug_close) echo $this->sql_query."<br>\n";
															return false;
														}
														$balance_transaction_id = $db->Insert_ID();

														$this->sql_query = "update ".$this->classifieds_table." set
															final_fee_transaction_number = ".$balance_transaction_id."
															where id  = ".$show['id']." and final_fee = 1 and final_fee_transaction_number = 0 and final_price != 0 and seller = ".$show['seller']."";
														$final_fee_result = $db->Execute($this->sql_query);
														if ($debug_close) echo $this->sql_query."<BR>\n";
														if (!$final_fee_result)
														{
															if ($debug_close) echo $this->sql_query."<BR>\n";
															$this->site_error($db->ErrorMsg());
															return false;
														}
													}
												}
											} // end of foreach
										} // end of if
									}
								}
							} //end of if ($final_fee_percentage > 0)
							else
							{
								//there is no final fee to charge
								if ($debug_close) echo "there is no final fee for - ".$show['id']."<BR>\n";
							}
						}
						else
						{
							//there is an error in the final fee bracket
							if ($debug_close) echo "there is no final fee bracket error for - ".$show['id']."<BR>\n";
						}
					}
				}
				else
				{

					$this->sql_query = "update ".$this->classifieds_table."
						set live = 0
						where id = ".$show['id'];
					$update_result = $db->Execute($this->sql_query);
					if ($debug_close) echo $this->sql_query."<BR>\n";
					if (!$update_result)
					{
						if ($debug_close) echo $this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
				}

				//remove from all favorites
				$this->sql_query = "delete from ".$this->favorites_table."
					where classified_id = ".$show['id'];
				$delete_result = $db->Execute($this->sql_query);
				if ($debug_close) echo $this->sql_query."<BR>\n";
				if (!$delete_result)
				{
					if ($debug_close) echo $this->sql_query."<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				$this->update_category_count($db,$show['category']);
			}
		}
		elseif ($debug_close)
		{
			echo $result->RecordCount()." is the number of classifieds to close<BR>\n";
		}		

		//send expiration notices
		if ($this->configuration_data['send_ad_expire_email'])
		{
			$send_expire_time = (($this->configuration_data['send_ad_expire_email'] * 86400) + $this->shifted_time($db));
			$this->sql_query = "select * from ".$this->classifieds_table." where
				(ends < ".$send_expire_time." and expiration_notice = 0 and live=1)";

			if($this->configuration_data['send_ad_expire_frequency'])
			{
				$this->sql_query .= " OR (expiration_notice = 1 and live = 1 and
					(((expiration_last_sent + ".$this->configuration_data['send_ad_expire_frequency'].") <= ".$this->shifted_time($db).") and expiration_last_sent != 0))";
			}
			//echo $this->sql_query."<br>";

			$send_expiration_result = $db->Execute($this->sql_query);
			if (!$send_expiration_result)
			{
				if ($debug_close) echo $this->sql_query."<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($send_expiration_result->RecordCount() > 0)
			{
				//send an expiration email for this ad
				$this->page_id = 52;
				$this->get_text($db);

				while ($show = $send_expiration_result->FetchRow())
				{
					$user_data = $this->get_user_data($db,$show['seller']);

					$message_body = urldecode($this->messages[724])." ";

					switch ($this->configuration_data['email_salutation_type'])
					{
						case 1: //display username
							$message_body .= $user_data->USERNAME.",\n\n";
							break;
						case 2: //display firstname
							$message_body .= $user_data->FIRSTNAME.",\n\n";
							break;
						case 3: //display firstname and lastname
							$message_body .= $user_data->FIRSTNAME." ".$user_data->LASTNAME.",\n\n";
							break;
						case 4: //display lastname and firstname
							$message_body .= $user_data->LASTNAME." ".$user_data->FIRSTNAME.",\n\n";
							break;
						case 5: //display email address
							$message_body .= $user_data->EMAIL.",\n\n";
							break;
						default:
							$message_body .= $user_data->USERNAME.",\n\n";
							break;
					}

					$message_body .= urldecode($this->messages[725])."\n\n";
					$message_body .= urldecode($show['title'])."\n";
					$message_body .= $this->configuration_data['classifieds_url']."?a=2&b=".$show['id']."\n";

					$subject = urldecode($this->messages[723]);

					$from = "From: ".$this->configuration_data['site_email'].$separator."Reply-to: ".$this->configuration_data['site_email'].$separator;

					if ($debug_close)
					{
						echo "SENDING SELLER CLOSE NOTICE EMAIL- ".$show['id']." - ".$show['seller']." - ".$message_body."<BR><BR>";
					}

					if ($this->configuration_data['email_configuration_type'] == 1)
						$message_body = str_replace("\n\n","\n",$message_body);

					$additional = "-f".$this->configuration_data['site_email'];

					if ($this->configuration_data['email_configuration'] == 1)
						mail($user_data->EMAIL, $subject, $message_body, $from,$additional);
					elseif ($this->configuration_data['email_configuration'] == 2)
						mail($user_data->EMAIL, $subject, $message_body, $from);
					else
						mail($user_data->EMAIL, $subject, $message_body);

					//@mail($user_data->EMAIL,$subject,$message_body,$message_header);

					if($this->configuration_data["send_admin_end_email"])
					{
						$message_body = "An ad for ".$user_data->USERNAME."is expiring\n\n";
						$message_body .= urldecode($show['title'])."\n";
						$message_body .= $this->configuration_data['classifieds_url']."?a=2&b=".$show['id']."\n";

						$subject = "An ad for ".$user_data->USERNAME." is expiring";

						if ($this->configuration_data['email_configuration'] == 1)
							$message_body = str_replace("\n\n","\n",$message_body);

						if ($this->configuration_data['email_configuration'] == 1)
							mail($user_data->EMAIL, $subject, $message_body, $from,$additional);
						elseif ($this->configuration_data['email_configuration'] == 2)
							mail($user_data->EMAIL, $subject, $message_body, $from);
						else
							mail($user_data->EMAIL, $subject, $message_body);
					}

					$this->sql_query = "update ".$this->classifieds_table." set
						expiration_notice = 1,
						expiration_last_sent = ".time()."
						where id = ".$show['id'];

					if ($debug_close) echo $this->sql_query."<br>\n";
					$update_expiration_result = $db->Execute($this->sql_query);
					if (!$update_expiration_result)
					{
						if ($debug_close) echo $this->sql_query."<br>\n";
						return false;
					}
				}
			}
		}
		if ($debug_close) echo "END OF CLASSIFIED_CLOSE<bR><br>\n";
		return true;

	} //end of function classified_close

//##################################################################################3

	function admin_delete_classified($db,$classified_id=0)
	{
		if ($classified_id)
		{
			$this->sql_query = "select * from ".$this->classifieds_table." where id = ".$classified_id;
			$get_ad_result = $db->Execute($this->sql_query);
			//echo $this->sql_query."<br>\n";
			if (!$get_ad_result)
			{
				$this->body .=$this->sql_query." is the query<br>\n";
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			elseif ($get_ad_result->RecordCount() == 1)
			{
				$show = $get_ad_result->FetchRow();
				$category_string = $this->get_category_string($db,$show['category']);

				$this->sql_query = "REPLACE ".$this->classifieds_expired_table."
					(id,seller,title,date,description,category,
					duration,location_state,location_zip,location_country,ends,search_text,ad_ended,reason_ad_ended,viewed,
					transaction_type,bolding,better_placement,featured_ad,subtotal,tax,total,precurrency,price,postcurrency,
					business_type,optional_field_1,optional_field_2,optional_field_3,optional_field_4,optional_field_5,
					optional_field_6,optional_field_7,optional_field_8,optional_field_9,optional_field_10,
					optional_field_11,optional_field_12,optional_field_13,optional_field_14,optional_field_15,
					optional_field_16,optional_field_17,optional_field_18,optional_field_19,optional_field_20,phone,phone2,fax,email)
					VALUES
					(".$show['id'].",
					\"".$show['seller']."\",
					\"".$show['title']."\",
					\"".$show['date']."\",
					\"".$show['description']."\",
					\"".$category_string."\",
					".$show['duration'].",
					\"".$show['location_state']."\",
					\"".$show['location_zip']."\",
					\"".$show['location_country']."\",
					\"".$show['ends']."\",
					\"".urlencode($show['search_text'])."\",
					".$this->shifted_time($db).",
					\"admin removed\",
					".$show['viewed'].",
					\"".$show['transaction_type']."\",
					\"".$show['bolding']."\",
					\"".$show['better_placement']."\",
					\"".$show['featured_ad']."\",
					\"".$show['subtotal']."\",
					\"".$show['tax']."\",
					\"".$show['total']."\",
					\"".$show['precurrency']."\",
					\"".$show['price']."\",
					\"".$show['postcurrency']."\",
					\"".$show['business_type']."\",
					\"".$show['optional_field_1']."\",
					\"".$show['optional_field_2']."\",
					\"".$show['optional_field_3']."\",
					\"".$show['optional_field_4']."\",
					\"".$show['optional_field_5']."\",
					\"".$show['optional_field_6']."\",
					\"".$show['optional_field_7']."\",
					\"".$show['optional_field_8']."\",
					\"".$show['optional_field_9']."\",
					\"".$show['optional_field_10']."\",
					\"".$show['optional_field_11']."\",
					\"".$show['optional_field_12']."\",
					\"".$show['optional_field_13']."\",
					\"".$show['optional_field_14']."\",
					\"".$show['optional_field_15']."\",
					\"".$show['optional_field_16']."\",
					\"".$show['optional_field_17']."\",
					\"".$show['optional_field_18']."\",
					\"".$show['optional_field_19']."\",
					\"".$show['optional_field_20']."\",
					\"".$show['phone']."\",
					\"".$show['phone2']."\",
					\"".$show['fax']."\",
					\"".$show['email']."\")";
				$insert_result = $db->Execute($this->sql_query);
				//echo $this->sql_query."<br>\n";
				if (!$insert_result)
				{
					$this->body .=$this->sql_query."<br>\n";
					$this->error_message = urldecode($this->messages[81]);
					return false;
				}
				else
				{

					//delete from classifieds extra questions
					$this->sql_query = "delete from ".$this->classified_extra_table." where classified_id = ".$classified_id;
					$remove_extra_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$remove_extra_result)
					{
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}

					//delete url images
					//get image urls to
					$this->sql_query = "select * from ".$this->images_urls_table." where classified_id = ".$classified_id;
					$get_url_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$get_url_result)
					{
						//$this->body .=$this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}
					elseif ($get_url_result->RecordCount())
					{
						while ($show_url = $get_url_result->FetchRow())
						{
							if ($show_url['full_filename'])
								unlink($show_url['file_path'].$show_url['full_filename']);
							if ($show_url['thumb_filename'])
								unlink($show_url['file_path'].$show_url['thumb_filename']);
						}
						$this->sql_query = "delete from ".$this->images_urls_table." where classified_id = ".$classified_id;
						$delete_url_result = $db->Execute($this->sql_query);
						//echo $this->sql_query."<br>\n";
						if (!$delete_url_result)
						{
							//$this->body .=$this->sql_query."<br>\n";
							$this->error_message = urldecode($this->messages[81]);
							return false;
						}
					}

					//delete db images
					$this->sql_query = "delete from ".$this->images_table." where classified_id = ".$classified_id;
					$delete_images_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$delete_images_result)
					{
						//$this->body .=$this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}

					$this->sql_query = "delete from ".$this->classifieds_table." where id = ".$classified_id;
					$delete_ad_result = $db->Execute($this->sql_query);
					//echo $this->sql_query."<br>\n";
					if (!$delete_ad_result)
					{
						//$this->body .=$this->sql_query."<br>\n";
						$this->error_message = urldecode($this->messages[81]);
						return false;
					}

					$this->update_category_count($db,$show['category']);
					header("Location: ".$this->configuration_data['classifieds_url']."?a=5&b=".$show['category']);
					return true;
				}
			}
			else
			{
				$this->error_message = urldecode($this->messages[81]);
				return false;
			}
			return true;
		}
		else
		{
			$this->error_message = urldecode($this->messages[81]);
			return false;
		}

	} //end of function admin_delete_classified

//##################################################################################

} // end of class Browse_ads

?>