<?php
    require("Common.php");

    // Create Database from XML File
    if (isset($_GET['dbname']) && $_GET['dbname']
        && isset($_GET['filename']) && $_GET['filename']) 
    {
        // Init Manager
        $manager = new MDB_manager();
        $manager->connect($db);
        // Update Database
        $res = $manager->updateDatabase($_GET['filename'], FALSE,
            array('DBName' => $_GET['dbname']));
        if (MDB::isError($res)) {
            print_r($res);
            exit;
        };
            print_r($res);
            exit;
    };
    // Redirect
    Header('Location: Frontend.php');
    exit;
?>
