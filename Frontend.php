<?php
    require("Common.php");
    
    // Print page with all Databases and all *.xml 
    // files listet. You can dump the Databases
    // and use the xml files to create a database.
    $tpl = new HTML_Template_IT('.');
    $tpl->loadTemplateFile('Frontend.tpl.html');
    foreach($db->listDatabases() as $dbName) {
        // If a DB is specified, we can display detailed information
        if (isset($_GET['db'])) {
            // Skip non specified databases
            if ($_GET['db'] != $dbName) {
                continue;
            };
            // Switch to specified Database
            $db->setDatabase($dbName);
            
            // Display DB-Dump-Dialog
            $tpl->setCurrentBlock('DumpDB');
            $tpl->setVariable('DumpDB_DBName', $dbName);
            $tpl->parseCurrentBlock();
            
            // For all Tables do ...
            foreach($db->listTables() as $tableName) {
                // Fill Tablelist on the left side 
                $tpl->setCurrentBlock('Navi_Databases_Tables');
                $tpl->setVariable('Navi_Databases_Tables_TableName', $tableName);
                $tpl->setVariable('Navi_Databases_Tables_DBName',   $dbName);
                $tpl->parseCurrentBlock();
                
                // Check if the user specified a special table
                if (!isset($_GET['action']) && isset($_GET['table']) && $_GET['table'] == $tableName) {
                    // Print detailed information about the Table
                    // List all Fields and their properties
                    foreach($db->listTableFields($tableName) as $fieldName) {
                        $def = $db->getTableFieldDefinition($tableName, $fieldName);
                        $attribs = $def[0][0];
                        $tpl->setCurrentBlock('Table_Detail_Field');
                        $tpl->setVariable('Table_Detail_Field_FieldName', $fieldName);
                        if (isset($attribs['length'])) {
                            $attribs['type'] .= ' ('.$attribs['length'].')';
                        };
                        $tpl->setVariable('Table_Detail_Field_FieldType', $attribs['type']);
                        if (isset($attribs['default'])) {
                            $tpl->setVariable('Table_Detail_Field_FieldDefault', $attribs['default']);
                        };
                        if (isset($attribs['notnull'])) {
                            $tpl->setVariable('Table_Detail_Field_FieldNotnull', ($attribs['notnull'] ? 'yes' : 'no'));
                        };
                        $tpl->parseCurrentBlock();
                    };
                    $tpl->setCurrentBlock('Table_Detail');
                    $tpl->setVariable('Table_Detail_TableName', $tableName);
                    $tpl->setVariable('Table_Detail_DBName', $dbName);
                    $tpl->parseCurrentBlock();
                }
                // Show Table Content
                if (isset($_GET['action']) && isset($_GET['table']) && $_GET['table'] == $tableName) {
                    switch($_GET['action']) 
                    {
                    case 'show':
                        $sql = 'SELECT * FROM '.$tableName;
                        if (isset($_GET['order'])) {
                            $sorting = 'ASC';
                            if (isset($_GET['sorting']) && $_GET['sorting'] == 'DESC') {
                                $sorting = 'DESC';
                            };
                            $sql .= ' ORDER BY '.addslashes($_GET['order']).' '.$sorting;
                        };
                        if (isset($_GET['new']) || !isset($_SESSION['_MDB']['queries'][$sql])) {
                            $_SESSION['_MDB']['queries'][$sql] = 
                                $db->getAll($sql , NULL, array(), NULL, MDB_FETCHMODE_ASSOC);                        
                        };
                        $data = $_SESSION['_MDB']['queries'][$sql];
                        $pager = &new Pager(array(
                            'itemData' => $data,
                            'linkClass'=> 'detaillink',
                            'perPage'  => 20,
                            ));
                        $data  = $pager->getPageData();
                        $links = $pager->getLinks();
                        $i = 0;
                        // Insert row by row ...
                        foreach($data as $rowNr => $row) {
                            foreach($row as $fieldName => $fieldContent) {
                                if ($i == 0) {
                                    $tpl->setCurrentBlock('Table_Content_FieldHeader');
                                    if (isset($_GET['order']) && $_GET['order'] == $fieldName 
                                        && isset($_GET['sorting']) && $_GET['sorting'] == 'ASC') 
                                    {
                                        $sorting = 'DESC';
                                    } else {
                                        $sorting = 'ASC';
                                    };
                                    $fieldLink = sprintf('Frontend.php?db=%s&amp;table=%s&amp;new=1&amp;action=show&amp;order=%s&amp;sorting=%s',
                                        $dbName, $tableName, 
                                        $fieldName, $sorting);
                                    $tpl->setVariable('Table_Content_FieldHeader_Name', $fieldName);
                                    $tpl->setVariable('Table_Content_FieldHeader_Link', $fieldLink);
                                    $tpl->parseCurrentBlock();
                                };
                                $tpl->setCurrentBlock('Table_Content_Field');
                                if (strlen($fieldContent) > 240) {
                                    $fieldContent = substr($fieldContent, 0, 235).' ... (cut)';
                                };
                                $fieldContent = wordwrap($fieldContent, round(180 / count($row)), "\n", 1);
                                $fieldContent = nl2br(htmlspecialchars($fieldContent));
                                $tpl->setVariable('Table_Content_Field', $fieldContent);
                                $tpl->parseCurrentBlock();
                            };
                            $tpl->setCurrentBlock('Table_Content_Row');
                            $tpl->setVariable('Table_Content_Row_Nr', $rowNr);
                            $tpl->parseCurrentBlock();
                            $i++;
                        };
                        $tpl->setCurrentBlock('Table_Content');
                        
                        // Insert Pagingvars into Template
                        $tpl->setVariable('Table_Content_Next', str_replace('&new=1', '',$links['next']));
                        $tpl->setVariable('Table_Content_Prev', str_replace('&new=1', '',$links['back']));
                        list($from, $to) = $pager->getOffsetByPageId();
                        $tpl->setVariable('Table_Content_From', $from);
                        $tpl->setVariable('Table_Content_To', $to);
                        $tpl->setVariable('Table_Content_Entries', $pager->numItems());
                        $tpl->setVariable('Table_Content_TableName', $tableName);
                        $tpl->parseCurrentBlock();
                    };
                };
                // Display list of all Tables
                if (!isset($_GET['action']) && !isset($_GET['table'])) {
                    $tpl->setCurrentBlock('Database_Detail_Table');
                    $tpl->setVariable('Database_Detail_Table_TableName', $tableName);
                    $entries = $db->getOne('SELECT COUNT(*) FROM '.$tableName);
                    $tpl->setVariable('Database_Detail_Table_Entries', $entries);
                    $tpl->setVariable('Database_Detail_Table_DBName', $dbName);
                    $tpl->parseCurrentBlock();
                };
            };
            // Display list of all Tables - Part 2
            if (!isset($_GET['action']) && !isset($_GET['table'])) {
                $tpl->setCurrentBlock('Database_Detail');
                $tpl->setVariable('Database_Detail_DBName', $dbName);
                $tpl->parseCurrentBlock();
            };
        };
        // Display list of Databases on left side of the page
        $tpl->setCurrentBlock('Navi_Databases');
        $tpl->setVariable('Navi_Databases_DBName', $dbName);
        $tpl->parseCurrentBlock();
    };
    
    
    // XML Schemas ...
    $dir = opendir('.');
    while (($file = readdir($dir)) !== false) {
        if (is_file($file)) {
            $ext = substr($file, strlen($file)-3);
            if ($ext != 'xml') {
                continue;
            };

            $tpl->setCurrentBlock('Navi_Schemas');
            $tpl->setVariable('Navi_Schemas_Filename', $file);
            $tpl->setVariable('Navi_Schemas_Size', round(filesize($file) / 1024));
            $tpl->parseCurrentBlock();

        };
    };
    closedir($dir);
    
    if (isset($_GET['viewdump'])) {
        $tpl->setCurrentBlock('DumpFile');
        $cmd = '/usr/bin/xsltproc '.dirname(__FILE__).'/xml_schema.xsl '.dirname(__FILE__).'/'.$_GET['viewdump'];
        $tpl->setVariable('DumpFile_Data', `$cmd`);
        $tpl->setVariable('DumpFile_Filename', $_GET['viewdump']);
        $tpl->parseCurrentBlock();
    };
    $tpl->show();

?>