To setup a new cron service
===========================

1. Create <ServiceName>.service file, which has the following content at the beginning

<?php

require_once('config.inc.php');

/** Verify the script call is from trusted place. */
global $application_unique_key;
if($_REQUEST['app_key'] != $application_unique_key) {
	echo "Access denied!";
	exit;
}

/**
 * Check if instance of this service is already running?
 */
$svcname = $_REQUEST['service'];
// We need to make sure the PIDfile name is unqique
$servicePIDFile = "logs/$svcname-service.pid";

if(file_exists($servicePIDFile)) {
	echo "Service $svcname already running! Check $servicePIDFile";
	exit;
} else {
	$servicePIDFp = fopen($servicePIDFile, 'a');
}

/**
 * Turn-off PHP error reporting.
 */
try { error_reporting(0); } catch(Exception $e) { }

// ... REST OF YOUR CODE ...

// AT END
/** Close and remove the PID file. */
if($servicePIDFp) {
	fclose($servicePIDFp);
	unlink($servicePIDFile);
}

?>

=====================================================================================================================================

2. Create <ServiceName>Cron.sh file which should have the following:

export VTIGERCRM_ROOTDIR=`dirname "$0"`/..
export USE_PHP=php

cd $VTIGERCRM_ROOTDIR

$USE_PHP -f vtigercron.php service="<ServiceName>" <param>="<value>"

=====================================================================================================================================

3. Create <ServiceName>Cron.bat file which should have the following:

@echo off

set VTIGERCRM_ROOTDIR="C:\Program Files\vtigercrm5\apache\htdocs\vtigerCRM"
set PHP_EXE="C:\Program Files\vtigercrm5\php\php.exe"

cd /D %VTIGERCRM_ROOTDIR%

%PHP_EXE% -f vtigercron.php service="<ServiceName>" <param>="<value>"
=====================================================================================================================================
