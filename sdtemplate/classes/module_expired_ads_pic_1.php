<? //module_featured_ads_pic_1.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

$this->get_text($db,$show_module['page_id']);
$this->get_css($db,$show_module['page_id']);
$seed = rand();
$this->body = "";

if ($this->configuration_data['display_sub_category_ads'])
	$this->get_sql_in_statement($db,$this->site_category);
else
	$this->in_statement = " in (".$this->site_category.") ";

if ($this->filter_id)
{
	$filter_in_statement = $this->get_sql_filter_in_statement($db);
	$sql_filter_in_statement = " and filter_id ".$filter_in_statement." ";
}

if ($this->state_filter)
{
	//add state to end of sql_query
	if (strlen(trim($this->sql_zip_filter_in_statement)) == 0)
		$this->sql_state_filter_statement = " and location_state = \"".$this->state_filter."\" ";
}
if (($this->zip_filter_distance) && ($this->zip_filter))
{
	//add zip code in statement to end of sql_query
	if (strlen(trim($this->sql_zip_filter_in_statement)) == 0)
	{
		$zip_filter_in_statement = $this->get_sql_zip_filter_in_statement($db);
		$this->sql_zip_filter_in_statement = " and ".$zip_filter_in_statement." ";
	}
}
//$show_module['module_display_type_listing'] WILL ALWAYS BE ZERO IF NOT CLASSAUCTIONS. THE SWITCH IN ADMIN IS NOT AVAILABLE TO CHOOSE.
if(($this->is_classifieds()) || (($this->is_class_auctions())&& ($show_module['module_display_type_listing']==2)))
{
	$type_in_statement = " and item_type=1 ";
}
if(($this->is_auctions()) || (($this->is_class_auctions())&& ($show_module['module_display_type_listing']==1)))
{
	$type_in_statement = " and item_type=2 ";
}
if(($this->is_class_auctions()) && ($show_module['module_display_type_listing']==0))
{
	$type_in_statement = "";
}
//$this->sql_query = "select * from geodesic_classifieds where featured_ad = 1 and image > 0 and live = 1 ".$this->sql_zip_filter_in_statement.$this->sql_state_filter_statement.$sql_filter_in_statement;
$this->sql_query = "select * from geodesic_classifieds,".$this->images_urls_table." WHERE geodesic_classifieds.item_type=2 AND
	geodesic_classifieds.id = ".$this->images_urls_table.".classified_id and ".$this->images_urls_table.".display_order = 1 and
 	unix_timestamp(now()) > ends and unix_timestamp(SUBDATE(now(), INTERVAL 30 DAY)) < ends
	and live = 0 ".$this->sql_zip_filter_in_statement.$this->sql_state_filter_statement.$sql_filter_in_statement.$type_in_statement;

if ($this->site_category)
	$this->sql_query .= " and category ".$this->in_statement;
$this->sql_query .= " order by rand(".$seed.") limit ".($show_module['module_number_of_ads_to_display'] * $show_module['module_number_of_columns']);
$featured_result = $db->Execute($this->sql_query);
//echo $this->sql_query."<br>\n";
if (!$featured_result)
{
	$this->error_message = "<font class=error_message>".urldecode($this->messages[65])."</font>";
	return false;
}
elseif ($featured_result->RecordCount() > 0)
{
 	//$show_module['module_number_of_ads_to_display'];
	//$result->Move(0);
	//$this->display_featured_1_result($db,$featured_result);
	
	$show_module['use_current'] = 1;
	
	if ($featured_result->RecordCount() > 0)
	{
		$this->body ="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%>\n\t";
		if ($show_module['module_display_header_row'])
			$this->body .="<tr class=featured_ad_pics_1_header_row><td colspan=".$show_module['module_number_of_columns'].">
				".urldecode($this->messages[1471])."</td></tr>\n\t";
		switch ($show_module['module_number_of_columns'])
		{
			case 1: $column_width = "100%"; break;
			case 2: $column_width = "50%"; break;
			case 3: $column_width = "33%"; break;
			case 4: $column_width = "25%"; break;
			case 5: $column_width = "20%"; break;
			case 6: $column_width = "16%"; break;
			case 7: $column_width = "14%"; break;
			case 8: $column_width = "12%"; break;
			case 9: $column_width = "11%"; break;
			case 10: $column_width = "10%"; break;
		} //end of switch
		$row = 0;
		while ($show = $featured_result->FetchRow())
		{
			if ($show_module['module_number_of_ads_to_display'] > $row)
			{
				$this->body .="<tr class=featured_ad_pics_1_pic_row><td width=".$column_width." valign=top>";
				$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=middle height=".$show_module['module_thumb_height'].">";
				//display first image
				//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

				$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

				//CLASSAUCTIONS SPECIFIC
				if($this->is_class_auctions())
				{
					//TYPE CLASSIFIED

					if (($show_module['module_display_type_text']) && ($show['item_type']==1))
					{
						$this->body .= "<br>";
						$this->body .= urldecode($this->messages[200027]);

					}

					//TYPE AUCTION

					if (($show_module['module_display_type_text']) && ($show['item_type']==2))
					{
						$this->body .= "<br>";
						$this->body .= urldecode($this->messages[200028]);

					}

				}

				$this->body .= "</td></tr></table></td>";

				if ($show_module['module_number_of_columns'] > 1)
				{
					if ($show = $featured_result->FetchRow())
					{
						$this->body .= "<td width=".$column_width." valign=top>";
						$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
						//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);
                        
						$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

						//CLASSAUCTIONS SPECIFIC
						if($this->is_class_auctions())
						{
							//TYPE CLASSIFIED

							if (($show_module['module_display_type_text']) && ($show['item_type']==1))
							{
								$this->body .= "<br>";
								$this->body .= urldecode($this->messages[200027]);

							}

							//TYPE AUCTION

							if (($show_module['module_display_type_text']) && ($show['item_type']==2))
							{
								$this->body .= "<br>";
								$this->body .= urldecode($this->messages[200028]);

							}

						}

						$this->body .= "</td></tr></table></td>";
					}
					else
						$this->body .= "<td width=".$column_width.">&nbsp;</td>";
					if ($show_module['module_number_of_columns'] > 2)
					{
						if ($show = $featured_result->FetchRow())
						{
							$this->body .= "<td width=".$column_width." valign=top>";
							$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
							//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

							$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

							//CLASSAUCTIONS SPECIFIC
							if($this->is_class_auctions())
							{
								//TYPE CLASSIFIED

								if (($show_module['module_display_type_text']) && ($show['item_type']==1))
								{
									$this->body .= "<br>";
									$this->body .= urldecode($this->messages[200027]);

								}

								//TYPE AUCTION

								if (($show_module['module_display_type_text']) && ($show['item_type']==2))
								{
									$this->body .= "<br>";
									$this->body .= urldecode($this->messages[200028]);

								}

							}

							$this->body .= "</td></tr></table></td>";
						}
						else
							$this->body .= "<td width=".$column_width.">&nbsp;</td>";
						if ($show_module['module_number_of_columns'] > 3)
						{
							if ($show = $featured_result->FetchRow())
							{
								$this->body .= "<td width=".$column_width." valign=top>";
								$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
								//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

								$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

								//CLASSAUCTIONS SPECIFIC
								if($this->is_class_auctions())
								{
									//TYPE CLASSIFIED

									if (($show_module['module_display_type_text']) && ($show['item_type']==1))
									{
										$this->body .= "<br>";
										$this->body .= urldecode($this->messages[200027]);

									}

									//TYPE AUCTION

									if (($show_module['module_display_type_text']) && ($show['item_type']==2))
									{
										$this->body .= "<br>";
										$this->body .= urldecode($this->messages[200028]);

									}

								}

								$this->body .= "</td></tr></table></td>";
							}
							else
								$this->body .= "<td width=".$column_width.">&nbsp;</td>";
							if ($show_module['module_number_of_columns'] > 4)
							{
								if ($show = $featured_result->FetchRow())
								{
									$this->body .= "<td width=".$column_width." valign=top>";
									$this->body .="<table  width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
									//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

									$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

									//CLASSAUCTIONS SPECIFIC
									if($this->is_class_auctions())
									{
										//TYPE CLASSIFIED

										if (($show_module['module_display_type_text']) && ($show['item_type']==1))
										{
											$this->body .= "<br>";
											$this->body .= urldecode($this->messages[200027]);

										}

										//TYPE AUCTION

										if (($show_module['module_display_type_text']) && ($show['item_type']==2))
										{
											$this->body .= "<br>";
											$this->body .= urldecode($this->messages[200028]);

										}

									}

									$this->body .= "</td></tr></table></td>";
								}
								else
									$this->body .= "<td width=".$column_width.">&nbsp;</td>";
								if ($show_module['module_number_of_columns'] > 5)
								{
									if ($show = $featured_result->FetchRow())
									{
										$this->body .= "<td width=".$column_width." valign=top>";
										$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
										//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

										$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

										//CLASSAUCTIONS SPECIFIC
										if($this->is_class_auctions())
										{
											//TYPE CLASSIFIED

											if (($show_module['module_display_type_text']) && ($show['item_type']==1))
											{
												$this->body .= "<br>";
												$this->body .= urldecode($this->messages[200027]);

											}

											//TYPE AUCTION

											if (($show_module['module_display_type_text']) && ($show['item_type']==2))
											{
												$this->body .= "<br>";
												$this->body .= urldecode($this->messages[200028]);

											}

										}

										$this->body .= "</td></tr></table></td>";
									}
									else
										$this->body .= "<td width=".$column_width.">&nbsp;</td>";
									if ($show_module['module_number_of_columns'] > 6)
									{
										if ($show = $featured_result->FetchRow())
										{
											$this->body .= "<td width=".$column_width." valign=top>";
											$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
											//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

											$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

											//CLASSAUCTIONS SPECIFIC
											if($this->is_class_auctions())
											{
												//TYPE CLASSIFIED

												if (($show_module['module_display_type_text']) && ($show['item_type']==1))
												{
													$this->body .= "<br>";
													$this->body .= urldecode($this->messages[200027]);

												}

												//TYPE AUCTION

												if (($show_module['module_display_type_text']) && ($show['item_type']==2))
												{
													$this->body .= "<br>";
													$this->body .= urldecode($this->messages[200028]);

												}

											}

											$this->body .= "</td></tr></table></td>";
										}
										else
											$this->body .= "<td width=".$column_width.">&nbsp;</td>";
										if ($show_module['module_number_of_columns'] > 7)
										{
											if ($show = $featured_result->FetchRow())
											{
												$this->body .= "<td width=".$column_width." valign=top>";
												$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
												//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

												$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

												//CLASSAUCTIONS SPECIFIC
												if($this->is_class_auctions())
												{
													//TYPE CLASSIFIED

													if (($show_module['module_display_type_text']) && ($show['item_type']==1))
													{
														$this->body .= "<br>";
														$this->body .= urldecode($this->messages[200027]);

													}

													//TYPE AUCTION

													if (($show_module['module_display_type_text']) && ($show['item_type']==2))
													{
														$this->body .= "<br>";
														$this->body .= urldecode($this->messages[200028]);

													}

												}

												$this->body .= "</td></tr></table></td>";
											}
											else
												$this->body .= "<td width=".$column_width.">&nbsp;</td>";
											if ($show_module['module_number_of_columns'] > 8)
											{
												if ($show = $featured_result->FetchRow())
												{
													$this->body .= "<td width=".$column_width." valign=top>";
													$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
													//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

													$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

													//CLASSAUCTIONS SPECIFIC
													if($this->is_class_auctions())
													{
														//TYPE CLASSIFIED

														if (($show_module['module_display_type_text']) && ($show['item_type']==1))
														{
															$this->body .= "<br>";
															$this->body .= urldecode($this->messages[200027]);

														}

														//TYPE AUCTION

														if (($show_module['module_display_type_text']) && ($show['item_type']==2))
														{
															$this->body .= "<br>";
															$this->body .= urldecode($this->messages[200028]);

														}

													}

													$this->body .= "</td></tr></table></td>";
												}
												else
													$this->body .= "<td width=".$column_width.">&nbsp;</td>";
												if ($show_module['module_number_of_columns'] > 9)
												{
													if ($show = $featured_result->FetchRow())
													{
														$this->body .= "<td width=".$column_width." valign=top>";
														$this->body .="<table width=".$show_module['module_thumb_width']." align=center><tr><td align=center valign=center height=".$show_module['module_thumb_height'].">";
														//$this->display_thumbnail($db,$show['id'],$show_module['module_thumb_width'],$show_module['module_thumb_height'],1);

														$this->featured_ads_pic_level_1_display($db, $show, $show_module, "featured_ad_pics_1_pic_row");

														//CLASSAUCTIONS SPECIFIC
														if($this->is_class_auctions())
														{
															//TYPE CLASSIFIED

															if (($show_module['module_display_type_text']) && ($show['item_type']==1))
															{
																$this->body .= "<br>";
																$this->body .= urldecode($this->messages[200027]);

															}

															//TYPE AUCTION

															if (($show_module['module_display_type_text']) && ($show['item_type']==2))
															{
																$this->body .= "<br>";
																$this->body .= urldecode($this->messages[200028]);

															}

														}

														$this->body .= "</td></tr></table></td>";
													}
													else
														$this->body .= "<td width=".$column_width.">&nbsp;</td>";
												}
											}
										}
									}
								}
							}
						}
					}
				}
				$this->body .= "</tr>\n";
			}
		}
		$this->body .="</table>\n";
	}
}
elseif (strlen(trim($show_module['display_empty_message'])) >0)
{
	$this->body .="<table cellpadding=3 cellspacing=1 border=0 align=center width=100%><tr><td class=featured_ad_pics_1_pic_row>".urldecode($show_module['display_empty_message'])."</td></tr></table>\n";
}
?>
