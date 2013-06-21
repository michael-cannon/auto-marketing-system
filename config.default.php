<?php
/**
 * Configuration file
 * Automotive Marketing System
 *
 * @author Michael Cannon <michael@peimic.com>
 * @version $Id: config.default.php,v 1.1.1.1 2010/04/15 09:42:34 peimic.comprock Exp $
 */

// central leads tables
$db_host						= 'localhost';
$db_username					= 'movingir_statist';
$db_password					= 'vejocrud';
$db_name						= 'movingir_statistic';
$db_prefix						= 'movingir_';

// notifications
$adminEmail						= 'michael+nl@peimic.com';
$bccEmail						= $adminEmail;
$designerEmail					= 'Nicole Johnson <nicole@ndezine.com>';
$designerEmail					= $adminEmail;
$videoEmail						= 'Robert Sharpe <robert@doublesharpe.com>';
$videoEmail						= $adminEmail;

// control panel settings
$cpanelUrl						= 'http://movingiron.com:2082/frontend/x3';
$cpUrl							= 'http://www.movingiron.com/cp';
$cpLogo							= 'images/mi_logo.gif';
$cpEmail						= 'tyler@intelauto.com';
$cpEmailBase					= '@intelauto.com';
$cpFirst						= 'Tyler';
$cpLast							= 'Stokes';
$cpPhone						= '1-866-519-4782';
$cpTitle						= 'Movingiron: Marketing Site Request';
$cpSiteName						= 'Movingiron, Inc.';
$cpAddress						= 'Three Galleria Tower';
$cpCity							= 'Dallas';
$cpState						= 'TX';
$cpZip							= '75240';
$cpInvCount						= '600';
$cpBkgColor						= '#e3e2de';
$cpSaleText						= 'Private Sale';
$cpPriceDelta					= '1495';
$show_video_default				= 'true';
$video_account					= 'movingiron';
$video_site						= 'movingiron';
$video_token					= 'e8b80797cc95c702';
$cpConfig						= array(
									'postfix'	=> '.movingiron.com'
									, 'email'	=> $cpEmail
									, 'emailBase'	=> $cpEmailBase
									, 'siteName'=> $cpSiteName
									, 'cpanelUrl'=> $cpanelUrl
									, 'baseSite'=> 'http://noble.movingiron.com'
									, 'logpass'	=> 'movingir:vejocrud'
									, 'leadsFilename'	=> 'movingiron-leads'
									, 'mi_host'	=> 'movingiron.com'
									, 'mi_login'=> 'movingir'
									, 'mi_password'	=> 'vejocrud'
									, 'db_password'	=> 'vejocrud'
									, 'pathToCpFolder'=> '/cp/datafeed/'
									, 'pathToCsvFolder'	=> '/cp/datafeed/csv/'
									, 'webdirectory'	=> '/home/movingir/public_html/'
        							, 'pathToTemplates[1]' => '/home/movingir/public_html/landingtemplates/new_used'
        							, 'pathToTemplates[2]' => '/home/movingir/public_html/landingtemplates/private_sale'
        							, 'pathToTemplates[3]' => '/home/movingir/public_html/landingtemplates/fleet_liquidation'
									, 'api_db_host'			=> $db_host
									, 'api_db_username'		=> $db_username
									, 'api_db_password'		=> $db_password
									, 'api_db_name'			=> $db_name
									, 'api_email'			=> $adminEmail
									// *.movingiron.com
        							, 'license' => 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAiKhJdR/ONvHOIjDzZltSTE+gcOmaP8m2P2qGRYplh+22AFsyjiY+yR4suITuOnH2ZopgZhNKhotGjiMizlnkb'
									, 'gcaadmin' => 'miadmin'
									, 'gcaadminpw' => 'H0iB6k'
								);

// helper includes
require_once("/home/movingir/local/cb_cogs/cb_cogs.config.php");

?>