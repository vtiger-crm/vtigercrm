<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header$
 * Description:  Defines the English language pack
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

$mod_strings = Array(
// Added in release 4.0
'LBL_MODULE_NAME'=>'Trouble Tickets',
'LBL_GROUP'=>'Group',
'LBL_ACCOUNT_NAME'=>'Account Name',
'LBL_CONTACT_NAME'=>'Contact Name',
'LBL_SUBJECT'=>'Subject',
'LBL_NEW_FORM_TITLE' => 'New Ticket',
'LBL_DESCRIPTION'=>'Description',
'NTC_DELETE_CONFIRMATION'=>'Are you sure you want to delete this record?',
'LBL_CUSTOM_FIELD_SETTINGS'=>'Custom Field Settings:',
'LBL_PICKLIST_FIELD_SETTINGS'=>'Picklist Field Settings:',
'Leads'=>'Lead',
'Accounts'=>'Account',
'Contacts'=>'Contact',
'Opportunities'=>'Opportunity',
'LBL_CUSTOM_INFORMATION'=>'Custom Information',
'LBL_DESCRIPTION_INFORMATION'=>'Description Information',

'LBL_ACCOUNT'=>'Account',
'LBL_OPPURTUNITY'=>'Oppurtunity',
'LBL_PRODUCT'=>'Product',

'LBL_COLON'=>':',
'LBL_TICKET'=>'Ticket',
'LBL_CONTACT'=>'Contact',
'LBL_STATUS'=>'Status',
'LBL_ASSIGNED_TO'=>'Assigned To',
'LBL_FAQ'=>'FAQ',
'LBL_VIEW_FAQS'=>'View FAQs',
'LBL_ADD_FAQS'=>'Add FAQs',
'LBL_FAQ_CATEGORIES'=>'FAQ Categories',

'LBL_PRIORITY'=>'Priority',
'LBL_CATEGORY'=>'Category',

'LBL_ANSWER'=>'Answer',
'LBL_COMMENTS'=>'COMMENTS',

'LBL_AUTHOR'=>'Author',
'LBL_QUESTION'=>'Question',

//Added vtiger_fields for File Attachment and Mail send in Tickets
'LBL_ATTACHMENTS'=>'Attachments',
'LBL_NEW_ATTACHMENT'=>'New Attachment',
'LBL_SEND_MAIL'=>'Send Mail',

//Added vtiger_fields for search option  in TicketsList -- 4Beta
'LBL_CREATED_DATE'=>'Created Date',
'LBL_IS'=>'is',
'LBL_IS_NOT'=>'is not',
'LBL_IS_BEFORE'=>'is before',
'LBL_IS_AFTER'=>'is after',
'LBL_STATISTICS'=>'Statistics',
'LBL_TICKET_ID'=>'Ticket Id',
'LBL_MY_TICKETS'=>'My Tickets',
"LBL_MY_FAQ"=>"My Faq's",
'LBL_ESTIMATED_FINISHING_TIME'=>'Estimated Finishing Time',
'LBL_SELECT_TICKET'=>'Select Ticket',
'LBL_CHANGE_OWNER'=>'Change Owner',
'LBL_CHANGE_STATUS'=>'Change Status',
'LBL_TICKET_TITLE'=>'Title',
'LBL_TICKET_DESCRIPTION'=>'Description',
'LBL_TICKET_CATEGORY'=>'Category',
'LBL_TICKET_PRIORITY'=>'Priority',

//Added vtiger_fields after 4 -- Beta
'LBL_NEW_TICKET'=>'New Ticket',
'LBL_TICKET_INFORMATION'=>'Ticket Information',

'LBL_LIST_FORM_TITLE'=>'Tickets List',
'LBL_SEARCH_FORM_TITLE'=>'Ticket Search',

//Added vtiger_fields after RC1 - Release
'LBL_CHOOSE_A_VIEW'=>'Choose a View...',
'LBL_ALL'=>'All',
'LBL_LOW'=>'Low',
'LBL_MEDIUM'=>'Medium',
'LBL_HIGH'=>'High',
'LBL_CRITICAL'=>'Critical',
//Added vtiger_fields for 4GA
'Assigned To'=>'Assigned To',
'Contact Name'=>'Contact Name',
'Priority'=>'Priority',
'Status'=>'Status',
'Category'=>'Category',
'Update History'=>'Update History',
'Created Time'=>'Created Time',
'Modified Time'=>'Modified Time',
'Title'=>'Title',
'Description'=>'Description',

'LBL_TICKET_CUMULATIVE_STATISTICS'=>'Ticket Cumulative Statistics:',
'LBL_CASE_TOPIC'=>'Case Topic',
'LBL_OPEN'=>'Open',
'LBL_CLOSED'=>'Closed',
'LBL_TOTAL'=>'Total',
'LBL_TICKET_HISTORY'=>'Ticket History',
'LBL_CATEGORIES'=>'Categories',
'LBL_PRIORITIES'=>'Priorities',
'LBL_SUPPORTERS'=>'Supporters',

//Added vtiger_fields after 4_0_1
'LBL_TICKET_RESOLUTION'=>'Solution Information',
'Solution'=>'Solution',
'Add Comment'=>'Add Comment',
'LBL_ADD_COMMENT'=>'Add Comment',//give the same value given to the above string 'Add Comment'

//Added for 4.2 Release -- CustomView
'Ticket ID'=>'Ticket ID',
'Subject'=>'Subject',

//Added after 4.2 alpha
'Severity'=>'Severity',
'Product Name'=>'Product Name',
'Related To'=>'Related To',
'LBL_MORE'=>'More',

'LBL_TICKETS'=>'Tickets',

//Added on 09-12-2005
'LBL_CUMULATIVE_STATISTICS'=>'Cumulative Statistics',

//Added on 12-12-2005
'LBL_CONVERT_AS_FAQ_BUTTON_TITLE'=>'Convert As FAQ',
'LBL_CONVERT_AS_FAQ_BUTTON_KEY'=>'C',
'LBL_CONVERT_AS_FAQ_BUTTON_LABEL'=>'Convert As FAQ',
'Attachment'=>'Attachment',
'LBL_COMMENT_INFORMATION'=>'Comment Information',

//Added for existing picklist entries

'Big Problem'=>'Big Problem',
'Small Problem'=>'Small Problem',
'Other Problem'=>'Other Problem',

'Low'=>'Low',
'Normal'=>'Normal',
'High'=>'High',
'Urgent'=>'Urgent',

'Minor'=>'Minor',
'Major'=>'Major',
'Feature'=>'Feature',
'Critical'=>'Critical',

'Open'=>'Open',
'In Progress'=>'In Progress',
'Wait For Response'=>'Wait For Response',
'Closed'=>'Closed',

//added to support i18n in ticket mails
'Hi' => 'Hi',
'Dear'=> 'Dear',
'LBL_PORTAL_BODY_MAILINFO'=> 'The Ticket is',
'LBL_DETAIL' => 'the details are :',
'LBL_REGARDS'=> 'Regards',
'LBL_TEAM'=> 'HelpDesk Team',
'LBL_TICKET_DETAILS' => 'Ticket Details',
'LBL_SUBJECT' => 'Subject : ',
'created' => 'created',
'replied' => 'replied',
'reply'=>'There is a reply to',
'customer_portal' => 'in the "Customer Portal" at VTiger.',
'link' => 'You can use the following link to view the replies made:',
'Thanks' => 'Thanks',
'Support_team' => 'Vtiger Support Team',

// Added/Updated for vtiger CRM 5.0.4

//this label for customerportal.
'LBL_STATUS_CLOSED' =>'Closed',//Do not convert this label. This is used to check the status. If the status 'Closed' is changed in vtigerCRM server side then you have to change in customerportal language file also.
'LBL_STATUS_UPDATE' => 'Ticket status is updated as',
'LBL_COULDNOT_CLOSED' => 'Ticket could not be',
'LBL_CUSTOMER_COMMENTS' => 'Customer has provided the following additional information to your reply:',
'LBL_RESPOND'=> 'Kindly respond to above ticket at the earliest.',
'LBL_REGARDS' =>'Regards',
'LBL_SUPPORT_ADMIN' => 'Support Administrator',
'LBL_RESPONDTO_TICKETID' =>'Respond to Ticket ID',
'LBL_CUSTOMER_PORTAL' => 'in Customer Portal - URGENT',
'LBL_LOGIN_DETAILS' => 'Following are your Customer Portal login details :',
'LBL_MAIL_COULDNOT_SENT' =>'Mail could not be sent',
'LBL_USERNAME' => 'User Name :',
'LBL_PASSWORD' => 'Password :',
'LBL_SUBJECT_PORTAL_LOGIN_DETAILS' => 'Regarding your Customer Portal login details',
'LBL_GIVE_MAILID' => 'Please give your email id',
'LBL_CHECK_MAILID' => 'Please check your email id for Customer Portal',
'LBL_LOGIN_REVOKED' => 'Your login is revoked. Please contact your admin.',
'LBL_MAIL_SENT' => 'Mail has been sent to your mail id with the customer portal login details',
'LBL_ALTBODY' => 'This is the body in plain text for non-HTML mail clients',

// Added after 5.0.4 GA

// Module Sequence Numbering
'Ticket No' => 'Ticket No',
// END

'Hours' => 'Hours',
'Days' => 'Days',
);

?>
