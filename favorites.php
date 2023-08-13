<?php   // home_page.php
    require_once 'database_credentials.php';
    require_once 'common_helper_methods.php';

    /*  
    
    
    
    */

    // SQL Database connection.
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die (mysql_fatal_error());

    // Start session.
    session_start();

    // Delete user selected record from the webpage.
    if(isset($_POST['delete']) && isset($_POST['id']))
    {
        $id = get_sanitized_string($conn, $_POST['id']);
        delete_record($conn, $id);
    }

    // Set up webpage.
    setup_html_home_page($conn);
    
    // Display the database containing user uploaded files.
    display_database($conn);
    

    /* -------------------- HELPER METHODS -------------------- */
   

    // Delete record from the database.
    function delete_record($conn, $id)
    {
        $query = "DELETE FROM user_favorites WHERE id=$id;";
        $result = $conn->query($query);
        if(!$result) echo "DELETE failed<br><br>";
    }
    
    // Display the database contents on the webpage.
    function display_database($conn)
    {
        $email = get_sanitized_string($conn, $_SESSION['email']);
        $query = "SELECT * FROM user_favorites WHERE email='$email';";
        $result = $conn->query($query);
        if(!$result) die (mysql_fatal_error());
        
        $rows = $result->num_rows;
        for($i = 0; $i < $rows; $i++)
        {
            $result->data_seek($i);
            $record = $result->fetch_array(MYSQLI_NUM);
            
            echo <<<_END
        <b>Name: $record[2]</b><br>
        Prep Time: $record[3] | Cook Time: $record[4] | Total Time: $record[5]<br><br>
        Ingredients: $record[6]<br><br>
        Directions: $record[7]
        
        <form action="favorites.php" method="post">
        <input type="hidden" name="delete" value="yes">
        <input type="hidden" name="id" value="$record[0]">
        <input type="submit" value="DELETE">
        </form><br><br><br>
_END;
        }
        $result->close();
    }

    // Set up the html home page which the user will interact with.
    function setup_html_home_page($conn)
    {
        $username = get_sanitized_string($conn, $_SESSION['user']);  
        
        echo <<<_END
        <!DOCTYPE html>
        <html>
            <head>
                <title> CS 174 - Final </title>
                <style>
                    div 
                    { 
                        padding-left: 110px;
                        padding-top: 50px;    
                    }
                </style>
            </head> 
            <body><div>
                <h1> Welcome $username! Here are your favorite recipes!</h1>
                <font size='+2'>Click here to return to home page: <a href='custom_home_page.php'><input type="button" value='Home Page'></a></font><br><br><br><br>
_END;
    }

    $conn->close();
    echo "</body></html>";
?>