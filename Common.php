<?php
    set_time_limit(0);

    require_once('MDB/manager.php');
    require_once('HTML/Template/IT.php');
    require_once('Pager/Pager.php');
    
    @session_start();
    
    $formData = array(
        'hostname' => 'localhost',
        'dbtype'   => 'mysql',
        'username' => 'root',
        'password' => '',
        );
    
    // Did the User try to Login?
    if (isset($_POST['formData'])) {
        $formData = $_SESSION['_MDB']['formData'] = $_POST['formData'];
    };
    
    // Formdata already in Session?
    if (!isset($_SESSION['_MDB']['formData'])) {
        // No ... display Loginscreen
        require(dirname(__FILE__).'/Login.tpl.php');
        exit;
    } else {
        // Yes ... so get this data
        $formData = $_SESSION['_MDB']['formData'];
    };
    
    // Build DSN
    $dsn = sprintf('%s://%s:%s@%s', $formData['dbtype'], $formData['username'],
                $formData['password'], $formData['hostname']);
    
    // Connect to DB
    $db = MDB::Connect($dsn);
    // If Login Failed, display Loginscreen with Errormessage
    if (MDB::isError($db)) {
        $error = $db->toString();
        require(dirname(__FILE__).'/Login.tpl.php');
        exit;
    };


?>