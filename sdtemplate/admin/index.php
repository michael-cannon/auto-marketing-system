<? //index.php
/**************************************************************************\
Copyright (c) 2004 Geodesic Solutions, LLC
All rights reserved
http://www.geodesicsolutions.com
see license attached to distribution
 \**************************************************************************/

include("../config.php");
include("../classes/adodb.inc.php");
include("admin_site_class.php");
include("admin_authentication_class.php");
error_reporting  (E_ERROR | E_WARNING | E_PARSE);

//$parser_version = phpversion();
//if ($parser_version <= "4.1.0")
//{
//	$_SERVER = $HTTP_SERVER_VARS;
//}

$debug = 0;
$debug_cookie = 0;

$db = &ADONewConnection('mysql');
//$db = &ADONewConnection('access');
//$db = &ADONewConnection('ad39o');
//$db = &ADONewConnection('ado_mssql');
//$db = &ADONewConnection('borland_ibase');
//$db = &ADONewConnection('csv');
//$db = &ADONewConnection('db2');
//$db = &ADONewConnection('fbsql');
//$db = &ADONewConnection('firebird');
//$db = &ADONewConnection('ibase');
//$db = &ADONewConnection('informix');
//$db = &ADONewConnection('mssql');
//$db = &ADONewConnection('mysqlt');
//$db = &ADONewConnection('oci8');
//$db = &ADONewConnection('oci8po');
//$db = &ADONewConnection('odbc');
//$db = &ADONewConnection('odbc_mssql');
//$db = &ADONewConnection('odbc_oracle');
//$db = &ADONewConnection('oracle');
//$db = &ADONewConnection('postgres7');
//$db = &ADONewConnection('postgress');
//$db = &ADONewConnection('proxy');
//$db = &ADONewConnection('sqlanywhere');
//$db = &ADONewConnection('sybase');
//$db = &ADONewConnection('vfp');

if($persistent_connections)
{
	//echo " Persistent Connection <bR>";
	if (!$db->PConnect($db_host, $db_username, $db_password, $database))
	{
		echo "could not connect to database";
		exit;
	}
}
else
{
	//echo " No Persistent Connection <bR>";
	if (!$db->Connect($db_host, $db_username, $db_password, $database))
	{
		echo "could not connect to database";
		exit;
	}
}

//if (isset($HTTP_COOKIE_VARS))
//	$_COOKIE = $HTTP_COOKIE_VARS;

if ($debug)
{
	foreach ($_COOKIE as $key => $value)
		echo $key." is the cookie and ".$value." is the value<br>\n";
}

//$debug_cookie = 1;

if ($debug_cookie)
{
	echo $HTTP_COOKIE_VARS["admin_classified_session"]." is HTTP_COOKIE_VARS-admin_classified_session cookie vars<Br>\n";
	echo $_COOKIE["admin_classified_session"]." is _COOKIE-admin_classified_session cookie vars<Br>\n";
}


include('../products.php');
$product_configuration = new product_configuration($db, $product_type, 1);

$site = new Admin_site($db, $product_configuration);

if (!$_COOKIE["admin_classified_session"])
{
	$current_time = $site->shifted_time();
	$sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3600);
	if ($debug_cookie) echo $sql_query." is the query in cookie does not exist condition<br>\n";
	$delete_session_result = &$db->Execute($sql_query);
	if (!$delete_session_result)
	{
		//echo $sql_query."<br>\n";
		return false;
	}

	//set session in db
	do {
		$custom_id = md5(uniqid(rand(),1));
		$custom_id = substr( $custom_id, 0,32);
		$sql_query = "SELECT classified_session FROM geodesic_sessions WHERE classified_session = \"".$custom_id."\"";
		if ($debug_cookie) echo $sql_query." is the query<br>\n";
		$custom_id_result = &$db->Execute($sql_query);
		if (!$custom_id_result)
		{
			//echo $sql_query."<br>\n";
			return false;
		}
	} while ($custom_id_result->RecordCount() > 0);

	//$ip = getenv("REMOTE_ADDR");
	$ip = 0;
	$sql_query = "insert into geodesic_sessions
		(classified_session,user_id,last_time,ip,level)
		values
		(\"".$custom_id."\",0,".$current_time.",\"".$ip."\",0)";
	if ($debug_cookie) echo $sql_query." is the query to insert a new session into the database<br>\n";

	$insert_session_result = &$db->Execute($sql_query);
	if (!$insert_session_result)
	{
		if ($debug_cookie) echo $sql_query." -  created an sql error<br>\n";
		return false;
	}
	$expires = $site->shifted_time() + 31536000;
	$user_id = 0;
	$user_level = 0;
	$admin_classified_session = $custom_id;
	setcookie("admin_classified_session",$custom_id,$expires,"/",$_SERVER["HTTP_HOST"]);
	//header("Set-Cookie: admin_classified_session=".$custom_id."; path=/; domain=".$_SERVER["HTTP_HOST"]."; expires=".gmstrftime("%A, %d-%b-%Y %H:%M:%S GMT",$expires));
	if ($debug_cookie)
	{
		echo $_SERVER["HTTP_HOST"]." is server host<bR>\n";
		echo $admin_classified_session." is the session cookie id that was just attempted<br>\n";
		echo $custom_id." is the value of admin_classified_session value<br>\n";
		echo $expires." is the expiration of the cookie<br>\n";
	}

}
else
{
	$current_time = $site->shifted_time();
	$sql_query = "delete from geodesic_sessions WHERE last_time < ".($current_time - 3600);
	$delete_session_result = $db->Execute($sql_query);
	//echo $current_time." is current - ".($current_time - 3600)."<BR>\n";
	if ($debug_cookie) echo $sql_query." is the query in cookie exists condition<br>\n";
	if (!$delete_session_result)
	{
		if ($debug_cookie) echo $sql_query."<br>\n";
		return false;
	}
	//get session information
	$sql_query = "SELECT * FROM geodesic_sessions WHERE classified_session = \"".$_COOKIE["admin_classified_session"]."\"";
	$session_result = $db->Execute($sql_query);
	if ($debug_cookie) echo $sql_query." is the query<br>\n";
	if (!$session_result)
	{
		//echo $sql_query."<br>\n";
		return false;
	}
	elseif ($session_result->RecordCount() == 1)
	{
		//$current_ip = getenv("REMOTE_ADDR");
		$current_ip = 0;
		$show = $session_result->FetchRow();

		$sql_query = "update geodesic_sessions set last_time = ".$current_time." where classified_session = \"".$_COOKIE["admin_classified_session"]."\"";
		$update_session_result = &$db->Execute($sql_query);
		if ($debug_cookie) echo $sql_query." is the query<br>\n";
		if (!$update_session_result)
		{
			if ($debug_cookie) echo $sql_query."<br>\n";
			return false;
		}
		elseif ($session_result->RecordCount() == 1)
		{
			if ($debug_cookie)
			{
				echo $show["last_time"]." is LAST_TIME<br>";
				echo $show["ip"]." is IP<br>\n";
			}
			if (($show["last_time"] < ($current_time + 60)) && ($current_ip == $show["ip"]))
			{
				$user_id = $show["user_id"];
				$user_level = $show["level"];
				$admin_classified_session = $_COOKIE["admin_classified_session"];
			}
			else
			{
				//change session
				setcookie("admin_classified_session","",0,"/",$_SERVER["HTTP_HOST"]);
				$sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["admin_classified_session"]."\"";
				$delete_session_result = &$db->Execute($sql_query);
				if ($debug_cookie) echo $sql_query." is the query<br>\n";
				if (!$delete_session_result)
				{
					if ($debug_cookie) echo $sql_query."<br>\n";
					return false;
				}
				header("Location: ".$_SERVER["PHP_SELF"]);
				exit;
			}
			if ($debug_cookie)
			{
				echo $user_id." is user_id<br>";
				echo $user_level." is user_level<br>\n";
			}
		}
		else
		{
			setcookie("admin_classified_session","",0,"/",$_SERVER["HTTP_HOST"]);
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
	}
	else
	{
		$ip = 0;
		$sql_query = "insert into geodesic_sessions
			(classified_session,user_id,last_time,ip,level)
			values
			(\"".$_COOKIE["admin_classified_session"]."\",0,".$current_time.",\"".$ip."\",0)";
		if ($debug_cookie) echo $sql_query." is the query<br>\n";
		$insert_session_result = &$db->Execute($sql_query);
		if (!$insert_session_result)
		{
			if ($debug_cookie) echo $sql_query."<br>\n";
			return false;
		}
		//setcookie("admin_classified_session","",0,"/","$_SERVER["HTTP_HOST"]");
		//echo "deleting cookie 3";
		//echo $_SERVER["PHP_SELF"]." is php_self<bR>\n";
		header("Location: ".$_SERVER["PHP_SELF"]);
	}
}

if ($user_id == 1)
{
	if(!$_REQUEST["a"])
	{
		$site->admin_header($db);
	}
	elseif (($_REQUEST["a"] != 104))
	{
		if ((((($_REQUEST["d"]) || ($_REQUEST["e"])) && ($_REQUEST["b"]) && (($_REQUEST["z"]) || ($start_from)))) && $_REQUEST["a"] != 41)
		{
			if($debug)
			{
				echo $_REQUEST["a"]." is a<br>\n";
				echo $_REQUEST["d"]." is d<br>\n";
				echo $_REQUEST["e"]." is e<br>\n";
				echo $_REQUEST["b"]." is b<br>\n";
				echo $_REQUEST["z"]." is z<br>\n";
				echo $start_from." is start from<br>\n";
			}

			//no header
			// if the messaging pages though we want a header
			//if($_REQUEST["a"] == 25 || $_REQUEST["a"] == 44)
				//$site->admin_header($db, 1);
		}
		else
		{
			//$site->admin_header($db, 1);
		}
	}
	switch ($_REQUEST["a"])
	{
		case 1:
			//display the add category form
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (!$admin_category->display_category_form($db,$_REQUEST["b"],1))
				$admin_category->category_error();
			$admin_category->display_page($db);
			break;

		case 2:
			//insert the new subcategory
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (!$admin_category->insert_category($db,$_REQUEST["b"],$_REQUEST["c"]))
				$admin_category->category_error();
			else
				if (!$admin_category->browse($db,$_REQUEST["c"]))
					$admin_category->category_error();
			$admin_category->display_page($db);
			break;

		case 3:
			//display a category
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (!$admin_category->display_current_category($db,$_REQUEST["b"]))
				$admin_category->category_error();
			else
				if (!$admin_category->browse($db))
					$admin_category->category_error();
			$admin_category->display_page($db);
			break;

		case 4:
			//delete a category
			//deletes a category and all categories underneath
			//moves all items in any of these categories into the parent category of the intended category to be deleted
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_category->delete_category($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_category->category_error();
				else
				{
					if (!$admin_category->browse($db))
						$admin_category->category_error();
				}
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_category->delete_category_check($db,$_REQUEST["b"]))
					$admin_category->category_error();
			}
			else
			{
				if (!$admin_category->browse($db))
					$admin_category->category_error();
			}
			$admin_category->display_page($db);
			break;

		case 5:
			//edit a category
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_category->display_category_form($db,$_REQUEST["b"]))
					$admin_category->category_error();
			}
			else
			{
				if (!$admin_category->browse($db,$_REQUEST["b"]))
					$admin_category->category_error();
			}
			$admin_category->display_page($db);
			break;

		case 6:
			//update a category
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //edit and update the category fields
					{
						if (($_REQUEST["b"]) && ($_REQUEST["c"]))
						{
							if (!$admin_category->update_fields_to_use($db,$_REQUEST["c"],$_REQUEST["b"]))
								$admin_category->category_error();
							else
								if (!$admin_category->category_fields_to_use_form($db,$_REQUEST["c"]))
									$admin_category->category_error();
						}
						elseif ($_REQUEST["c"])
						{
							if (!$admin_category->category_fields_to_use_form($db,$_REQUEST["c"]))
								$admin_category->category_error();
						}
						else
							if (!$admin_category->browse($db,$_REQUEST["c"]))
								$admin_category->category_error();
					}
					break;
				case 2: //edit and update the category specific lengths of ads
					{
						if ($_REQUEST["d"])
						{
							//delete a length
							if ($admin_category->category_specific_delete_length($db,$_REQUEST["d"]))
							{
								if (!$admin_category->category_specific_lengths_form($db,$_REQUEST["c"]))
									$admin_category->category_error();
							}
							else
								$admin_category->category_error();
							break;
						}
						if (($_REQUEST["b"]) && ($_REQUEST["c"]))
						{
							if (!$admin_category->add_category_specific_length($db,$_REQUEST["b"],$_REQUEST["c"]))
								$admin_category->category_error();
							else
								if (!$admin_category->category_specific_lengths_form($db,$_REQUEST["c"]))
									$admin_category->category_error();
						}
						elseif ($_REQUEST["c"])
						{
							if (!$admin_category->category_specific_lengths_form($db,$_REQUEST["c"]))
								$admin_category->category_error();
						}
						else
							if (!$admin_category->browse($db,$_REQUEST["c"]))
								$admin_category->category_error();
					}
					break;
				case 3: //edit and update the templates used within this category
					{
						if (($_REQUEST["b"]) && ($_REQUEST["c"]))
						{
							if (!$admin_category->update_category_templates($db,$_REQUEST["c"],$_REQUEST["b"]))
								$admin_category->category_error();
							else
								if (!$admin_category->category_templates_form($db,$_REQUEST["c"],1))
									$admin_category->category_error();
						}
						elseif (($_REQUEST["c"]))
						{
							if (!$admin_category->category_templates_form($db,$_REQUEST["c"],1))
								$admin_category->category_error();
						}
						else
							if (!$admin_category->browse($db))
								$admin_category->category_error();
					}
					break;
				default:
				{
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						if (!$admin_category->update_category($db,$_REQUEST["c"],$_REQUEST["d"]))
							$admin_category->category_error();
						else
							if (!$admin_category->browse($db,$_REQUEST["c"]))
								$admin_category->category_error();
					}
					else
					{
						$admin_category->category_error();
					}
					break;
				}
			}
			$admin_category->display_page($db);
			break;

		case 7:
			//browse a category
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_category->browse($db,$_REQUEST["b"]))
					$admin_category->category_error();
			}
			else
			{
				if (!$admin_category->browse($db))
					$admin_category->category_error();
			}
			$admin_category->display_page($db);
			break;

		case 8:
			//list sell questions attached to a category
			include("admin_category_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if (!$admin_question->show_current_questions($db,$_REQUEST["b"]))
					$admin_question->admin_question_error();
			$admin_question->display_page($db);
			break;

		case 9:
			//edit a sell question
			include("admin_category_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_question->sell_question_form($db,$_REQUEST["b"]))
					$admin_question->admin_question_error();
			}
			else
			{
				if (!$admin_question->edit_admin_login_form($db))
					$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 10:
			//new sell question form
			include("admin_category_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_question->sell_question_form($db,0,$_REQUEST["b"]))
					$admin_question->admin_question_error();
			}
			else
			{
				$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 11:
			//insert a new sell question
			include("admin_category_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_question->insert_sell_question($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_question->admin_question_error();
				else
					if (!$admin_question->show_current_questions($db,$_REQUEST["c"]))
						$admin_question->admin_question_error();

			}
			else
			{
				$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 12:
			//delete a sell question
			include("admin_category_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_question->delete_sell_question($db,$_REQUEST["b"]))
					$admin_question->admin_question_error();
				elseif (!$admin_question->show_current_questions($db,$_REQUEST["c"]))
						$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 13:
			//update a sell question
			include("admin_category_questions_class.php");
			include("admin_categories_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			$admin_category = new Admin_categories($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_question->update_sell_question($db,$_REQUEST["c"],$_REQUEST["b"]))
					$admin_question->admin_question_error();
				elseif ($_REQUEST["d"])
				{
					if (!$admin_question->show_current_questions($db,$_REQUEST["d"]))
						$admin_question->admin_question_error();
						$admin_question->display_page($db);
				}
				else
				{
					if (!$admin_category->browse($db))
						$admin_category->category_error();
					$admin_category->display_page($db);
				}
			}
			break;

		case 14:
			//text messages
			include("admin_text_management_class.php");
			$admin_text = new Text_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]) && ($_REQUEST["l"]) && ($_REQUEST["z"]))
			{
				//update the text messages
				if (!$admin_text->update_text_message($db,$_REQUEST["b"],$_REQUEST["c"],$_REQUEST["z"],$_REQUEST["l"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_text->display_page_messages($db,$_REQUEST["b"],$_REQUEST["l"]))
						$admin_text->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["b"]) && ($_REQUEST["c"]) && ($_REQUEST["l"]))
			{
				//edit this message
				if (!$admin_text->edit_text_message($db,$_REQUEST["c"],$_REQUEST["l"],$_REQUEST["b"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["b"]) && ($_REQUEST["l"]))
			{
				//display this pages messages
				if (!$admin_text->display_page_messages($db,$_REQUEST["b"],$_REQUEST["l"]))
				{
					//echo "returned error<br>\n";
					$admin_text->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["l"])
			{
				//display this pages messages
				if (!$admin_text->language_home($db,$_REQUEST["l"]))
				{
					//echo "returned error in language home<br>\n";
					$admin_text->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif($_REQUEST["y"])
			{
				if ($debug) echo $_REQUEST["type"]." is type<br>\n";
				if (!$admin_text->search_text($db, $_REQUEST["search_text"], $_REQUEST["search_type"]))
				{
					$admin_text->site_error(0, __FILE__, __LINE__);
				}
			}
			else
			{
				//display the text management homepage
				$admin_text->home($db);
			}
			$admin_text->display_page($db);
			break;

		case 15:
			//badword management
			include("admin_text_badwords_class.php");
			$admin_badwords = new Text_badwords_management($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				//insert new badword
				if (!$admin_badwords->insert_badword($db,$_REQUEST["b"]))
					$admin_badwords->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_badwords->display_badword_list($db))
						$admin_badwords->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["c"])
			{
				//edit this message
				if (!$admin_badwords->delete_badword($db,$_REQUEST["c"]))
					$admin_badwords->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_badwords->display_badword_list($db))
						$admin_badwords->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the text management homepage
				if (!$admin_badwords->display_badword_list($db))
					$admin_badwords->site_error(0, __FILE__, __LINE__);
			}
			$admin_badwords->display_page($db);
			break;

		case 16:
			//search users
			include("admin_user_management_class.php");
			$admin_search = new Admin_user_management($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				//search users
				if (!$admin_search->search_users($db,$_REQUEST["b"]))
					$admin_search->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the simple and advanced search box
				$admin_search->advanced_user_search($db);
			}
			$admin_search->display_page($db);
			break;

		case 17:
			//display this user
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				//search users
				if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the simple and advanced search box
				if (!$admin_user->list_users($db))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			$admin_user->display_page($db);
			break;

		case 18:
			//edit this user
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			if ($_REQUEST["b"] && $_REQUEST["c"])
			{
				if (!$admin_user->update_user_info($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_user->site_error(0, __FILE__, __LINE__);
				elseif ($admin_user->user_management_error)
				{
					if (!$admin_user->edit_user_form($db,$_REQUEST["b"]))
						$admin_user->site_error(0, __FILE__, __LINE__);
				}
				else
				{
					if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
						$admin_user->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["b"])
			{
				//edit user form
				if (!$admin_user->edit_user_form($db,$_REQUEST["b"]))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the simple and advanced search box
				if (!$admin_user->list_users($db))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			$admin_user->display_page($db);
			break;

		case 19:
			//list _users
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				//search users
				if (!$admin_user->list_users($db,$_REQUEST["b"]))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the simple and advanced search box
				$admin_user->list_users($db);
			}
			$admin_user->display_page($db);
			break;

		case 20:
			//list _users
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			if ($sql_query)
			{
				//search users
				if (!$admin_user->list_users($db,$_REQUEST["b"]))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the simple and advanced search box
				$admin_user->list_users($db);
			}
			$admin_user->display_page($db);
			break;

		case 21:
			//edit state dropdown
			include("admin_state_management_class.php");
			$admin_states = new State_management($db, $product_configuration);
			if ($_REQUEST["b"] || $_REQUEST["d"])
			{
				//insert/update state
				if (!$admin_states->update_state($db,$_REQUEST["b"],$_REQUEST["d"]))
				{
					$admin_states->site_error(0, __FILE__, __LINE__);
				}
				elseif (!$admin_states->display_state_list($db))
				{
					$admin_states->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["c"])
			{
				//delete this state
				if (!$admin_states->delete_state($db,$_REQUEST["c"]))
					$admin_states->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_states->display_state_list($db))
						$admin_states->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the text management homepage
				if (!$admin_states->display_state_list($db))
					$admin_states->site_error(0, __FILE__, __LINE__);
			}
			if (!$site->check_for_any_tax($db))
				return false;
			$admin_states->display_page($db);
			break;

		case 22:
			//edit country dropdown
			include("admin_country_management_class.php");
			$admin_countries = new Country_management($db, $product_configuration);
			if ($_REQUEST["b"] || $_REQUEST["d"])
			{
				//insert/update country
				if (!$admin_countries->update_country($db,$_REQUEST["b"],$_REQUEST["d"]))
				{
					$admin_countries->site_error(0, __FILE__, __LINE__);
				}
				elseif (!$admin_countries->display_country_list($db))
				{
					$admin_countries->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["c"])
			{
				//delete this country
				if (!$admin_countries->delete_country($db,$_REQUEST["c"]))
					$admin_countries->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_countries->display_country_list($db))
						$admin_countries->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the text management homepage
				if (!$admin_countries->display_country_list($db))
					$admin_countries->site_error(0, __FILE__, __LINE__);
			}
			if (!$site->check_for_any_tax($db))
				return false;
			$admin_countries->display_page($db);
			break;

		case 23:
			//edit ad configuration
			include("admin_ad_configuration_class.php");
			$admin_ad_configuration = new Ad_configuration($db, $product_configuration);
			switch ($_REQUEST["r"])
			{
				case 1:
					//images configuration
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_ad_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->display_ad_configuration_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->display_ad_configuration_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 2:
					//length configuration
					if ($_REQUEST["c"])
					{
						//add new length
						if (!$admin_ad_configuration->add_classified_length($db,$_REQUEST["c"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->classified_length_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["d"])
					{
						//delete length
						if (!$admin_ad_configuration->delete_classified_length($db,$_REQUEST["d"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->classified_length_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->classified_length_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 3:
					//file type configuration
					if ($_REQUEST["e"])
					{
						//update accepted file types
						if (!$admin_ad_configuration->update_file_types($db,$_REQUEST["e"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->file_types_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
						if (!$admin_ad_configuration->file_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					break;

				case 4:
					//fields to use configuration
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_fields_to_use($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->fields_to_use_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->fields_to_use_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 5:
					//this case is available for use
					break;


				case 6:
					//ad extras allowed
					if ($_REQUEST["b"])
					{
						//update ad extras
						if (!$admin_ad_configuration->update_ad_extras($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->ad_extras_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the ad extras form
						if (!$admin_ad_configuration->ad_extras_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 7:
					//this case is available for use
					break;

				case 8:
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_max_lengths($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->ad_configuration_home($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->ad_configuration_home($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				/*	Deprecated
				case 9:
					//extra checkbox data fields templates
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_extra_checkbox_template($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->extra_template_checkbox_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->extra_template_checkbox_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				*/

				case 10:
					//update flyer template and flyer images
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_flyer($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->flyer_template_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->flyer_template_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 11:
					//update sign template and sign images
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_sign($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->sign_template_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->sign_template_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 12:
					//delete image
					if (($_REQUEST["b"]) && ($_REQUEST["c"]))
					{
						//update ad configuration
						if (!$admin_ad_configuration->delete_template_image($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif ($_REQUEST["c"] == 13)
						{
							if (!$admin_ad_configuration->flyer_template_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						}
						elseif ($_REQUEST["c"] == 14)
						{
							if (!$admin_ad_configuration->sign_template_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							if (!$admin_ad_configuration->ad_configuration_home($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->ad_configuration_home($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 13:
					//extra question data fields templates
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_max_lengths($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->ad_configuration_home($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->ad_configuration_home($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 14:
					//currency choices available for price field
					if ($_REQUEST["b"])
					{
						//insert currency type
						if (!$admin_ad_configuration->insert_currency_type($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->currency_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["z"])
					{
						//delete currency type
						if (!$admin_ad_configuration->delete_currency_type($db,$_REQUEST["z"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->currency_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->currency_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 15:
					//extra checkbox data fields templates
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_full_images_template($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->full_images_template_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->full_images_template_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 16:
					//print friendly ad display fields templates
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_print_friendly_ad_template($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->print_friendly_ad_template_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->print_friendly_ad_template_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 17:
					//insert new file type for upload
					if ($_REQUEST["b"])
					{

						//insert currency type
						if (isset ($HTTP_POST_FILES))
							$_FILES = &$HTTP_POST_FILES;
						if (!$admin_ad_configuration->insert_new_file_type($db,$_REQUEST["b"],$_FILES))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->file_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the new file type form
						if (!$admin_ad_configuration->new_file_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 18:
					//delete file type
					if ($_REQUEST["b"])
					{
						//delete currency type
						if (!$admin_ad_configuration->delete_file_type($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->file_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the file type homepage
						if (!$admin_ad_configuration->file_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 19:
					//extra checkbox data fields templates
					if ($_REQUEST["b"])
					{
						//update ad configuration
						if (!$admin_ad_configuration->update_picture_popup_template($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_ad_configuration->picture_popup_template_form($db))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->picture_popup_template_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 20:
					//bid increment admin
					if($_REQUEST["z"])
					{
							if ($_REQUEST["b"])
							{
								if (!$admin_ad_configuration->update_increments($db,$_REQUEST["b"],$_REQUEST["c"]))
								{
									$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
								}
								else
								{
									if (!$admin_ad_configuration->increments_form($db))
										$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
								}
							}
							else
							{
								if (!$admin_ad_configuration->increments_form($db))
									$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
							}
					}
					elseif($_REQUEST["c"] >= 0.00 && $_REQUEST["d"])
					{
						if(!$admin_ad_configuration->delete_increments($db, $_REQUEST["c"], $_REQUEST["d"]))
						{
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							if (!$admin_ad_configuration->increments_form($db))
									$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						if (!$admin_ad_configuration->increments_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 21:
					//payment choices available for payment
					if ($_REQUEST["b"])
					{
						//insert payment type
						if($_REQUEST["c"])
						{
							if(!$admin_ad_configuration->update_payment_types($db, $_REQUEST["c"]))
								$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						}
						elseif (!$admin_ad_configuration->insert_payment_type($db,$_REQUEST["b"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->payment_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["z"])
					{
						//delete payment type
						if (!$admin_ad_configuration->delete_payment_type($db,$_REQUEST["z"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_ad_configuration->payment_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					elseif($_REQUEST["d"])
					{
						if(!$admin_ad_configuration->update_payment_types($db, $_REQUEST["d"]))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						if (!$admin_ad_configuration->payment_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_ad_configuration->payment_types_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 22:
					//configuration home
					if (!$admin_ad_configuration->ad_configuration_home($db))
						$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					break;

				case 23:
					if($_REQUEST['c'])
					{
						if(!$admin_ad_configuration->remove_archived_listings($db, $_REQUEST['c']))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
						elseif(!$admin_ad_configuration->remove_archived_listings_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if(!$admin_ad_configuration->remove_archived_listings_form($db))
							$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;

				default:
					//configuration home
					if (!$admin_ad_configuration->ad_configuration_home($db))
						$admin_ad_configuration->site_error(0, __FILE__, __LINE__);
					break;
			} //end of switch
			$admin_ad_configuration->display_page($db);
			break;

		case 24:
			//edit html allowed
			include("admin_html_allowed_class.php");
			$admin_html_allowed = new HTML_allowed($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				//update html allowed
				if (!$admin_html_allowed->update_html_allowed_list($db,$_REQUEST["b"]))
					$admin_html_allowed->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_html_allowed->display_html_allowed_list($db))
						$admin_html_allowed->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the text management homepage
				if (!$admin_html_allowed->display_html_allowed_list($db))
					$admin_html_allowed->site_error(0, __FILE__, __LINE__);
			}
			$admin_html_allowed->display_page($db);
			break;

		case 25:
			include("admin_messaging_class.php");
			$admin_messaging = new Admin_messaging($db, $product_configuration);
			switch ($_REQUEST["x"])
			{
				case 1:
				{
					//send a message to a list
					if ((($_REQUEST["d"]) || ($_REQUEST["e"])) && ($_REQUEST["b"]) && (($_REQUEST["z"]) || ($start_from)))
					{
						//send the text message to the list
						if (!$admin_messaging->send_admin_message($db,$_REQUEST["b"],$_REQUEST["d"],$start_from,$_REQUEST["e"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					elseif (($_REQUEST["b"]) && ($_REQUEST["c"]))
					{
						//display of prechosen message to edit
						if (!$admin_messaging->admin_messaging_form($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_messaging->admin_messaging_form($db,$_REQUEST["b"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					break;
				}

				case 2:
				{
					//administer form messages
					if ($_REQUEST["d"])
					{
						//add new form message
						if (!$admin_messaging->insert_new_message($db,$_REQUEST["d"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_messaging->display_form_messages_list($db))
								$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["c"])
					{
						//delete the form message
						if (!$admin_messaging->delete_form_message($db,$_REQUEST["c"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_messaging->display_form_messages_list($db))
								$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					elseif (($_REQUEST["b"]) && ($_REQUEST["e"]))
					{
						//update the form message
						if (!$admin_messaging->update_message_form($db,$_REQUEST["b"],$_REQUEST["e"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_messaging->display_form_messages_list($db))
								$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["b"])
					{
						//edit the form message
						if (!$admin_messaging->form_message_edit($db,$_REQUEST["b"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_messaging->display_form_messages_list($db))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					break;
				}

				case 3:
				{
					if ($_REQUEST["c"])
					{
						//remove message from history
						if (!$admin_messaging->delete_from_message_history($db,$_REQUEST["c"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_messaging->display_message_history($db))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["b"])
					{
						//display this messages data
						if (!$admin_messaging->display_message_history_detail($db,$_REQUEST["b"]))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display all past messages
						if (!$admin_messaging->display_message_history($db))
							$admin_messaging->site_error(0, __FILE__, __LINE__);
					}
					break;
				}

				default:
				{
					//text messaging home
					if (!$admin_messaging->messaging_home($db))
						$admin_messaging->site_error(0, __FILE__, __LINE__);
				}
			}
			$admin_messaging->display_page($db);
			break;

		case 26:
			//edit registration variables
			include("admin_registration_configuration_class.php");
			$admin_registration = new Registration_configuration($db, $product_configuration);
			switch($_REQUEST["z"])
			{
				case 1:
				{
					//edit registration variables
					if ($_REQUEST["b"])
					{
						//update html allowed
						if (!$admin_registration->update_registration_configuration($db,$_REQUEST["b"]))
							$admin_registration->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_registration->display_registration_configuration_form($db))
							$admin_registration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_registration->display_registration_configuration_form($db))
							$admin_registration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 2:
				{
					//email domain configuration
					if ($_REQUEST["b"])
					{
						if (!$admin_registration->update_email_domain($db,$_REQUEST["b"]))
							$admin_registration->site_error(0, __FILE__, __LINE__);
						else
							$admin_registration->display_email_domains($db);
					}
					else
					{
						//display form with all the email domains that are blocked
						if (!$admin_registration->display_email_domains($db))
							$admin_registration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 3:
				{
					//Insert email domain configuration
					if ($_REQUEST["b"])
					{
						//insert new email domain
						if (!$admin_registration->insert_email_domain($db,$_REQUEST["b"]))
							$admin_registration->site_error(0, __FILE__, __LINE__);
						else
							$admin_registration->display_email_domains($db);
					}
					else
					{
						if (!$admin_registration->email_domains_form($db))
							$admin_registration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 4:
				{
					// Registration confirmation
					switch($_REQUEST["b"])
					{
						case 1:
						{
							if(!$admin_registration->display_registration_confirmation_form($db))
								$admin_registration->site_error(0, __FILE__, __LINE__);
							break;
						}
						case 2:
						{
							$sql_query = "select * from geodesic_confirm where username='".$_REQUEST["c"]."'";
							$confirm_result = $db->Execute($sql_query);
							$confirm_info = $confirm_result->FetchRow();
							$admin_registration->update_registration_confirmation($db, $confirm_info);
							break;
						}
					}

					break;
				}

				default:
				{
					$admin_registration->registration_configuration_home();
				}
			}
			$admin_registration->display_page($db);
			break;

		case 28:
			//site configuration tool
			include("admin_site_configuration_class.php");
			$admin_configuration = new Site_configuration($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1:
				{
					//edit html allowed
					if ($_REQUEST["b"])
					{
						//update html allowed
						if (!$admin_configuration->update_header_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_header_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_configuration->display_header_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}

				case 2:
				{
					//edit footer info
					if ($_REQUEST["b"])
					{
						//update html allowed
						if (!$admin_configuration->update_footer_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$v->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_footer_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_configuration->display_footer_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}

				case 3:
				{
					//edit font info
					if ($_REQUEST["b"])
					{
						//update font configuration
						if (!$admin_configuration->update_font_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_font_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_configuration->display_font_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 4:
				{
					//edit dimensions and color info
					if ($_REQUEST["b"])
					{
						//update html allowed
						if (!$admin_configuration->update_dimensions_and_colors_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_dimensions_and_colors_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_configuration->display_dimensions_and_colors_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 5:
				{
					//edit menu bar info
					if ($_REQUEST["b"])
					{
						//update html allowed
						if (!$admin_configuration->update_menu_bar_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_menu_bar_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_configuration->display_menu_bar_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 6:
				{
					//edit general info
					if ($_REQUEST["b"])
					{
						//update html allowed
						if (!$admin_configuration->update_general_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_general_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the text management homepage
						if (!$admin_configuration->display_general_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 7:
				{
					//edit browsing configuration
					if ($_REQUEST["b"])
					{
						//update browsing configuration
						if (!$admin_configuration->update_browse_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_browse_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the browse configuration page
						if (!$admin_configuration->display_browse_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				case 8:
				{
					//edit column configuration
					if ($_REQUEST["b"])
					{
						//update column configuration
						if (!$admin_configuration->update_column_configuration($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->display_column_configuration_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display the column configuration page
						if (!$admin_configuration->display_column_configuration_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				/*	Deprecated
				case 9:
				{
					// Edit User management home template configuration
					if($_REQUEST["b"])
					{
						if (!$admin_configuration->update_home_template($db,$_REQUEST["b"]))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_configuration->home_template_form($db))
								$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_configuration->home_template_form($db))
							$admin_configuration->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				*/
				default:
				{
					$admin_configuration->configuration_home();
				}
			}
			$admin_configuration->display_page($db);
			break;

		case 29:
			//update language
			include("admin_text_management_class.php");
			$admin_text = new Text_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["l"]))
			{
				if (!$admin_text->update_language($db,$_REQUEST["l"],$_REQUEST["b"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_text->home($db))
						$admin_text->site_error(0, __FILE__, __LINE__);

			}
			elseif ($_REQUEST["l"])
			{
				if (!$admin_text->edit_language_form($db,$_REQUEST["l"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_text->home($db))
					$admin_text->site_error(0, __FILE__, __LINE__);
			}
			$admin_text->display_page($db);
			break;

		case 30:
			//add new language
			include("admin_text_management_class.php");
			$admin_text = new Text_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1:
				{
					if ($_REQUEST["b"])
					{
						$language_id = $admin_text->insert_new_language($db,$_REQUEST["b"]);
						if (!$language_id)
							$admin_text->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_text->home($db,$language_id))
								$admin_text->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_text->new_language_form($db))
							$admin_text->site_error(0, __FILE__, __LINE__);
					}
					break;
				}
				default:
					//list the current languages
					$admin_text->home($db);
			}
			$admin_text->display_page($db);
			break;

		case 31:
			//remove a  language
			include("admin_text_management_class.php");
			$admin_text = new Text_management($db, $product_configuration);
			if (($_REQUEST["l"]) && ($_REQUEST["z"]))
			{
				if ($_REQUEST["l"] != 1)
				{
					if (!$admin_text->delete_language($db,$_REQUEST["l"],$_REQUEST["z"]))
						$admin_text->site_error(0, __FILE__, __LINE__);
					else
						if (!$admin_text->home($db))
							$admin_text->site_error(0, __FILE__, __LINE__);
				}
				else
				{
					if (!$admin_text->list_language_delete_form($db))
						$admin_text->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["l"])
			{
				if ($_REQUEST["l"] != 1)
				{
					if (!$admin_text->delete_language_verify($db,$_REQUEST["l"]))
						$admin_text->site_error(0, __FILE__, __LINE__);
				}
				else
				{
					if (!$admin_text->list_language_delete_form($db))
						$admin_text->site_error(0, __FILE__, __LINE__);
				}
			}
			else
			{
				if (!$admin_text->list_language_delete_form($db))
					$admin_text->site_error(0, __FILE__, __LINE__);
			}
			$admin_text->display_page($db);
			break;

		case 32:
			//edit category dropdown questions
			include("admin_category_questions_class.php");
			$admin_question_dropdowns = new Admin_category_questions($db, $product_configuration);
			if (($_REQUEST["e"]) && ($_REQUEST["b"]))
			{
				//insert the new dropdown
				$returned = $admin_question_dropdowns->insert_new_dropdown($db,$_REQUEST["b"]);
				if (!$returned)
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$returned))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["e"])
			{
				//insert new dropdown form
				if (!$admin_question_dropdowns->new_dropdown_form())
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["d"]) && ($_REQUEST["z"]))
			{
				//handle the type of delete
				//either change id
				//or just delete from sell questions
				if (!$admin_question_dropdowns->delete_dropdown($db,$_REQUEST["d"],$_REQUEST["z"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->show_all_dropdowns($db))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["d"])
			{
				//delete this question dropdown
				//delete all questions that rely on this dropdown
				//show list before delete?
				if (!$admin_question_dropdowns->delete_dropdown_intermediate($db,$_REQUEST["d"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["g"]) && ($_REQUEST["c"]))
			{
				//delete from this dropdown
				if (!$admin_question_dropdowns->delete_dropdown_value($db,$_REQUEST["g"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				//add to this dropdown
				if (!$admin_question_dropdowns->add_dropdown_value($db,$_REQUEST["c"],$_REQUEST["b"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["c"])
			{
				//edit this question dropdown
				if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_question_dropdowns->show_all_dropdowns($db))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			$admin_question_dropdowns->display_page($db);
			break;

		case 33:
			//add edit extra pages
			include("admin_extra_pages_class.php");
			$extra_pages = new Admin_extra_pages($db, $product_configuration);
			switch ($_REQUEST["b"])
			{
				case 1:
					//edit this extra page
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						if (!$extra_pages->update_extra_page($db,$_REQUEST["c"],$_REQUEST["d"]))
							$extra_pages->site_error(0, __FILE__, __LINE__);
						else
							if (!$extra_pages->add_edit_extra_page_configuration_form($db,$_REQUEST["c"]))
								if (!$extra_pages->extra_page_home($db))
									$extra_pages->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["c"])
					{
						if (!$extra_pages->add_edit_extra_page_configuration_form($db,$_REQUEST["c"]))
							$extra_pages->site_error(0, __FILE__, __LINE__);

					}
					else
					{
						if (!$extra_pages->extra_page_home($db))
							$extra_pages->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 2:
					//delete extra page
					if ($_REQUEST["c"])
					{
						if (!$extra_pages->delete_extra_page($db,$_REQUEST["c"]))
							$extra_pages->site_error(0, __FILE__, __LINE__);
						else
							if (!$extra_pages->extra_page_home($db))
								$extra_pages->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$extra_pages->extra_page_home($db))
							$extra_pages->site_error(0, __FILE__, __LINE__);
					}

					break;
				case 3:
					//add an extra page
					if ($_REQUEST["d"])
					{
						if (!$extra_pages->insert_extra_page($db,$_REQUEST["d"]))
							$extra_pages->site_error(0, __FILE__, __LINE__);
						else
							if (!$extra_pages->extra_page_home($db))
								$extra_pages->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$extra_pages->add_edit_extra_page_configuration_form($db))
							$extra_pages->site_error(0, __FILE__, __LINE__);
					}
					break;
				default:
					if (!$extra_pages->extra_page_home($db))
						$extra_pages->site_error(0, __FILE__, __LINE__);
					break;
			}
			$extra_pages->display_page($db);
			break;

		case 34:
			//remove this user
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["z"]))
			{
				if (!$admin_user->remove_user($db,$_REQUEST["b"]))
					$admin_user->site_error(0, __FILE__, __LINE__);
				else
				{
					if (!$admin_user->list_users($db))
						$admin_user->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["b"])
			{
				//search users
				if (!$admin_user-> remove_user_verify($db,$_REQUEST["b"]))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display the simple and advanced search box
				if (!$admin_user->list_users($db))
					$admin_user->site_error(0, __FILE__, __LINE__);
			}
			$admin_user->display_page($db);
			break;

		case 35:
			//backup the database to a file
			$site->body .= file_get_contents("db_backup.html");
			$site->display_page($db);
			break;

		case 36:
			//group management
			include("admin_group_management_class.php");
			$admin_group = new Group_management($db, $product_configuration);
			switch ($_REQUEST["b"])
			{
				case 1:
					//add new group
					if ($_REQUEST["d"])
					{
						if (!$admin_group->insert_group($db,$_REQUEST["d"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_group->new_group_form($db))
						$admin_group->site_error(0, __FILE__, __LINE__);
					break;
				case 2:
					//delete group
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						//move current users from group
						if ($admin_group->move_to_group($db,$_REQUEST["c"],$_REQUEST["d"]))
						{
							//delete group
							if ($admin_group->delete_group($db,$_REQUEST["c"]))
							{
								if (!$admin_group->display_group_list($db))
									$admin_group->site_error(0, __FILE__, __LINE__);
							}
							else
								$admin_group->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							$admin_group->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["c"])
					{
						if (!$admin_group->delete_group_form($db,$_REQUEST["c"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 3:
					//update default group
					if ($_REQUEST["e"])
					{
						if ($admin_group->set_default_group($db,$_REQUEST["e"]))
						{
							if (!$admin_group->display_group_list($db))
								$admin_group->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							$admin_group->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 4:
					//update group name and description
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						if (!$admin_group->update_group_info($db,$_REQUEST["c"],$_REQUEST["d"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_group->group_form($db,$_REQUEST["c"]))
								$admin_group->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["c"])
					{
						if (!$admin_group->group_form($db,$_REQUEST["c"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 5:
					//move users to another group
					if (($_REQUEST["g"]) && ($_REQUEST["h"]))
					{
						if (!$admin_group->move_to_group($db,$_REQUEST["g"],$_REQUEST["h"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_group->display_group_list($db))
								$admin_group->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["g"])
					{
						if (!$admin_group->move_group_form($db,$_REQUEST["g"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 6:
					//move group to another price plan
					if ($_REQUEST["g"] && $_REQUEST["k"])
					{
						if (!$admin_group->move_group_price_plan($db,$_REQUEST["g"],$_REQUEST["k"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_group->move_group_price_plan_form($db,$_REQUEST["g"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["g"])
					{
						if (!$admin_group->move_group_price_plan_form($db,$_REQUEST["g"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 7:
					//attach additional price plans to this group
					if ($_REQUEST["g"] && $_REQUEST["t"])
					{
						if (!$admin_group->group_multiple_price_plan_form($db,$_REQUEST["g"],$_REQUEST["t"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 8:
					//attach another price plans to this group
					if (($_REQUEST["g"]) && ($_REQUEST["p"]) && ($_REQUEST["t"]))
					{
						if (!$admin_group->add_attached_price_plan($db,$_REQUEST["g"],$_REQUEST["p"],$_REQUEST["t"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_group->group_multiple_price_plan_form($db,$_REQUEST["g"],$_REQUEST["t"]))
								$admin_group->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["g"])
					{
						if (!$admin_group->group_multiple_price_plan_form($db,$_REQUEST["g"],$_REQUEST["t"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 9:
					//delete attached price plans to this group
					if ($_REQUEST["g"] && $_REQUEST["p"] && $_REQUEST["t"])
					{
						if (!$admin_group->delete_attached_price_plan($db,$_REQUEST["g"],$_REQUEST["p"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_group->group_multiple_price_plan_form($db,$_REQUEST["g"]))
								$admin_group->site_error(0, __FILE__, __LINE__);
					}
					elseif (($_REQUEST["g"]) && ($_REQUEST["t"]))
					{
						if (!$admin_group->group_multiple_price_plan_form($db,$_REQUEST["g"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 10:
					//edit and update registration freebies
					if (($_REQUEST["g"]) && ($_REQUEST["h"]))
					{
						if (!$admin_group->update_price_plan_registration_freebies($db,$_REQUEST["g"],$_REQUEST["h"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_group->price_plan_registration_freebies_form($db,$_REQUEST["g"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["g"])
					{
						if (!$admin_group->price_plan_registration_freebies_form($db,$_REQUEST["g"]))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_group->display_group_list($db))
							$admin_group->site_error(0, __FILE__, __LINE__);
					}
					break;
				default:
					if (!$admin_group->display_group_list($db))
						$admin_group->site_error(0, __FILE__, __LINE__);
			}
			$admin_group->display_page($db);
			break;

		case 37:
			//price plan management
			include("admin_price_plan_management_class.php");
			$admin_price_plan = new Price_plan_management($db, $product_configuration);
			//Classifieds => applies_to = 1
			//Actions => applies_to = 2
			switch ($_REQUEST["b"])
			{
				case 1:
					//add new price plan
					if ($_REQUEST["d"])
					{
						if (!$admin_price_plan->insert_price_plan($db,$_REQUEST["d"]))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_price_plan->display_price_plan_list($db))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						if (!$admin_price_plan->price_plan_form($db))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 2:
					//delete price plan
					if ($_REQUEST["c"] && $_REQUEST["f"])
					{
						//move current users from group
						if ($admin_price_plan->move_to_price_plan($db,$_REQUEST["c"],$_REQUEST["d"]))
						{
							//delete price plan
							if ($admin_price_plan->delete_price_plan($db,$_REQUEST["c"]))
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["c"])
					{
						if (!$admin_price_plan->delete_price_plan_form($db,$_REQUEST["c"]))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_price_plan->display_price_plan_list($db))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
					}
					break;
				case 3:
					//edit specific price plan
					switch ($_REQUEST["f"])
					{
						case 1:
							//edit and update price plan expiration
							if (($_REQUEST["g"]) && ($_REQUEST["d"]))
							{
								if (!$admin_price_plan->update_price_plan_expiration($db,$_REQUEST["g"],$_REQUEST["d"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->price_plan_expiration_form($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->price_plan_expiration_form($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 3:
							//edit and update price plan specifics
							if (($_REQUEST["g"]) && ($_REQUEST["h"]))
							{
								if (!$admin_price_plan->update_category_specific_price_plan($db,$_REQUEST["g"],$_REQUEST["h"], 0))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->category_specific_price_plan_form($db, 0, 0, $_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->category_specific_price_plan_form($db, 0, 0, $_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 4:
							//edit and update registration freebies
							if (($_REQUEST["g"]) && ($_REQUEST["h"]))
							{
								if (!$admin_price_plan->update_price_plan_registration_freebies($db,$_REQUEST["g"],$_REQUEST["h"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->price_plan_registration_freebies_form($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->price_plan_registration_freebies_form($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 5:
							//edit and update price plan name and description
							if (($_REQUEST["g"]) && ($_REQUEST["d"]))
							{
								if (!$admin_price_plan->update_price_plan_name_and_description($db,$_REQUEST["g"],$_REQUEST["d"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->price_plan_home($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->price_plan_form($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 6:
							//delete subscription period
							if (($_REQUEST["g"]) && ($_REQUEST["h"]))
							{
								if (!$admin_price_plan->delete_subscription_period($db,$_REQUEST["h"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->display_subscription_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->display_subscription_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 7:
							//add new subscription period
							if (($_REQUEST["g"]) && ($_REQUEST["d"]))
							{
								if (!$admin_price_plan->insert_subscription_period($db,$_REQUEST["g"],$_REQUEST["d"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->display_subscription_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->subscription_period_form($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 8:
							//subscription period list
							if ($_REQUEST["g"])
							{
								if (!$admin_price_plan->display_subscription_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 9:
							//delete credit period
							if (($_REQUEST["g"]) && ($_REQUEST["h"]))
							{
								if (!$admin_price_plan->delete_credit_period($db,$_REQUEST["h"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->display_credit_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->display_credit_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 10:
							//add new credit period
							if (($_REQUEST["g"]) && ($_REQUEST["d"]))
							{
								if (!$admin_price_plan->insert_credit_period($db,$_REQUEST["g"],$_REQUEST["d"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->display_credit_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["g"])
							{
								if (!$admin_price_plan->credit_period_form($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 11:
							//credit period list
							if ($_REQUEST["g"])
							{
								if (!$admin_price_plan->display_credit_periods($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;

						default:
							//show price plan home
							if ($_REQUEST["g"])
							{
								if (!$admin_price_plan->price_plan_home($db,$_REQUEST["g"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->display_price_plan_list($db))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
					} //end of switch
					break;

				case 4:
					// Deprecated
					//move users to another price plan
					/*if (($_REQUEST["g"]) && ($_REQUEST["h"]))
					{
						if (!$admin_price_plan->move_to_price_plan($db,$_REQUEST["g"],$_REQUEST["h"]))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
						else
							if (!$admin_price_plan->display_price_plan_list($db))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["g"])
					{
						if (!$admin_price_plan->move_price_plan_form($db,$_REQUEST["g"]))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
					}
					else
					{*/
						if (!$admin_price_plan->display_price_plan_list($db))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
					//}
					break;

				case 5:
					//set a category specific pricing plan
					switch ($_REQUEST["e"])
					{
						case 1:
							//edit and update price plan for this category
							if (($_REQUEST["h"]) && ($_REQUEST["y"]) && ($_REQUEST["x"]))
							{
								if (!$admin_price_plan->update_category_specific_price_plan($db,$_REQUEST["y"],$_REQUEST["h"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->browse_categories($db,$_REQUEST["d"],$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif (($_REQUEST["y"]) && ($_REQUEST["x"]))
							{
								if (!$admin_price_plan->category_specific_price_plan_form($db,$_REQUEST["y"],$_REQUEST["d"],$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->browse_categories($db,0,$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							break;
						case 2:
							//delete price plan attached to this category
							if (($_REQUEST["x"]) && ($_REQUEST["d"]) && ($_REQUEST["y"]))
							{
								if (!$admin_price_plan->delete_category_specific_price_plan($db,$_REQUEST["y"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->browse_categories($db,$_REQUEST["d"],$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif (($_REQUEST["x"]) && ($_REQUEST["d"]))
							{
								if (!$admin_price_plan->browse_categories($db,$_REQUEST["d"],$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif ($_REQUEST["x"])
							{
								if (!$admin_price_plan->browse_categories($db,0,$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif (!$admin_price_plan->display_price_plan_list($db))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);

							break;

						case 3:
							//add new price plan attached to this category
							if (($_REQUEST["d"]) && ($_REQUEST["h"]) && ($_REQUEST["x"]))
							{
								if (!$admin_price_plan->insert_category_specific_price_plan($db,$_REQUEST["d"],$_REQUEST["x"],$_REQUEST["h"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
								elseif (!$admin_price_plan->browse_categories($db,$_REQUEST["d"],$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif (($_REQUEST["x"]) && ($_REQUEST["d"]))
							{
								if (!$admin_price_plan->category_specific_price_plan_form($db,0,$_REQUEST["d"],$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif (!$admin_price_plan->display_price_plan_list($db))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);

							break;

						default:
							//show price plan category home
							if ($_REQUEST["x"])
							{
								if (!$admin_price_plan->browse_categories($db,$_REQUEST["d"],$_REQUEST["x"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							elseif (!$admin_price_plan->display_price_plan_list($db))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
							break;
					} //end of switch
					break;

				case 6: //add and edit increments
					if (($_REQUEST["d"]) && ($_REQUEST["e"]))
					{
						if (!$admin_price_plan->update_increments($db,$_REQUEST["d"],$_REQUEST["c"],$_REQUEST["e"],$_REQUEST["f"]))
						{
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							if (!$admin_price_plan->increments_form($db,$_REQUEST["e"],$_REQUEST["f"]))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["e"])
					{
						if (!$admin_price_plan->increments_form($db,$_REQUEST["e"],$_REQUEST["f"]))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
					}
					else
						if (!$admin_price_plan->display_price_plan_list($db))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
				break;

				case 7:  //add edit lengths form
					if ($_REQUEST["c"])
					{
						if ($_REQUEST["x"])
						{
							//delete a length
							if (!$admin_price_plan->delete_length($db,$_REQUEST["x"]))
							{
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->lengths_form($db,$_REQUEST["c"],$_REQUEST["e"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
						}
						elseif ($_REQUEST["d"])
						{
							//insert a length
							if (!$admin_price_plan->add_length($db,$_REQUEST["c"],$_REQUEST["d"],$_REQUEST["e"]))
							{
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_price_plan->lengths_form($db,$_REQUEST["c"],$_REQUEST["e"]))
									$admin_price_plan->site_error(0, __FILE__, __LINE__);
							}
						}
						else
						{
							if (!$admin_price_plan->lengths_form($db,$_REQUEST["c"],$_REQUEST["e"]))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						//no price plan id
					}
				break;

				case 8:
					if(($_REQUEST["e"]) && ($_REQUEST["f"]))
					{
						if(!$admin_price_plan->delete_increments($db, $_REQUEST["e"], $_REQUEST["f"]))
						{
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							if (!$admin_price_plan->final_fees_increments_form($db,$_REQUEST["e"]))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif (($_REQUEST["d"]) && ($_REQUEST["e"]))
					{
						if (!$admin_price_plan->update_final_fee_increments($db,$_REQUEST["d"],$_REQUEST["c"],$_REQUEST["e"] ))
						{
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							if (!$admin_price_plan->final_fees_increments_form($db,$_REQUEST["e"] ))
								$admin_price_plan->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["e"] )
					{
						if (!$admin_price_plan->final_fees_increments_form($db,$_REQUEST["e"] ))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
					}
					else
						if (!$admin_price_plan->display_price_plan_list($db))
							$admin_price_plan->site_error(0, __FILE__, __LINE__);
				break;

				default:
					if (!$admin_price_plan->display_price_plan_list($db))
						$admin_price_plan->site_error(0, __FILE__, __LINE__);
			}
			$admin_price_plan->display_page($db);
			break;

		case 38:
			//css elements
			include("admin_font_management_class.php");
			$admin_fonts = new Font_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]) && ($_REQUEST["z"]))
			{
				//update the css tag
				if (!$admin_fonts->update_font_element($db,$_REQUEST["b"],$_REQUEST["c"],$_REQUEST["z"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_fonts->display_page_fonts($db,$_REQUEST["b"]))
						$admin_text->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				//edit this css tag
				//echo $_REQUEST["b"]." is page_id in index<br>\n";
				if (!$admin_fonts->edit_font_element($db,$_REQUEST["c"],$_REQUEST["b"]))
					$admin_fonts->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["b"]) && ($t))
			{
				//display this pages messages
				if ($admin_fonts->delete_page_font($db,$t))
				{
					if (!$admin_fonts->display_page_fonts($db,$_REQUEST["b"]))
					{
						$admin_fonts->site_error(0, __FILE__, __LINE__);
					}
				}
				else
				{
					$admin_fonts->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif (($_REQUEST["b"]))
			{
				//display this pages messages
				if (!$admin_fonts->display_page_fonts($db,$_REQUEST["b"]))
				{
					//echo "returned error<br>\n";
					$admin_fonts->site_error(0, __FILE__, __LINE__);
				}
			}
			else
			{
				//display the text management homepage
				$admin_fonts->home($db);
			}
			$admin_fonts->display_page($db);
			break;

		case 39:
			//payment management
			include("admin_payment_management_class.php");
			$admin_payments = new Payment_management($db, $product_configuration);
			switch ($_REQUEST["b"])
			{
				case 1:
					//ads are free or not
					if ($_REQUEST["c"])
					{
						if (!$admin_payments->update_free_or_pay_ads($db,$_REQUEST["c"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->free_or_pay_ads_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;
				case 2:
					//payment type accepted
					if ($_REQUEST["d"])
					{
						if (!$admin_payments->update_payment_accepted($db,$_REQUEST["d"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->display_payment_accepted_list($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				case 3:
					//edit paypal
					if ($_REQUEST["f"])
					{
						if (!$admin_payments->update_paypal($db,$_REQUEST["f"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->display_paypal_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				case 4:
					//edit credit card choice
					if ($_REQUEST["f"])
					{
						if (!$admin_payments->update_credit_card_choice($db,$_REQUEST["f"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->display_credit_card_choice_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				case 5:
					//edit credit card
					if ($_REQUEST["c"]) //cc choice
					{
						$admin_payments->sql_query = "select cc_admin_file from ".$admin_payments->cc_choices." where cc_id = ".$_REQUEST["c"];
						$admin_file_result = &$db->Execute($admin_payments->sql_query);
						if (!$admin_file_result)
						{
							//echo $admin_payments->sql_query."<br>\n";
							$this->error_message = $this->internal_error_message;
							return false;
						}
						elseif ($admin_file_result->RecordCount() == 1)
						{
							$show_file = $admin_file_result->FetchRow();
 							include ($show_file["cc_admin_file"]);
							if ((function_exists('display_cc_admin_form')) && (function_exists('update_cc_admin')))
							{
								if ($_REQUEST["g"])
								{
									if (!update_cc_admin($db,$admin_payments,$_REQUEST["g"]))
										$admin_payments->site_error(0, __FILE__, __LINE__);
									elseif(!display_cc_admin_form($db,$admin_payments,$admin_payments))
										$admin_payments->site_error(0, __FILE__, __LINE__);
								}
								elseif(!display_cc_admin_form($db,$admin_payments,$admin_payments))
									$admin_payments->site_error(0, __FILE__, __LINE__);
							}
							elseif (!$admin_payments->display_credit_card_choice_form($db))
								$admin_payments->site_error(0, __FILE__, __LINE__);
						}
						elseif (!$admin_payments->display_credit_card_choice_form($db))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_payments->display_credit_card_choice_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;
				case 6:
					//monetary designation
					if ($_REQUEST["h"])
					{
						if (!$admin_payments->update_currency_designation($db,$_REQUEST["h"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->currency_designation_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				case 7:
					//payment waiting period
					if ($_REQUEST["c"])
					{
						if (!$admin_payments->update_waiting_period($db,$_REQUEST["c"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->waiting_period_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				case 8:
					//edit worldpay
					if ($_REQUEST["e"])
					{
						if (!$admin_payments->update_worldpay($db,$_REQUEST["e"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->display_worldpay_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				case 9:
					//instant cash, check or money order renewals
					if ($_REQUEST["h"])
					{
						if (!$admin_payments->update_currency_designation($db,$_REQUEST["h"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->currency_designation_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				case 10:
					//edit site balance payment type
					if ($_REQUEST["e"])
					{
						if (!$admin_payments->update_site_balance($db,$_REQUEST["e"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->display_site_balance_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;
				case 11:
					//edit NOCHEX payment type
					if ($_REQUEST["e"])
					{
						if (!$admin_payments->update_nochex($db,$_REQUEST["e"]))
							$admin_payments->site_error(0, __FILE__, __LINE__);
					}
					if (!$admin_payments->display_nochex_form($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
					break;

				default:
					//ads are free or not
					if (!$admin_payments->admin_payments_home($db))
						$admin_payments->site_error(0, __FILE__, __LINE__);
			}
			$admin_payments->display_page($db);
			break;

		case 40:
			//transaction
			include("admin_transaction_management_class.php");
			$admin_transactions = new Transaction_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //view all unapproved transactions
					if (!$admin_transactions->unapproved_transactions_list($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
				break;
				case 2: //view this unapproved transaction
					if (!$admin_transactions->view_unapproved_transaction($db,$_REQUEST["b"]))
							$admin_transactions->site_error(0, __FILE__, __LINE__);
				break;
				case 3: //approve transaction
					if (!$admin_transactions->approve_transaction($db,$_REQUEST["b"]))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_transactions->unapproved_transactions_list($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
				break;
				case 4: //delete unapproved transaction
					if (!$admin_transactions->delete_classified_ad($db,$_REQUEST["b"]))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_transactions->unapproved_transactions_list($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
				break;
				case 5: //delete unapproved renewal transaction
					if (!$admin_transactions->delete_renewal($db,$_REQUEST["b"]))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_transactions->unapproved_transactions_list($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
				break;
				default:
					$admin_transactions->transaction_home($db);
			}
			$admin_transactions->display_page($db);
			break;

		case 41:
			//view user transaction
			include("admin_transaction_management_class.php");
			$admin_transactions = new Transaction_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_transactions->view_this_user_transaction($db,$_REQUEST["c"],$_REQUEST["b"],$_REQUEST["d"],$_REQUEST["z"]))
					$admin_transactions->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["b"])
			{
				if (!$_REQUEST["e"])
					$_REQUEST["e"]=1;
				if (!$admin_transactions->view_user_transactions($db,$_REQUEST["b"],$_REQUEST["e"],$_REQUEST["d"]))
					$admin_transactions->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_transactions->search_users($db))
					$admin_transactions->site_error(0, __FILE__, __LINE__);
			}
			$admin_transactions->display_page($db);
			break;

		case 42:
			//transaction home
			include("admin_transaction_management_class.php");
			$admin_transactions = new Transaction_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //view a months transactions
					if ($_REQUEST["b"])
					{
						if (!$admin_transactions->display_transactions_by($db,$_REQUEST["b"],$_REQUEST["z"]))
							$admin_transactions->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_transactions->transaction_home($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);

				break;
				case 2: //view transaction statistics for a period
					if ($_REQUEST["b"])
					{
						if (!$admin_transactions->display_transactions_by($db,$_REQUEST["b"],$_REQUEST["z"]))
							$admin_transactions->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_transactions->transaction_home($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);

				break;
				default:
					if (!$admin_transactions->transaction_home($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
			}
			$admin_transactions->display_page($db);
			break;

		case 43:
			//ad banners home
			include("admin_banner_management_class.php");
			$admin_banners = new Admin_banner_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //view banners list
					if (!$admin_banners->list_banners($db,$_REQUEST["b"]))
						$admin_banners->site_error(0, __FILE__, __LINE__);

				break;
				case 2: //edit banner
					if (($_REQUEST["b"])  && ($_REQUEST["x"]))
					{
						if (!$admin_banners->update_banner($db,$_REQUEST["b"]))
							$admin_banners->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_banners->list_banners($db))
							$admin_banners->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_banners->banner_form($db,$_REQUEST["b"]))
							$admin_banners->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_banners->banner_form($db))
						$admin_banners->site_error(0, __FILE__, __LINE__);
				break;
				case 3: //delete banner
					if (($_REQUEST["b"]) && ($_REQUEST["x"]))
					{
						if (!$admin_banners->delete_banner($db,$_REQUEST["b"]))
							$admin_banners->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_banners->list_banners($db))
							$admin_banners->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_banners->delete_banner_verify($db,$_REQUEST["b"]))
							$admin_banners->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_banners->list_banners($db))
							$admin_banners->site_error(0, __FILE__, __LINE__);
					}
				break;
				case 4: //add banner
					if ($_REQUEST["b"])
					{
						if (!$admin_banners->insert_banner($db,$_REQUEST["b"]))
							$admin_banners->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_banners->list_banners($db))
							$admin_banners->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_banners->banner_form($db))
						$admin_banners->site_error(0, __FILE__, __LINE__);
				break;
				case 5: //view banner stats
					if ($_REQUEST["b"])
					{
						if (!$admin_banners->view_banner_stats($db,$_REQUEST["b"],$_REQUEST["z"]))
							$admin_banners->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_banners->list_banners($db))
						$admin_banners->site_error(0, __FILE__, __LINE__);
				break;
				default:
					if (!$admin_banners->list_banners($db))
						$admin_banners->site_error(0, __FILE__, __LINE__);
			}
			$admin_banners->display_page($db);
			break;

		case 44:
			//page administration
			include("admin_pages_class.php");
			$admin_pages = new Admin_pages($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //display page section
					if (!$admin_pages->browse_sections($db,$_REQUEST["b"]))
						$admin_pages->site_error(0, __FILE__, __LINE__);
				break;

				case 2: //remove module from page
					if (!$admin_pages->remove_module_from_page($db,$_REQUEST["b"],$_REQUEST["c"]))
						$admin_pages->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
						$admin_pages->site_error(0, __FILE__, __LINE__);
				break;

				case 3: //display page information
					if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
						$admin_pages->site_error(0, __FILE__, __LINE__);
				break;

				case 4: //page - page element delete
					if (($_REQUEST["b"]) && ($t))
					{
						if (!$admin_pages->delete_page_element($db,$t))
							$admin_pages->site_error(0,__FILE__, __LINE__);
						else
						{
							if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
								$admin_pages->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						if (!$admin_pages->browse_sections($db,0))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 5: //page - template/language
					if (($_REQUEST["c"]) && ($_REQUEST["b"]) && ($_REQUEST["d"]))
					{
						if (!$admin_pages->update_template_attachment($db,$_REQUEST["b"],$_REQUEST["c"],$_REQUEST["d"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_pages->edit_template_attachment($db, $_REQUEST["b"], $_REQUEST["c"]))
								$admin_pages->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif (($_REQUEST["c"]) && ($_REQUEST["b"]))
					{
						if (!$admin_pages->edit_template_attachment($db,$_REQUEST["b"], $_REQUEST["c"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_pages->browse_sections($db,0))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
				break;

				case 6: //add module
					if (($_REQUEST["b"]) && ($_REQUEST["c"]))
					{
						if (!$admin_pages->add_module_to_page($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
								$admin_pages->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_pages->add_module_form($db,$_REQUEST["b"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_pages->browse_sections($db,0))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 7: //edit module
					if (($_REQUEST["b"]) && ($_REQUEST["c"]))
					{
						if (!$admin_pages->update_module_specifics($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
								$admin_pages->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_pages->browse_sections($db,0))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 8: // module to page quick attachement
					if (($_REQUEST["b"]) && ($_REQUEST["c"]))
					{
						if (!$admin_pages->update_module_to_page_attachments($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
								$admin_pages->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_pages->module_to_page_attachments_form($db,$_REQUEST["b"]))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_pages->browse_sections($db,0))
							$admin_pages->site_error(0, __FILE__, __LINE__);
					}
					break;

				default:
					if (!$admin_pages->browse_sections($db,0))
						$admin_pages->site_error(0, __FILE__, __LINE__);
			}
			$admin_pages->display_page($db);
			break;

		case 45:
			//template administration
			include("admin_templates_class.php");
			$admin_templates = new Admin_template_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //display template
					if (!$admin_templates->display_template($db,$_REQUEST["b"]))
						$admin_templates->site_error(0, __FILE__, __LINE__);
				break;

				case 2: //edit template
					if (!$admin_templates->edit_template($db,$_REQUEST["b"]))
						$admin_templates->site_error(0, __FILE__, __LINE__);
				break;

				case 3: //update template
					if (!$admin_templates->update_template($db,$_REQUEST["b"],$_REQUEST["c"]))
						$admin_templates->site_error(0, __FILE__, __LINE__);
					else
						if (!$admin_templates->display_template($db,$_REQUEST["b"]))
							$admin_templates->site_error(0, __FILE__, __LINE__);
				break;

				case 4:
					//delete template
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						//move current users from group
						if ($admin_templates->move_to_template($db,$_REQUEST["c"],$_REQUEST["d"]))
						{
							//delete group
							if ($admin_templates->delete_template($db,$_REQUEST["c"]))
							{
								if (!$admin_templates->list_templates($db))
									$admin_templates->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								$admin_templates->site_error(0, __FILE__, __LINE__);
							}
						}
						else
						{
							$admin_templates->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif (($_REQUEST["c"]) && ($_REQUEST["x"]))
					{
						//just delete template -- no pages attached
						if ($admin_templates->delete_template($db,$_REQUEST["c"]))
						{
							if (!$admin_templates->list_templates($db))
								$admin_templates->site_error(0, __FILE__, __LINE__);
						}
						else
							$admin_templates->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["c"])
					{
						if (!$admin_templates->delete_verify_template($db,$_REQUEST["c"]))
						{
							echo "the error is here<Br>\n";
							$admin_templates->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							//echo "this returned true<br>\n";
						}
					}
					else
					{
						if (!$admin_templates->list_templates($db))
							$admin_templates->site_error(0, __FILE__, __LINE__);
					}
					break;

				case 5: //add new template
					if ($_REQUEST["c"])
					{
						if (!$admin_templates->insert_template($db,$_REQUEST["c"]))
							$admin_templates->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_templates->list_templates($db))
							$admin_templates->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_templates->edit_template($db))
							$admin_templates->site_error(0, __FILE__, __LINE__);
				break;

				/*case 6: //revert to last template
					if ($_REQUEST["b"])
					{
						if (!$admin_templates->revert_template($db,$_REQUEST["b"]))
							$admin_templates->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_templates->display_template($db,$_REQUEST["b"]))
							$admin_templates->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_templates->list_templates($db))
						$admin_templates->site_error(0, __FILE__, __LINE__);
				break;
				*/

				case 6: //revert to last template
					if ($_REQUEST["b"])
					{
						$update_result = $admin_templates->revert_template($db,$_REQUEST["b"]);
;						if (!$update_result)
							$admin_templates->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_templates->display_template($db,$update_result))
							$admin_templates->site_error(0, __FILE__, __LINE__);
					}
				break;

				default: //display template list
					if (!$admin_templates->list_templates($db))
						$admin_templates->site_error(0, __FILE__, __LINE__);
				break;
			}
			$admin_templates->display_page($db);
			break;

		case 46:
			//languages

		case 47:
			//edit user credits and subscriptions
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //delete credits
					if (($_REQUEST["b"]) && ($_REQUEST["c"]))
					{
						if (!$admin_user->delete_credits($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
								$admin_user->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_user->list_users($db))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
				break;
				case 2: //add credits
					if (($_REQUEST["b"])  && ($_REQUEST["c"]))
					{
						//add the credits
						if (!$admin_user->add_credits($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["b"])
					{
						//display credits form
						if (!$admin_user->add_credits_form($db,$_REQUEST["b"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_user->list_users($db))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
				break;
				case 3: //change subscription expiration
					if (($_REQUEST["b"]) && ($_REQUEST["d"]))
					{
						if (!$admin_user->update_subscription($db,$_REQUEST["b"],$_REQUEST["c"],$_REQUEST["d"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
								$admin_user->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_user->change_subscription_form($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_user->list_users($db))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
				break;
				case 4: //delete subscriptions
					if ($_REQUEST["b"])
					{
						if (!$admin_user->delete_subscription($db,$_REQUEST["b"]))
						{
							$admin_user->site_error(0, __FILE__, __LINE__);
						}
						else
						{
							if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
							{
								$admin_user->site_error(0, __FILE__, __LINE__);
							}
						}
					}
					else
					{
						if (!$admin_user->list_users($db))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
				break;
				default:
					if (!$admin_user->list_users($db))
						$admin_user->site_error(0, __FILE__, __LINE__);
			}
			$admin_user->display_page($db);
			break;

		case 48:
			//edit category dropdown questions
			include("admin_group_questions_class.php");
			$admin_question_dropdowns = new Admin_category_questions($db, $product_configuration);
			if (($_REQUEST["e"]) && ($_REQUEST["b"]))
			{
				//insert the new dropdown
				$returned = $admin_question_dropdowns->insert_new_dropdown($db,$_REQUEST["b"]);
				if (!$returned)
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$returned))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["e"])
			{
				//insert new dropdown form
				if (!$admin_question_dropdowns->new_dropdown_form())
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["d"]) && ($_REQUEST["z"]))
			{
				//handle the type of delete
				//either change id
				//or just delete from sell questions
				if (!$admin_question_dropdowns->delete_dropdown($db,$_REQUEST["d"],$_REQUEST["z"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->show_all_dropdowns($db))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["d"])
			{
				//delete this question dropdown
				//delete all questions that rely on this dropdown
				//show list before delete?
				if (!$admin_question_dropdowns->delete_dropdown_intermediate($db,$_REQUEST["d"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["g"]) && ($_REQUEST["c"]))
			{
				//delete from this dropdown
				if (!$admin_question_dropdowns->delete_dropdown_value($db,$_REQUEST["g"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				//add to this dropdown
				if (!$admin_question_dropdowns->add_dropdown_value($db,$_REQUEST["c"],$_REQUEST["b"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["c"])
			{
				//edit this question dropdown
				if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_question_dropdowns->show_all_dropdowns($db))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			$admin_question_dropdowns->display_page($db);
			break;

		case 50:
			//update the admin login
			$admin_auth = new Admin_auth($db, $product_configuration);
			if ($_REQUEST["b"])
			{

				if (!$admin_auth->update_admin_login($db,$_REQUEST["b"]))
				{
					if (!$admin_auth->edit_admin_login_form($db))
						$admin_auth->auth_error();
				}
				else
					$admin_auth->admin_home($db);
			}
			else
			{

				if (!$admin_auth->edit_admin_login_form($db))
					$admin_auth->auth_error();
			}
			$admin_auth->display_page($db);
			break;

		case 51:
			//edit the admin login
			$admin_auth = new Admin_auth($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_auth->update_admin_login($db,$_REQUEST["b"]))
				{
					$admin_auth->auth_error();
				}
				if (!$admin_auth->edit_admin_login_form($db))
						$site->admin_home($db);
			}
			else
			{
				if (!$admin_auth->edit_admin_login_form($db))
				{
					$site->admin_home($db);
					//$admin_auth->auth_error();
				}
			}
			$admin_auth->display_page($db);
			break;

		case 52:
			//renew users ads
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //renew ad
					if (($_REQUEST["b"]) && ($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						if (!$admin_user->renew_ad($db,$_REQUEST["b"],$_REQUEST["c"],$_REQUEST["d"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
								$admin_user->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif (($_REQUEST["b"]) && ($_REQUEST["c"]))
					{
						if (!$admin_user->renew_ad_form($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["b"])
					{
						if (!$admin_user->display_user_data($db,$_REQUEST["b"]))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_user->list_users($db))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
				break;
				default:
					if (!$admin_user->list_users($db))
						$admin_user->site_error(0, __FILE__, __LINE__);
			}
			$admin_user->display_page($db);
			break;

		case 53:
			//display classified
			include("admin_user_management_class.php");
			$admin_classified = new Admin_user_management($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!($ad = $admin_classified->display_basic_classified_data($db,$_REQUEST["b"])))
					$admin_classified->site_error(0, __FILE__, __LINE__);
				else
				{
					$admin_classified->title = "Ad Details";
					$admin_classified->body .= $ad;
				}
			}
			else
			{
				$admin_classified->site_error(0, __FILE__, __LINE__);
			}
			$admin_classified->display_page($db);
			break;

		case 54:
			//restart classified
			include("admin_user_management_class.php");
			$admin_classified = new Admin_user_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				//display_user_data
				if($_REQUEST['user_id'])
				{
					if (!$admin_classified->restart_classified($db,$_REQUEST["b"],$_REQUEST["c"]))
						$admin_classified->site_error(0, __FILE__, __LINE__);
					elseif(!$admin_classified->display_user_data($db, $_REQUEST['user_id']))
						$admin_classified->site_error(0, __FILE__, __LINE__);
				}
				else
				{
					if (!$admin_classified->restart_classified($db,$_REQUEST["b"],$_REQUEST["c"]))
						$admin_classified->site_error(0, __FILE__, __LINE__);
					elseif (!($ad = $admin_classified->display_basic_classified_data($db,$_REQUEST["b"])))
						$admin_classified->site_error(0, __FILE__, __LINE__);
					else
					{
						$admin_classified->title = "Ad Details";
						$admin_classified->body .= $ad;
					}
				}
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_classified->restart_classified_form($db,$_REQUEST["b"]))
					$admin_classified->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				$admin_classified->site_error(0, __FILE__, __LINE__);
			}
			$admin_classified->display_page($db);
			break;

		case 55:
			//increase max image count
			include("admin_user_management_class.php");
			$admin_classified = new Admin_user_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_classified->increase_image_count($db,$_REQUEST["b"]))
					$admin_classified->site_error(0, __FILE__, __LINE__);
				elseif (!$admin_classified->display_user_data($db,$_REQUEST["c"]))
					$admin_classified->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				$admin_classified->site_error(0, __FILE__, __LINE__);
			}
			$admin_classified->display_page($db);
			break;

		case 60:
			//list sell questions attached to a category
			include("admin_group_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if (!$admin_question->show_current_questions($db,$_REQUEST["b"]))
					$admin_question->admin_question_error();
				$admin_question->display_page($db);
			break;

		case 61:
			//edit a sell question
			include("admin_group_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_question->group_question_form($db,$_REQUEST["b"]))
					$admin_question->admin_question_error();
			}
			else
			{
				if (!$admin_question->edit_admin_login_form($db))
					$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 62:
			//new sell question form
			include("admin_group_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_question->group_question_form($db,0,$_REQUEST["b"]))
					$admin_question->admin_question_error();
			}
			else
			{
				$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 63:
			//insert a new sell question
			include("admin_group_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_question->insert_sell_question($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_question->admin_question_error();
				else
					if (!$admin_question->show_current_questions($db,$_REQUEST["c"]))
						$admin_question->admin_question_error();

			}
			else
			{
				$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 64:
			//delete a sell question
			include("admin_group_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_question->delete_sell_question($db,$_REQUEST["b"]))
					$admin_question->admin_question_error();
				elseif (!$admin_question->show_current_questions($db,$_REQUEST["c"]))
						$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 65:
			//update a sell question
			include("admin_group_questions_class.php");
			$admin_question = new Admin_category_questions($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_question->update_sell_question($db,$_REQUEST["c"],$_REQUEST["b"]))
					$admin_question->admin_question_error();
				elseif ($_REQUEST["d"])
					if (!$admin_question->show_current_questions($db,$_REQUEST["d"]))
						$admin_question->admin_question_error();
			}
			$admin_question->display_page($db);
			break;

		case 66:
			//manage attention getters
			include("admin_ad_configuration_class.php");
			$admin_attention_getters = new Ad_configuration($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //add new attention getter
				{
					if ($_REQUEST["b"])
					{
						if (!$admin_attention_getters->insert_attention_getter($db,$_REQUEST["b"]))
							$admin_attention_getters->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_attention_getters->attention_getters_form($db))
							$admin_attention_getters->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display current list
						if (!$admin_attention_getters->attention_getters_form())
							$admin_attention_getters->site_error(0, __FILE__, __LINE__);
					}
				}
				break;
				case 2: //delete attention getter
				{
					if ($_REQUEST["c"])
					{
						if (!$admin_attention_getters->delete_attention_getter($db,$_REQUEST["c"]))
							$admin_attention_getters->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_attention_getters->attention_getters_form($db))
							$admin_attention_getters->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						//display current list
						if (!$admin_attention_getters->attention_getters_form())
							$admin_attention_getters->site_error(0, __FILE__, __LINE__);
					}
				}
				break;
				default:
				{
					if (!$admin_attention_getters->attention_getters_form($db))
						$admin_attention_getters->site_error(0, __FILE__, __LINE__);
				}
			} //enn of switch ($_REQUEST["z"])
			$admin_attention_getters->display_page($db);
			break;

		case 67:
			//edit category dropdown questions
			include("admin_registration_configuration_class.php");
			$admin_question_dropdowns = new Registration_configuration($db, $product_configuration);
			if (($_REQUEST["e"]) && ($_REQUEST["b"]))
			{
				//insert the new dropdown
				$returned = $admin_question_dropdowns->insert_new_dropdown($db,$_REQUEST["b"]);
				if (!$returned)
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$returned))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["e"])
			{
				//insert new dropdown form
				if (!$admin_question_dropdowns->new_dropdown_form())
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["d"]) && ($_REQUEST["z"]))
			{
				//handle the type of delete
				//either change id
				//or just delete from sell questions
				if (!$admin_question_dropdowns->delete_dropdown($db,$_REQUEST["d"],$_REQUEST["z"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->show_all_dropdowns($db))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["d"])
			{
				//delete this question dropdown
				//delete all questions that rely on this dropdown
				//show list before delete?
				if (!$admin_question_dropdowns->delete_dropdown_intermediate($db,$_REQUEST["d"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["g"]) && ($_REQUEST["c"]))
			{
				//delete from this dropdown
				if (!$admin_question_dropdowns->delete_dropdown_value($db,$_REQUEST["g"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				//add to this dropdown
				if (!$admin_question_dropdowns->add_dropdown_value($db,$_REQUEST["c"],$_REQUEST["b"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
						$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["c"])
			{
				//edit this question dropdown
				if (!$admin_question_dropdowns->edit_dropdown($db,$_REQUEST["c"]))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_question_dropdowns->show_all_dropdowns($db))
					$admin_question_dropdowns->site_error(0, __FILE__, __LINE__);
			}
			$admin_question_dropdowns->display_page($db);
			break;

		case 68:
			//filter management
			include("admin_filter_class.php");
			$admin_filter = new Admin_filter($db, $product_configuration);
			if (($_REQUEST["x"]) && ($_REQUEST["y"]))
			{
				if (!$admin_filter->delete_filter($db,$_REQUEST["x"],$_REQUEST["y"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_filter->browse($db,$_REQUEST["b"]))
						$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["x"])
			{
				//go to verify deletion
				if (!$admin_filter->delete_filter_check($db,$_REQUEST["x"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["m"])
			{
				//update filter switch
				if (!$admin_filter->update_filter_switch($db,$_REQUEST["m"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
				elseif (!$admin_filter->browse($db,$_REQUEST["b"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["j"]) && ($_REQUEST["k"]))
			{
				if (!$admin_filter->update_filter_associations($db,$_REQUEST["j"],$_REQUEST["k"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
				elseif (!$admin_filter->browse($db,$_REQUEST["b"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["i"]) && ($_REQUEST["g"]))
			{
				if (!$admin_filter->insert_filter($db,$_REQUEST["g"],$_REQUEST["j"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_filter->browse($db,$_REQUEST["b"]))
						$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["i"])
			{
				//go to verify deletion
				if (!$admin_filter->display_filter_form ($db,$_REQUEST["j"],$_REQUEST["k"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["e"]) && ($_REQUEST["b"]) && ($_REQUEST["f"]) && ($_REQUEST["g"]))
			{
				//update filter
				if (!$admin_filter->update_filter($db,$_REQUEST["b"],$_REQUEST["f"],$_REQUEST["g"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
				elseif (!$admin_filter->browse($db,$_REQUEST["b"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["e"]) && ($_REQUEST["b"]))
			{
				//edit filter
				if (!$admin_filter->edit_filter_form($db,$_REQUEST["b"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_filter->browse($db,$_REQUEST["b"]))
					$admin_filter->site_error(0, __FILE__, __LINE__);
			}
			$admin_filter->display_page($db);
			break;

		case 69:
			//insert new user
			include("admin_user_management_class.php");
			$admin_user = new Admin_user_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //insert user
					if ($_REQUEST["c"])
					{
						$admin_user->get_form_variables($_REQUEST["c"]);
						if ($admin_user->check_info($db))
						{
							if (!$admin_user->insert_new_user($db,$_REQUEST["c"]))
							{
								if (!$admin_user->insert_new_user_form($db))
									$admin_user->site_error(0, __FILE__, __LINE__);
							}
							else
							{
								if (!$admin_user->display_user_data($db,$admin_user->new_user_id))
									$admin_user->site_error(0, __FILE__, __LINE__);
							}
						}
						else
							if (!$admin_user->insert_new_user_form($db))
								$admin_user->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_user->insert_new_user_form($db))
							$admin_user->site_error(0, __FILE__, __LINE__);
					}
				break;
				default:
					if (!$admin_user->insert_new_user_form($db))
						$admin_user->site_error(0, __FILE__, __LINE__);
			}
			$admin_user->display_page($db);
			break;

		case 70:
			//reset category counts
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (!$admin_category->reset_all_category_counts($db))
				$admin_category->category_error();
			elseif (!$admin_category->browse($db))
					$admin_category->category_error();
			$admin_category->display_page($db);
			break;

		case 71:
			//transaction
			include("admin_transaction_management_class.php");
			$admin_transactions = new Transaction_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //approve renewal transaction
					if (!$admin_transactions->approve_subscription_renewal($db,$_REQUEST["b"]))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_transactions->unapproved_transactions_list($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
				break;
				case 2: //delete renewal transaction
					if (!$admin_transactions->delete_subscription_renewal($db,$_REQUEST["b"]))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_transactions->unapproved_transactions_list($db))
						$admin_transactions->site_error(0, __FILE__, __LINE__);
				break;
				default:
					$admin_transactions->transaction_home($db);
			}
			$admin_transactions->display_page($db);
			break;

		case 72:
			//copy subcategory data to new subcategory
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				$new_category = $admin_category->duplicate_category_structure($db,$_REQUEST["b"],$_REQUEST["c"],$_REQUEST["d"],$_REQUEST["e"]);
				if ($new_category)
				{
					if (!$admin_category->browse($db,$_REQUEST["c"]))
						$admin_category->category_error();
				}
				else
					$admin_category->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display copy subcategory form
				if (!$admin_category->duplicate_structure_form($db))
					$admin_category->site_error(0, __FILE__, __LINE__);
			}
			$admin_category->display_page($db);
			break;

		case 73:
			//copy category specific questions from one category to another
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if ($admin_category->duplicate_category_questions($db,$_REQUEST["b"],$_REQUEST["c"]))
				{
					if (!$admin_category->browse($db,$_REQUEST["c"]))
						$admin_category->category_error();
				}
				else
					$admin_category->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				//display copy subcategory form
				if (!$admin_category->duplicate_questions_form($db))
					$admin_category->site_error(0, __FILE__, __LINE__);
			}
			$admin_category->display_page($db);
			break;

		case 74:
			//view a module
			include("admin_pages_class.php");
			$admin_pages = new Admin_pages($db, $product_configuration);
			if ($_REQUEST["b"] && $_REQUEST["c"])
			{
				if (!$admin_pages->update_module_specifics($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_pages->site_error(0, __FILE__, __LINE__);
				else
				{
					if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
						$admin_pages->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_pages->display_current_page($db,$_REQUEST["b"]))
					$admin_pages->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_pages->show_modules($db))
					$admin_pages->site_error(0, __FILE__, __LINE__);
			}
			$admin_pages->display_page($db);
			break;

		case 75:
			//administer discount codes
			include("admin_price_plan_management_class.php");
			$admin_discounts = new Price_plan_management($db, $product_configuration);
			switch ($_REQUEST["b"])
			{
				case 1: //insert new discount code
					if ($_REQUEST["d"])
					{
						if (!$admin_discounts->insert_discount_code($db,$_REQUEST["d"]))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_discounts->display_discount_code_list($db))
								$admin_discounts->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						if (!$admin_discounts->edit_discount_code($db))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
					}

				break;
				case 2: //edit discount code
					if (($_REQUEST["c"]) && ($_REQUEST["d"]))
					{
						if (!$admin_discounts->update_discount_code($db,$_REQUEST["c"],$_REQUEST["d"]))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_discounts->display_discount_code_list($db))
								$admin_discounts->site_error(0, __FILE__, __LINE__);
						}
					}
					elseif ($_REQUEST["c"])
					{
						if (!$admin_discounts->edit_discount_code($db,$_REQUEST["c"]))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_discounts->display_discount_code_list($db))
								$admin_discounts->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						if (!$admin_discounts->display_discount_code_list($db))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
					}
				break;
				case 3: //delete discount code
					if ($_REQUEST["c"])
					{
						if (!$admin_discounts->delete_discount_code($db,$_REQUEST["c"]))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
						else
						{
							if (!$admin_discounts->display_discount_code_list($db,$_REQUEST["c"]))
								$admin_discounts->site_error(0, __FILE__, __LINE__);
						}
					}
					else
					{
						if (!$admin_discounts->display_discount_code_list($db))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
					}
				break;
				case 4: //view add associated with discount code
					if ($_REQUEST["c"])
					{
						if (!$admin_discounts->display_discount_code_ads($db,$_REQUEST["c"],$page))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
					}
					else
					{
						if (!$admin_discounts->display_discount_code_list($db))
							$admin_discounts->site_error(0, __FILE__, __LINE__);
					}
				break;
				default: //show discount code list
					if (!$admin_discounts->display_discount_code_list($db))
						$admin_discounts->site_error(0, __FILE__, __LINE__);
				break;
			}
			$admin_discounts->display_page($db);
			break;

		case 76:
			//administer a users account balance
			include("admin_user_management_class.php");
			$admin_balance = new Admin_user_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_balance->update_account_balance($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_balance->site_error(0, __FILE__, __LINE__);
				else
				{
					if (!$admin_balance->display_user_data($db,$_REQUEST["b"]))
						$admin_balance->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif ($_REQUEST["b"])
			{
				//user id exists
				if (!$admin_balance->edit_account_balance($db,$_REQUEST["b"]))
					$admin_balance->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_balance->list_users($db))
					$admin_balance->site_error(0, __FILE__, __LINE__);
				elseif (!$admin_auth->edit_admin_login_form($db))
				{
					$site->admin_home($db);
					//$admin_auth->auth_error();
				}
			}
			$admin_balance->display_page($db);
			break;

		case 77:
			//approve/delete/view an account balance transaction
			include("admin_transaction_management_class.php");
			$admin_balance = new Transaction_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //approve a balance transaction
					if (!$admin_balance->approve_balance_transaction($db,$_REQUEST["b"]))
						$admin_balance->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_balance->unapproved_transactions_list($db))
						$admin_balance->site_error(0, __FILE__, __LINE__);
				break;

				case 2: //delete a balance transaction
					if (!$admin_balance->delete_balance_transaction($db,$_REQUEST["b"]))
						$admin_balance->site_error(0, __FILE__, __LINE__);
					elseif (!$admin_balance->unapproved_transactions_list($db))
						$admin_balance->site_error(0, __FILE__, __LINE__);
				break;

				default:
					if (!$admin_balance->unapproved_transactions_list($db))
						$admin_balance->site_error(0, __FILE__, __LINE__);
			}
			$admin_balance->display_page($db);
			break;

		case 78:
			//invoicing system
			include("admin_invoicing_class.php");
			include("admin_user_management_class.php");
			$admin_invoice = new Admin_invoicing($db, $product_configuration);
			$admin_user = new Admin_user_management($db, $product_configuration);
			switch ($_REQUEST["z"])
			{
				case 1: //mark invoice unpaid
					if ($_REQUEST["b"] && $_REQUEST["c"])
					{
						if (!$admin_invoice->mark_invoice_unpaid($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_user->display_user_data($db,$_REQUEST["c"]))
								$admin_user->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["c"])
					{
						if (!$admin_user->display_user_data($db,$_REQUEST["c"]))
								$admin_user->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_invoice->unpaid_invoices($db))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 2: //mark invoice paid
					if ($_REQUEST["b"] && $_REQUEST["c"])
					{
						if (!$admin_invoice->mark_invoice_paid($db,$_REQUEST["b"],$_REQUEST["c"]))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_user->display_user_data($db,$_REQUEST["c"]))
								$admin_user->site_error(0, __FILE__, __LINE__);
					}
					elseif ($_REQUEST["c"])
					{
						if (!$admin_user->display_user_data($db,$_REQUEST["c"]))
								$admin_user->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_invoice->paid_invoices($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 3: //create invoices
					if (!$admin_invoice->create_invoices($db,$_REQUEST["b"]))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 4: //search for an invoice
					if ($_REQUEST["b"])
					{
						if (!$admin_invoice->search_invoices($db,$_REQUEST["b"]))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_invoice->invoicing_home($db))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_invoice->invoicing_home($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 5: //set account cutoff
					if ($_REQUEST["b"])
					{
						if (!$admin_invoice->update_invoice_cutoff($db,$_REQUEST["b"]))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
						elseif (!$admin_invoice->invoicing_home($db))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_invoice->invoice_cutoff_form($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 6: //display paid invoices
					if (!$admin_invoice->paid_invoices($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 7: //display unpaid invoices
					if (!$admin_invoice->unpaid_invoices($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 8: //invoice details
					if ($_REQUEST["b"])
					{
						if (!$admin_invoice->show_invoice($db,$_REQUEST["b"]))
							$admin_invoice->site_error(0, __FILE__, __LINE__);
					}
					elseif (!$admin_invoice->invoicing_home($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				case 9:	// Set max invoice limits
					if(!$admin_invoice->update_max_invoice($db, $_REQUEST["b"]))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
					elseif(!$admin_invoice->invoicing_home($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
				break;

				default:
					if (!$admin_invoice->invoicing_home($db))
						$admin_invoice->site_error(0, __FILE__, __LINE__);
			}
			$admin_invoice->display_page($db);
			break;

		case 79:
			//view a module by section
			include_once("admin_pages_class.php");
			$admin_pages = new Admin_pages($db, $product_configuration);
			if ($_REQUEST["b"])
			{
				if (!$admin_pages->browse_module_sections($db,$_REQUEST["b"]))
					$admin_pages->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_pages->browse_module_sections($db))
					$admin_pages->site_error(0, __FILE__, __LINE__);
			}
			$admin_pages->display_page($db);
			break;
			
		case 80:
			//edit ip ban list
			include("admin_site_configuration_class.php");
			$admin_configuration = new Site_configuration($db);
			if ($_REQUEST["c"])
			{
				//delete an ip from the ban list	
				if (!$admin_configuration->delete_ip_from_list($db,$_REQUEST["c"]))
					$admin_configuration->site_error();				
			}
			if ($_REQUEST["b"])
			{
				//insert an ip into the ban list
				if (!$admin_configuration->insert_ip_to_ban_list($db,$_REQUEST["b"]))
					$admin_configuration->site_error();
			}
			$admin_configuration->ban_ip_form($db);
			$admin_configuration->display_page($db);
			break;					

		case 99:
			//remove before release
			//insert new messages
			include("admin_text_management_class.php");
			$admin_text = new Text_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_text->insert_new_message($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_text->new_message_form($db,$_REQUEST["b"]))
						$admin_text->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_text->new_message_form($db,$_REQUEST["b"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["d"]) && ($_REQUEST["e"]))
			{
				if (!$admin_text->update_message_name_and_description($db,$_REQUEST["d"],$_REQUEST["e"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_text->name_and_description_form($db,$_REQUEST["d"]))
						$admin_text->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["d"])
			{
				if (!$admin_text->name_and_description_form($db,$_REQUEST["d"]))
					$admin_text->site_error(0, __FILE__, __LINE__);
			}
			$admin_text->display_page($db);
			break;

		case 100:
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			$admin_category->migrate_languages_categories($db);

		case 101:
			//remove before release
			//edit subpages
			include("admin_font_management_class.php");
			$admin_font = new Font_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_font->update_subpage_message_name_and_description($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
				elseif (!$admin_font->choose_subpage_to_edit($db))
						$admin_font->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_font->subpage_name_and_description_form($db,$_REQUEST["b"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
			}
			else
			{
				if (!$admin_font->choose_subpage_to_edit($db))
					$admin_font->site_error(0, __FILE__, __LINE__);
			}
			$admin_font->display_page($db);
			break;

		case 102:
			//remove before release
			//insert new messages
			include("admin_font_management_class.php");
			$admin_font = new Font_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_font->insert_new_subpage($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_font->new_subpage_form($db,$_REQUEST["b"]))
						$admin_font->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_font->new_subpage_form($db,$_REQUEST["b"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
			}
			break;

		case 103:
			//remove before release
			//insert new messages
			include("admin_font_management_class.php");
			$admin_font = new Font_management($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_font->insert_new_message($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_font->new_message_form($db,$_REQUEST["b"]))
						$admin_font->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_font->new_message_form($db,$_REQUEST["b"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
			}
			elseif (($_REQUEST["d"]) && ($_REQUEST["e"]))
			{
				if (!$admin_font->update_message_name_and_description($db,$_REQUEST["d"],$_REQUEST["e"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
				else
					if (!$admin_font->name_and_description_form($db,$_REQUEST["d"]))
						$admin_font->site_error(0, __FILE__, __LINE__);
			}
			elseif ($_REQUEST["d"])
			{
				if (!$admin_font->name_and_description_form($db,$_REQUEST["d"]))
					$admin_font->site_error(0, __FILE__, __LINE__);
			}
			$admin_font->display_page($db);
			break;

		case 104:
			//log this user out
			if ($user_id)
			{
				//destroy the cookie
				setcookie("admin_classified_session","",0,"/",$_SERVER["HTTP_HOST"]);
				$sql_query = "delete from geodesic_sessions where classified_session = \"".$_COOKIE["admin_classified_session"]."\"";
				$delete_session_result = &$db->Execute($sql_query);
				//echo $sql_query." is the query<br>\n";
				if (!$delete_session_result)
				{
					//echo $sql_query."<br>\n";
					return false;
				}
				//$site->admin_header($db);
				$admin_auth = new Admin_auth($db, $product_configuration);
				$admin_auth->admin_login_form($db);
				//$site->admin_footer($db);
			}
			else
			{
				//$site->admin_header($db);
				$admin_auth = new Admin_auth($db, $product_configuration);
				$admin_auth->admin_login_form($db);
				//$site->admin_footer($db);
			}
			exit;
			break;

		case 105:
			//delete a category's subcategories
			//deletes a category and all categories underneath
			//moves all items in any of these categories into the parent category of the intended category to be deleted
			include("admin_categories_class.php");
			$admin_category = new Admin_categories($db, $product_configuration);
			if (($_REQUEST["b"]) && ($_REQUEST["c"]))
			{
				if (!$admin_category->delete_subcategories($db,$_REQUEST["b"],$_REQUEST["c"]))
					$admin_category->category_error();
				else
				{
					if (!$admin_category->browse($db))
						$admin_category->category_error();
				}
			}
			elseif ($_REQUEST["b"])
			{
				if (!$admin_category->delete_subcategory_check($db,$_REQUEST["b"]))
					$admin_category->category_error();
			}
			else
			{
				if (!$admin_category->browse($db))
					$admin_category->category_error();
			}
			$admin_category->display_page($db);
			break;

		case 108:
			// CSS Management section
			// Skipped the past 3 to maintain complete compatibility with auctions enterprise
			include("admin_css_management.php");
			$css = new css_management($db, $product_configuration);

			if($_REQUEST["b"])
			{
				switch($_REQUEST["b"])
				{
					case 1:
						$css->search($db, $_REQUEST["z"]);
						break;
					case 2:
						if(!$css->check_page($db, $_REQUEST["c"]))
							$css->site_error(0, __FILE__, __LINE__);
						break;
					case 3:
						if(!$css->edit_all($db, $_REQUEST["c"]))
							$css->site_error(0, __FILE__, __LINE__);
						break;
					case 4:
						if(!$css->update_all($db, $_REQUEST["c"], $_REQUEST["z"]))
							$css->site_error(0, __FILE__, __LINE__);
						if(!$css->show_all_css_forms($db))
							$css->site_error(0, __FILE__, __LINE__);
						break;
					case 5:
						if(!$css->edit_all($db, "font_all"))
							$css->site_error(0, __FILE__, __LINE__);
						break;
					case 6:
						if(!$css->edit_all($db, "color_all"))
							$css->site_error(0, __FILE__, __LINE__);
						break;
					default:
						if(!$css->show_all_css_forms($db))
							$css->site_error(0, __FILE__, __LINE__);
						break;
				}
			}
			else
			{
				if(!$css->show_all_css_forms($db))
					$css->site_error(0, __FILE__, __LINE__);
			}
			$css->display_page($db);
			break;

		case 109:
			//search text page
			include("admin_text_management_class.php");
			$search_text = new Text_management($db, $product_configuration);
			$search_text->search_text_page($db, $_REQUEST["search_type"]);
			$search_text->display_page($db);
			break;

		case 110:
			// Feedback section
			include_once("admin_feedback_class.php");
			$admin_feedback = new Admin_Feedback($db, $product_configuration);

			if($_REQUEST["b"] && $_REQUEST["c"] &&  $_REQUEST["d"])
			{
				// Update edited feedback
				if(!$admin_feedback->update_edit_feedback($db, $_REQUEST["b"], $_REQUEST["c"], $_REQUEST["d"]))
					$admin_feedback->feedback_error();
				else
				{
					include_once("admin_user_management_class.php");
					$admin_user = new Admin_user_management($db, $product_configuration);

					if (!$admin_user->display_user_data($db,$_REQUEST["c"]))
						$admin_user->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif($_REQUEST["e"])
			{
				// Delete feedback
				if(!$admin_feedback->delete_feedback($db, $_REQUEST["b"], $_REQUEST["c"], $_REQUEST["e"]))
					$admin_feedback->feedback_error();
				else
				{
					include_once("admin_user_management_class.php");
					$admin_user = new Admin_user_management($db, $product_configuration);

					if (!$admin_user->display_user_data($db,$_REQUEST["e"]))
						$admin_user->site_error(0, __FILE__, __LINE__);
				}
			}
			elseif($_REQUEST["b"] && $_REQUEST["c"])
			{
				// Edit feedback form
				if(!$admin_feedback->edit_feedback_form($db, $_REQUEST["b"], $_REQUEST["c"]))
					$admin_feedback->feedback_error();
			}
			elseif($_REQUEST["b"])
			{
				// Update feedback
				if(!$admin_feedback->update_feedback_icon($db, $_REQUEST["b"]))
					$admin_feedback->feedback_error();
				if(!$admin_feedback->display_icon_info($db))
					$admin_feedback->feedback_error();
			}
			elseif($_REQUEST["z"])
			{
				// Display the icon form
				if(!$admin_feedback->display_icon_form($db))
					$admin_feedback->feedback_error();
			}
			elseif($_REQUEST['feedback'])
			{
				if(!$admin_feedback->update_feedback_settings($db, $_REQUEST['feedback']))
					$admin_feedback->feedback_error();
				elseif(!$admin_feedback->display_icon_info($db))
					$admin_feedback->feedback_error();
			}
			else
			{
				// Display the icon info
				if(!$admin_feedback->display_icon_info($db))
					$admin_feedback->feedback_error();
			}
			$admin_feedback->display_page($db);
			break;

		case 200:
			if(is_file('bulk_uploader/bulk_uploader_class.php'))
			{
				include_once('bulk_uploader/bulk_uploader_class.php');
				// Check variables and put the correct ones into the request variable
				if(!$_REQUEST["category_id"])
					$_REQUEST["category_id"] = $_REQUEST["c"]["category_id"];
				if(!$_REQUEST["listing_type"])
					$_REQUEST["listing_type"] = $_REQUEST["c"]["listing_type"];
				$bulk = new bulk_uploader($db, $_REQUEST["category_id"], $product_configuration, $_REQUEST['listing_type']);

				if($_REQUEST['action'])
				{
					switch($_REQUEST['action'])
					{
						case 'show_file_block':
							$bulk->getFileBlockCode();
							break;
						case 'upload':
							$bulk->upload_file($_REQUEST["c"], $_FILES);
							$bulk->display_uploader(2, $_REQUEST["c"]);
							$bulk->display_page($db);
							break;
						case 'change_dropdown':
							$bulk->update_dropdown($_REQUEST["fields"], $_REQUEST["bottom"], $_REQUEST["div"], $_REQUEST['second_value']);
							break;
						case 'LoadProfile':
							$bulk->load_profile($_REQUEST['profile']);
							break;
						case 'update_result':
							// TODO Implement a function in the class to handle this
							break;
						case 'submit_iframe':
							$bulk->save_iframe_variables($_REQUEST['bottom'], $_REQUEST['prevalue'], $_REQUEST['duration'], $_REQUEST['skip']);
							break;
						case 'run_queries':
							$bulk->run_queries($_REQUEST['start'], $_REQUEST['user_id'], $_REQUEST["title"], $_REQUEST['category_id'], $_REQUEST['listing_type'], $_REQUEST['filename']);
							break;
						case 'update_results':
							$bulk->update_results($_REQUEST['old_size'], $_REQUEST['new_size']);
							break;
						case 'update_username':
							$bulk->send_username($_REQUEST['user_id']);
							break;
						case 'update_user_id':
							$bulk->send_user_id($_REQUEST['username']);
							break;
						case 'save_new_profile':
							$bulk->save_new_profile($_REQUEST["c"]);
							break;
						case 'save_profile_changes':
							$bulk->save_profile_changes($_REQUEST["c"]);
							break;
						case 'delete_log':
							$bulk->delete_log($_REQUEST['log_id']);
							$bulk->display_uploader();
							$bulk->display_page($db);
							break;
					}
				}
				else
				{
					// Display the first page
					$bulk->display_uploader();
					$bulk->display_page($db);
				}
			}
			else
				$site->admin_home($db);
			break;


//STOREFRONT CODE
		case 201:
			// StoreFront
			if(is_file("storefront/admin_store.php") && is_file("storefront/admin_store_management.php"))
			{
				include("storefront/admin_store.php");
				include("storefront/admin_store_management.php");
				$admin_store_management = new Admin_store_management($db, $product_configuration);
				switch($_REQUEST["b"])
				{
					//templates
					case 1:
						if(isset($_POST["templateSubmit"]))
						{
							if($admin_store_management->adminStoreTemplatesUpdate($db,$_POST["templateType"],$_POST["storefrontTemplateDefault"]))
								$admin_store_management->adminStoreTemplates($db);
						}
						else
							$admin_store_management->adminStoreTemplates($db);
                            $admin_store_management->display_page($db);
						break;
					case 2:
						if(isset($_POST["displayLinkSubmit"]))
						{
							if($admin_store_management->adminUseStorefrontLinkUpdate($db,$_POST["site_default"],$_POST["displayStorefrontLink"],$_POST["applyToSubCategories"]))
								$admin_store_management->adminUseStorefrontLink($db);
						}
						else
							$admin_store_management->adminUseStorefrontLink($db);
						$admin_store_management->display_page($db);
						break;
					case 3:
						if(isset($_POST["submitFieldsToUse"]))
						{
							if($admin_store_management->adminStorefrontFieldsToUseUpdate($db,$_POST["c"]))
								$admin_store_management->adminStorefrontFieldsToUse($db);
						}
						else
							$admin_store_management->adminStorefrontFieldsToUse($db);
						$admin_store_management->display_page($db);
						break;
					case 4:
						switch($_REQUEST["c"])
						{
							case "add":
								if(isset($_POST["addNewChoice"]))
								{
									if(!$admin_store_management->insert_subscription_period($db,$_POST["d"]))
										$admin_store_management->subscription_period_form($db);
									else
										$admin_store_management->display_subscription_periods($db);
								}
								else
									$admin_store_management->subscription_period_form($db);
								break;
							case "edit":
								if(isset($_POST["editChoice"])&&$_REQUEST["h"])
								{
									if(!$admin_store_management->insert_subscription_period($db,$_POST["d"],$_REQUEST["h"]))
										$admin_store_management->subscription_period_form($db,$_REQUEST["h"]);
									else
										$admin_store_management->display_subscription_periods($db);
								}
								elseif($_REQUEST["h"])
									$admin_store_management->subscription_period_form($db,$_REQUEST["h"]);
								else
									$admin_store_management->display_subscription_periods($db);
								break;
							case "delete":
								if(isset($_REQUEST["h"]))
								{
									$admin_store_management->delete_subscription_period($db,$_REQUEST["h"]);
								}
								$admin_store_management->display_subscription_periods($db);
								break;
							default:
								$admin_store_management->display_subscription_periods($db);
								break;
						}
						$admin_store_management->display_page($db);
						break;
					default:
						$admin_store_management->storefront_home($db);
						$admin_store_management->display_page($db);
						break;
				}
			}
			else
				$site->admin_home($db);
			break;
//STOREFRONT CODE

		case 202:
			if(is_file('user_import/user_import_class.php'))
			{
				include_once('user_import/user_import_class.php');
				$user_import = new user_import($db, $product_configuration);
				if($_REQUEST['action'])
				{
					switch ($_REQUEST['action'])
					{
						case 'submit_first_page':
							$user_import->upload_file($_FILES, $_REQUEST['c']);
							$user_import->display_second_page($_REQUEST['c']);
							break;
						case 'update_dropdown':
							$user_import->update_dropdowns($_REQUEST['c']);
							break;
						case 'update_results':
							$user_import->update_results($_REQUEST['old_size'], $_REQUEST['new_size']);
							break;
						case 'final_page':
							$user_import->final_page($_REQUEST['c']);
							break;
						case 'perform_user_import':
							$user_import->perform_user_import($_REQUEST['start'], $_REQUEST['c']);
							break;
						case 'insert_id_list':
							$user_import->insert_id_list($_REQUEST['id_list']);
							break;
						case 'undo_import':
							$user_import->undo_insert($_REQUEST['import_id']);
							$user_import->display_first_page();
							break;
						default:
							$site->admin_home($db);
							break;
					}
				}
				else
				{
					// Display the first page
					$user_import->display_first_page();
				}
			}
			else
				$site->admin_home($db);
			break;

        case 300:
            require_once("admin_auction_reports_class.php");
            $admin_listing = new Admin_auction_reports($db, $product_configuration);
            if (!empty($_REQUEST['b']) && ($_REQUEST['c'] == 1))
            {
            	switch($_REQUEST['b'])
            	{
            		case 1:
                	if(!($admin_listing->show_current_bids($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                	case 2:
                    if(!($admin_listing->show_items_with_no_bids($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                	case 3:
                    if(!($admin_listing->show_completed_auctions($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                	case 4:
                    if(!($admin_listing->show_email_report_1($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                    case 5:
                    if(!($admin_listing->show_email_report_2($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                    case 6:
                    if(!($admin_listing->show_email_report_3($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                    case 7:
                    if(!($admin_listing->show_email_report_4($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                }
                $admin_listing->display_page($db);
            }
            elseif (!empty($_REQUEST['b']) && ($_REQUEST['c'] == 2))
            {
                switch($_REQUEST['b'])
                {
                    case 1:
                    if(!($admin_listing->download_current_bids($db)))
                    {
                    	//$admin_listing->site_error(0, __FILE__, __LINE__);
                    	$admin_listing->display_page($db);
                    }
                    break;
                    case 2:
                    if(!($admin_listing->download_items_with_no_bids($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 3:
                    if(!($admin_listing->download_completed_auctions($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 4:
                    if(!($admin_listing->download_email_report_1($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 5:
                    if(!($admin_listing->download_email_report_2($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 6:
                    if(!($admin_listing->download_email_report_3($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 7:
                    if(!($admin_listing->download_email_report_4($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                }
            }
            else
            {
                if (!$admin_listing->listing_home($db))
                {
                    $site->admin_home($db);
                    //$admin_auth->auth_error();
                    break;
                }
                $admin_listing->display_page($db);
            }
            break;

        case 301:
            require_once("admin_classified_reports_class.php");
            $admin_listing = new Admin_classified_reports($db, $product_configuration);
            if (!empty($_REQUEST['b']) && ($_REQUEST['c'] == 1))
            {
                switch($_REQUEST['b'])
                {
                    case 1:
                    if(!($admin_listing->show_closed_classifieds_report($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                    case 2:
                    if(!($admin_listing->show_open_classifieds_report($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                    case 3:
                    if(!($admin_listing->show_email_report_1($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                    case 4:
                    if(!($admin_listing->show_email_report_2($db)))
                    $admin_listing->site_error(0, __FILE__, __LINE__);
                    break;
                }
                $admin_listing->display_page($db);
            }
            elseif (!empty($_REQUEST['b']) && ($_REQUEST['c'] == 2))
            {
                switch($_REQUEST['b'])
                {
                    //Classifieds Reports
                    case 1:
                    if(!($admin_listing->download_closed_classifieds_report($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 2:
                    if(!($admin_listing->download_open_classifieds_report($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 3:
                    if(!($admin_listing->download_email_report_1($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                    case 4:
                    if(!($admin_listing->download_email_report_2($db)))
                    {
                        //$admin_listing->site_error(0, __FILE__, __LINE__);
                        $admin_listing->display_page($db);
                    }
                    break;
                }
            }
            else
            {
                if (!$admin_listing->listing_home($db))
                {
                    $site->admin_home($db);
                    //$admin_auth->auth_error();
                    break;
                }
                $admin_listing->display_page($db);
            }
            break;

        case 302:
            require_once("admin_listings_admin_class.php");
            $admin_listings = new Admin_listings_admin($db, $product_configuration);
            if($_REQUEST['b'])
            {
                switch($_REQUEST['b'])
                {
                	case 1:
                	if(!($admin_listings->show_classifieds_to_auctions($db)))
                	$admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                	break;
                	case 2:
                	if(!($admin_listings->classifieds_to_auctions($db)))
                	$admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                	break;
                    case 3:
                    if(!($admin_listings->show_auctions_to_classifieds($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                    case 4:
                    if(!($admin_listings->auctions_to_classifieds($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                    case 5:
                    if(!($admin_listings->sold_classifieds($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                    case 6:
                    if(!($admin_listings->show_delete_inventory($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                    case 7:
                    if(!($admin_listings->do_delete_inventory($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                    case 8:
                    if(!($admin_listings->show_missing_images($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                    case 9:
                    if(!($admin_listings->do_missing_images($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                }
            }
            else
            {
                if (!$admin_listings->listings_home($db))
                {
                    $site->admin_home($db);
                    //$admin_auth->auth_error();
                    break;
                }
                $admin_listings->display_page($db);
            }
            break;

        case 303:
            require_once("admin_registered_users_class.php");
            $admin_registered = new Admin_show_registered_users($db, $product_configuration);
            if(!($admin_registered->show_user_list($db))) $admin_registered->site_error(0, __FILE__, __LINE__);
            break;

        case 304:
            require_once("admin_listings_admin_class.php");
            $admin_listings = new Admin_listings_admin($db, $product_configuration);
            if($_REQUEST['b'])
            {
                switch($_REQUEST['b'])
                {
                    case 1:
                    if(!($admin_listings->show_new_end_date($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                    case 2:
                    if(!($admin_listings->process_new_end_date($db)))
                    $admin_listings->site_error(0, __FILE__, __LINE__);
                    $admin_listings->display_page($db);
                    break;
                }
            }
            else
            {
                $site->admin_home($db);
                $admin_listings->display_page($db);
            }
            break;

        case 1616:
            //search users
            include("admin_user_management_class.php");
            $admin_search = new Admin_user_management($db, $product_configuration);
            if ($_REQUEST["b"] == 1 && !empty($_REQUEST['from']) && !empty($to))
            {
                //search users
                if (!$admin_search->show_user_emails($db,$_REQUEST["b"]))
                    $admin_search->site_error(0, __FILE__, __LINE__);
            	$admin_search->display_page($db);
            }
            elseif ($_REQUEST["b"] == 1 && empty($_REQUEST['from']) && empty($to))
            {
                //search users
                if (!$admin_search->show_user_emails_all($db,$_REQUEST["b"]))
                    $admin_search->site_error(0, __FILE__, __LINE__);
                $admin_search->display_page($db);
            }
            elseif ($_REQUEST["b"] == 2 && !empty($_REQUEST['from']) && !empty($to))
            {
                //search users
                $res = 0;
                if ($admin_search->download_user_emails($db,$_REQUEST["b"], $res))
                {
                    if(!$res) $admin_search->display_page($db);
                }
                else
                {
                    $admin_search->site_error(0, __FILE__, __LINE__);
                    $admin_search->display_page($db);
                }
            }
            elseif ($_REQUEST["b"] == 2 && empty($_REQUEST['from']) && empty($to))
            {
                //search users
                $res = 0;
                if ($admin_search->download_user_emails_all($db,$_REQUEST["b"], $res))
                {
                    if(!$res) $admin_search->display_page($db);
                }
                else
                {
                    $admin_search->site_error(0, __FILE__, __LINE__);
                    $admin_search->display_page($db);
                }
            }
            else
            {
                //display the simple and advanced search box
                $admin_search->show_form($db);
                $admin_search->display_page($db);
            }
            break;

		case 100000:
			//display api installations information
			if (is_file("./api_admin_site_class.php"))
			{
				include("api_admin_site_class.php");
				$admin_api = new Api_admin_site();
				if ($admin_api->connected)
				{
					$admin_api->list_installations();
				}
			}
			else
			{
				$site->title = "API Installations Management";
				$site->body = "<span class=medium_font>If you wish to integrate your Geodesic Software with another installation or one of the other pre-configured
					products, please contact sales at sales@geodesicsolutions.com.<br><br></span>";
				$site->display_page($db);
			}
			break;

		case 100001:
			//edit api installation information
			if (is_file("./api_admin_site_class.php"))
			{
				include("api_admin_site_class.php");
				$admin_api = new Api_admin_site();
				if (($_REQUEST["b"]) && ($_REQUEST["c"]))
				{
					if ($admin_api->update_installation($_REQUEST["b"],$_REQUEST["c"]))
					{
						$admin_api->list_installations();
					}
				}
				elseif ($_REQUEST["b"])
				{
					$admin_api->installation_form($_REQUEST["b"]);
				}
				else
				{
					$admin_api->list_installations();
				}
			}
			else
			{
				$site->body = "<span class=medium_font>If you wish to integrate your Geodesic Software with another installation or one of the other pre-configured
					products, please contact sales at sales@geodesicsolutions.com.<br><br></span>";
				$site->display_page($db);
			}
			break;

		case 100002:
			//insert new api installation information
			if (is_file("./api_admin_site_class.php"))
			{
				include("api_admin_site_class.php");
				$admin_api = new Api_admin_site();
				if ($_REQUEST["c"])
				{
					$admin_api->insert_new_installation($_REQUEST["c"]);
					$admin_api->list_installations();
				}
				else
				{
					$admin_api->installation_form();
				}
			}
			else
			{
				$site->body = "<span class=medium_font>If you wish to integrate your Geodesic Software with another installation or one of the other pre-configured
					products, please contact sales at sales@geodesicsolutions.com.<br><br></span>";
				$site->display_page($db);
			}
			break;

		case 100003:
			//insert new api installation information
			if (is_file("./api_admin_site_class.php"))
			{
				include("api_admin_site_class.php");
				$admin_api = new Api_admin_site();
				if ($_REQUEST["b"])
				{
					$admin_api->remove_installation($_REQUEST["b"]);
					$admin_api->list_installations();
				}
				else
				{
					$admin_api->list_installations();
				}
			}
			else
			{
				$site->body = "<span class=medium_font>If you wish to integrate your Geodesic Software with another installation or one of the other pre-configured
					products, please contact sales at sales@geodesicsolutions.com.<br><br></span>";
				$site->display_page($db);
			}
			break;

		case 100004:
			//check connection to api installation
			if (is_file("./api_admin_site_class.php"))
			{
				include("api_admin_site_class.php");
				$admin_api = new Api_admin_site();
				if ($_REQUEST["b"])
				{
					$admin_api->check_connection_to_installation_db($_REQUEST["b"]);
				}
				else
				{
					$admin_api->list_installations();
				}
			}
			else
			{
				$site->body = "<span class=medium_font>If you wish to integrate your Geodesic Software with another installation or one of the other pre-configured
					products, please contact sales at sales@geodesicsolutions.com.<br><br></span>";
				$site->display_page($db);
			}
			break;
		default:
			$site->admin_home($db);
			break;

	} //end of switch
}
elseif ($_REQUEST["b"])
{
	$admin_auth = new Admin_auth($db, $product_configuration);

	if ($debug)
	{
		echo $_REQUEST["b"]["username"]." is the username submitted<BR>\n";
		echo $_REQUEST["b"]["password"]." is the password submitted<bR>\n";
		echo $_COOKIE["admin_classified_session"]." is the cookie<BR>\n";
		echo $admin_classified_session." is the session value set<BR>\n";
		echo $_COOKIE["admin_classified_session"]." is the cookie-session value set<BR>\n";
	}

	if (!$user_id)
	{
		$authorized = $admin_auth->login($db,$_REQUEST["b"]["username"],$_REQUEST["b"]["password"], $_REQUEST["b"]["license_key"],$admin_classified_session);
		if ($authorized)
		{
			if ($_REQUEST['cookieexists'] == "false")
			{
				//javascript is not enabled, so send them back to the login form
				$admin_auth->auth_messages["javascript"] = 'You must enable javascript to run this admin.';
				$admin_auth->admin_login_form($db, $_REQUEST["b"]["username"], $_REQUEST["b"]["password"], $_REQUEST["b"]["license_key"]);
				exit;
			}
			if ($debug)
			{
				echo $authorized." is authorized<BR>\n";
			}

			// Redirect back to the index.php
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}
		else
		{
			$admin_auth->admin_login_form($db, $_REQUEST["b"]["username"], $_REQUEST["b"]["password"], $_REQUEST["b"]["license_key"]);
		}
	}
	else
	{
		$site->admin_header($db);
		$admin_auth->admin_login_form($db);
	}
	$site->admin_footer($db);

}
else
{
	$admin_auth = new Admin_auth($db, $product_configuration);
	$admin_auth->admin_login_form($db);
	exit;
}
?>