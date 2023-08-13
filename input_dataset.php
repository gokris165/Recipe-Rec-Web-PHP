<?php   // input_dataset.php
    require_once 'database_credentials.php';
    require_once 'common_helper_methods.php';

    // SQL Database connection.
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die (mysql_fatal_error());


    if(isset($_POST['import']) && isset($_FILES['file']))
    {
        $filename = get_sanitized_string($conn, $_FILES['file']['tmp_name']);
        
        $file = fopen($filename, 'r');
        $column = fgetcsv($file, 10000, ';');
        $success = 0;
        $fail = 0;
        while(($column = fgetcsv($file, 10000, ';')) !== FALSE)
        {
            $insert_str = "INSERT INTO recipes VALUES (NULL, '$column[0]','$column[2]','$column[4]','$column[5]','$column[6]','$column[7]','$column[8]');";
            
            $result = $conn->query($insert_str);
            if(!$result) $fail++;
            else $success++;
        }
        echo "Success: $success | Fail: $fail";
    }


    print_page();
    function print_page()
    {
        echo <<<_END
        <!DOCTYPE html>
        <html>
            <head>
                <title> SQL Dataset Import </title>
                <style>
                    div 
                    { 
                        padding-left: 110px;
                        padding-top: 50px;    
                    }
                </style>
            </head> 
            <body><div>
            <form method='post' action='input_dataset.php' enctype='multipart/form-data'><pre>
            <label> Choose CSV File </label>
            File: <input type='file' name='file' accept='.csv'>
            <button type='submit' name='import'>Import</button></pre></form>
            
_END;
        
    }


?>