Create directory with <MODULENAME> and save your module specific smarty template files.

In your Module php files you can refer to these templates files as

$smarty->display(vtlib_getModuleTemplate('<MODULENAME>', '<TEMPLATEFILENAME>.tpl'));

