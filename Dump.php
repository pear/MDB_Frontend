<?php
    require("Common.php");

    // Dump existing Database
    if (isset($_GET['db']) && $_GET['db']
        && isset($_GET['filename']) && $_GET['filename']) 
    {
        // Init Manager
        $manager = new MDB_manager();
        $manager->connect($db);
        $res = $manager->setDatabase($_GET['db']);
        // Dump Database
        if (isset($_GET['what'])) {
            switch($_GET['what']) 
            {
            case 'structure':
                $what = MDB_MANAGER_DUMP_STRUCTURE;
                break;
            case 'data':
                $what = MDB_MANAGER_DUMP_CONTENT;
                break;
            case 'both':
                $what = MDB_MANAGER_DUMP_ALL;
                break;
            default:
                $what = MDB_MANAGER_DUMP_ALL;
            };
        } else {
            $what = MDB_MANAGER_DUMP_ALL;
        };
        
        $manager->dumpDatabase(
            array(
                'Output_Mode' => 'file',
                'Output' => $_GET['filename'].'tmp',
                ),
            $what
            );
        $warnings = $manager->getWarnings();
        if (count($warnings)) {
            echo "<pre>";
            print_r($warnings);
            echo "</pre>";
        };
        // Change DBName in XML File
        $fp1 = fopen($_GET['filename'].'tmp', 'r');
        $fp2 = fopen($_GET['filename'], 'w');
        $i = 0;
        while ($str = fgets($fp1, 4096)) {
            $i++;
            if ($i == 2) {
                fwrite($fp2, '<?xml-stylesheet type="text/xsl" href="xml_schema.xsl"?>'."\n");
            };
            if ($i == 4) {
                fwrite($fp2, "  <name><variable>DBName</variable></name>\n");
                continue;
            };
            fwrite($fp2, $str);
        };
        fclose($fp1);
        fclose($fp2);
        unlink($_GET['filename'].'tmp');
    };
    // Redirect
    Header('Location: Frontend.php');
    exit;
?>
