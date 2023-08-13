<?php   // home_page.php
    require_once 'database_credentials.php';
    require_once 'common_helper_methods.php';
    
    /*  
    
    
    
    */
    
    // SQL Database connection.
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die (mysql_fatal_error());

    // Log user out
    destroy_session_data();
    
    // Set up webpage.
    setup_html_home_page();
    
    // Log user in if their credentials exist in the database.
    if(isset($_POST['user']) && isset($_POST['pass']))
    {
        $username = get_sanitized_string($conn, $_POST['user']);
        $password = get_sanitized_string($conn, $_POST['pass']);
        
        if(exists_in_database($conn, $username, $password))
        {
            $email = get_user_email($conn, $username, $password);
            start_user_session($username, $email);
            switch_to_custom_home_page();
        }
        else echo "<div>Invalid username/password combination.</div>";
    }
    
    // Recieve ingredients list from the user.
    if(isset($_POST['search_button']))
    {   
        $str = get_sanitized_string($conn, $_POST['ingredients']);
        if($str != "")
        {
            // Get user inputted ingredients
            $ingredients_array = get_ingredients_in_array($conn, $str);
        
            // Generate SQL Query
            $query = generate_select_query($ingredients_array);
        
            // Display search results
            display_html_search_results($conn, $query);
        }   
    }
    
    
    /* -------------------- HELPER METHODS -------------------- */
    
    // Get ingredients in an array format.
    function get_ingredients_in_array($conn, $str)
    {
        $ingredients_str = str_replace(' ', '', $str);
        $ingredients_array = explode(',', $ingredients_str);
        return $ingredients_array;
    }

    // Generate mySQL SELECT query to search for recipes.
    function generate_select_query($ingredients_array)
    {
        $query = 'SELECT * FROM recipes WHERE';
        for($i = 0; $i < sizeof($ingredients_array); $i++)
        {
            $query = $query . " ingredients LIKE '%$ingredients_array[$i]%'";
            if($i < sizeof($ingredients_array) - 1)
                $query = $query . " AND";
        }
        $query = $query . ";";
        return $query;
    }

    // Destroy session and cookie.
    function destroy_session_data()
    {
        session_start();
        $_SESSION = array();
        setcookie(session_name(), '', time() - 2592000, '/');
        session_destroy();
    }

    // Get user email from database.
    function get_user_email($conn, $user, $pass)
    {
        $query = "SELECT * FROM user_credentials;";
        $result = $conn->query($query);
        $rows = $result->num_rows;
        
        for($i = 0; $i < $rows; $i++)
        {
            $result->data_seek($i);
            $record = $result->fetch_array(MYSQLI_ASSOC);
            if(password_verify($pass, $record['password']))
                return $record['email'];
        }
        $result->close();
    }
    
    // Go to home page.
    function switch_to_custom_home_page()
    {
        header("Location: custom_home_page.php");
    }
    
    // Setup user session.
    function start_user_session($username, $email)
    {
        session_start();
        $_SESSION['user'] = $username;
        $_SESSION['email'] = $email;
    }

    // Query the database using the username and check corresponding password.
    function exists_in_database($conn, $user, $pass)
    {
        $query = "SELECT password FROM user_credentials WHERE username='$user';";
        $result = $conn->query($query);
        $row = $result->fetch_array(MYSQLI_NUM);
        return password_verify($pass, $row[0]);
    }
    
    // Set up the html login page which the user will interact with.
    function setup_html_home_page()
    {
        echo <<<_END
        <!DOCTYPE html>
        <html>
            <head>
                <title> CS 174 - Final </title>
                <style>
                    .top
                    {
                        padding-top: 50px;   
                    }
                    .left 
                    { 
                        padding-left: 110px;
                        float: left;
                    }
                    .right
                    {
                        padding-right: 110px;
                        float: right;
                    }
                </style>
                <script src="validation.js"></script>
            </head> 
            <body>
                <form method='post' action='home_page.php' onSubmit="return validate_login_page(this)">
                <div class='right top'>
                    <h2> Please log in to your account. </h2>
                    <h3> Username: <input type='text' name='user'> </h3>
                    <h3> Password: <input type='text' name='pass'> </h3>
                    <input type='submit' value='Login' name='button'> <br><br><br>
                    If you don't have an account, please <a href='sign_up.php'> click here </a> to sign up!
                </div>
                </form>
                
                <div id='setup' class='left top'>
                    <h1> Welcome Guest!</h1>
                    <h1> You can search for recipes here!</h1><br>
                    <font size='+2'> Use the search bar to search for recipes based on the ingredients it uses.<br>
                    For example, typing "potato" will search for all recipes that use potatoes.<br>
                    Typing "potato, tomato" will search for all recipes that use potatoes and tomatoes.<br></font>
                    <form method='post'>
                    <h3> Ingredients: <input type='text' name='ingredients' size='80'> </h3>
                    <button type='submit' name='search_button'> Search </button></form><br>
                    <font size='+1'>
_END;
    }
    
    // Display recipes for ingredients that the user inputted.
    function display_html_search_results($conn, $query)
    {
        $results = $conn->query($query);
        if(!$results) echo "There are no search results, please try a different set of ingredients<br><br>";
        else if($results->num_rows != 0) echo "Search results found!<br><br>";
        else echo "There are no search results, please try a different set of ingredients<br><br>";
        
        $rows = $results->num_rows;
        for($i = 0; $i < $rows; $i++)
        {
            $results->data_seek($i);
            $record = $results->fetch_array(MYSQLI_NUM);
            
            echo <<<_END
        <b>Name: $record[1]</b><br>
        Prep Time: $record[3] | Cook Time: $record[4] | Total Time: $record[5]<br><br>
        Ingredients: $record[6]<br><br>
        Directions: $record[7]<br><br><br>
_END;
        }
    }

    $conn->close();
    echo "</body></html>";
?>