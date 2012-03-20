#*********************************************************************************
# The contents of this file are subject to the vtiger CRM Public License Version 1.0
# ("License"); You may not use this file except in compliance with the License
# The Original Code is:  vtiger CRM Open Source
# The Initial Developer of the Original Code is vtiger.
# Portions created by vtiger are Copyright (C) vtiger.
# All Rights Reserved.
#
# ********************************************************************************
# wget "http://localhost:APACHEPORT/vtigercron.php?service=com_vtiger_workflow&app_key=YOUR_APP_KEY_HERE" -O /dev/null

export VTIGERCRM_ROOTDIR=`dirname "$0"`/../../..
export USE_PHP=php

cd $VTIGERCRM_ROOTDIR

$USE_PHP -f vtigercron.php service="com_vtiger_workflow"