Using skeleton module
=====================

1. Copy ModuleDir/<target_vtiger_version> to modules/<NewModuleName>
2. Rename modules/<NewModuleName>/ModuleFile.php     to <NewModuleName>.php
3. Rename modules/<NewModuleName>/ModuleFileAjax.php to <NewModuleName>Ajax.php
4. Rename modules/<NewModuleName>/ModuleFile.js      to <NewModuleName>.js

5. Edit <NewModuleName>.php

   a. Update $table_name and $table_index (Module table name and table index column)

   b. Update $groupTable

   c. Update $tab_name, $tab_name_index

   d. Update $list_fields, $list_fields_name, $sortby_fields

   e. Update $detailview_links

   f. Update $default_order_by, $default_sort_order

   g. Update $customFieldTable

   h. Rename class ModuleClass to class <NewModuleName>

Refer documentation for more details.