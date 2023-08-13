<?php   // sign_up.php
    require_once 'database_credentials.php';
    require_once 'common_helper_methods.php';

    /*  This page will allow the user to sign up for an account and store
        their credentials in a database. If the user inputs an email that 
        is already stored in the database, then there will be a warning
        thrown letting the user know that their email is already registered
        with an account. 
    */

    // SQL Database connection.
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die (mysql_fatal_error());
    
    // Set up webpage.
    setup_html_signup_page();
    
    // Enter user credentials in database.
    if(isset($_POST['user']) && isset($_POST['pass']) && isset($_POST['email']))
    {
        $username = get_sanitized_string($conn, $_POST['user']);
        $email = get_sanitized_string($conn, $_POST['email']);

        $password = get_sanitized_string($conn, $_POST['pass']);
        $pw_hashed = password_hash($password, PASSWORD_DEFAULT);
        
        // Create new account if email is unique.
        if(email_is_unique($conn, $email))
            create_new_user_account($conn, $username, $pw_hashed, $email);
        else echo "<div>Sign up failed.</div>";
    }


    /* -------------------- HELPER METHODS -------------------- */
    
    
    // Print error message for user to see if their signup fails.
    function mysql_signup_error()
    {
        echo "<div>SIGNUP FAILED<br> 
            Make sure that:<br>
            1. \"Username\" field is not empty<br>
            2. \"Password\" field is not empty<br>
            3. \"Email\" field is not empty.<br></div>";
    }
    
    // Create a new account for the user.
    function create_new_user_account($conn, $user, $pass, $email)
    {
        if ((strlen($user) > 0) && (strlen($pass) > 0) && (strlen($email) > 0))
        {
            $query = "INSERT INTO user_credentials VALUES ('NULL', '$email', '$user', '$pass');";
            $result = $conn->query($query);
        
            if(!$result) 
            {
                echo "<div>This email already has an account registered to it.<br><br></div>";
            }
            else 
            {
                echo "<div>Sign up successfull! <br>";
                echo "<br>Please <a href='home_page.php'> click here </a> to return.</div>";
            }    
        }
        else mysql_signup_error();
    }

    // Set up the html sign up page which the user will interact with.
    function setup_html_signup_page()
    {
        echo    <<<_END
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
                <script src='validation.js'></script>
            </head>
            <body>
                <div>
                <h1> Sign up page </h1><br>
                <form method='post' action='sign_up.php' onSubmit="return validate_signup_page(this)">
                <h2> Please sign up for an account! </h2>
                <h3> Username: <input type='text' name='user'> </h3>
                <h3> Password: <input type='text' name='pass'> </h3>
                <h3> Email: <input type='text' name='email'> </h3>
                <input type='submit' value='Sign up' name='button'> <br><br><br>
                Go back to <a href='home_page.php'> home page </a>
                </div>
_END;          
    }

    // Check if the email already exists in the database.
    function email_is_unique($conn, $email)
    {
        $query = "SELECT * FROM user_credentials WHERE email='$email';";
        $result = $conn->query($query);
        return $result;
    }

    
    $conn->close();
    echo "</body></html>";
?>