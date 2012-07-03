<?php
	chdir(dirname(__FILE__).'/../../');
	require_once 'include/utils/utils.php';
	$language = $_REQUEST['login_language'];
	if(empty($language)) {
		$language = $default_language;
	}
	global $language;
	$applicationStrings = return_application_language($language);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title><?php echo $applicationStrings['LBL_RESET_PASSWORD'];?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  </head>
  <body>
	  <p>
		  <?php echo $applicationStrings['LBL_PHP_UPGRADE'];?>
	  </p>
	  <ul>
		  <li><?php echo $applicationStrings['LBL_RESET_PASSWORD_DESCRIPTION']?>
			  <a href="https://wiki.vtiger.com/index.php/520:Upgrading_to_PHP_5.3"><?php echo $applicationStrings['LBL_PLEASE_CLICK'].' '.
				$applicationStrings['LBL_HERE']; ?></a>
		  </li>
	  </ul>
  </body>
</html>
