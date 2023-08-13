<?php   // home_page.php
    require_once 'database_credentials.php';
    require_once 'common_helper_methods.php';
    
    /*  This page will ask the user for their username and password.
        If the username and password match with what is in the mySQL database,
        let the user login to the homepage. Otherwise prompt the user for login information again. 
        
        If the user doesn't have an account, let them sign up for an account in a different sign up page.
    */
    
    // SQL Database connection.
    $conn = new mysqli($hn, $un, $pw, $db);
    if($conn->connect_error) die (mysql_fatal_error());

    // Start session.
    session_start();
    
    // Set up webpage.
    setup_html_custom_home_page($conn);

    if(isset($_POST['add_favorite']) &&
       isset($_POST['recipe_name']) &&
       isset($_POST['prep_time']) &&
       isset($_POST['cook_time']) &&
       isset($_POST['total_time']) &&
       isset($_POST['ingredients']) &&
       isset($_POST['directions']))
    {
        $email = get_sanitized_string($conn, $_SESSION['email']);
        $recipe = get_sanitized_string($conn, $_POST['recipe_name']);
        $prep = get_sanitized_string($conn, $_POST['prep_time']);
        $cook = get_sanitized_string($conn, $_POST['cook_time']);
        $total = get_sanitized_string($conn, $_POST['total_time']);
        $ingredients = get_sanitized_string($conn, $_POST['ingredients']);
        $directions = get_sanitized_string($conn, $_POST['directions']);
        
        insert_into_favorites($conn, $email, $recipe, $prep, $cook, $total, $ingredients, $directions);
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

    // Insert recipe into user favorites.
    function insert_into_favorites($conn, $email, $recipe, $prep, $cook, $total, $ingredients, $directions)
    {   
        if(already_in_favorites($conn, $email, $recipe, $prep, $cook, $total, $ingredients, $directions)) echo "Recipe Already in Favorites!";
        else
        {
            $query = "INSERT INTO user_favorites VALUES (NULL, '$email', '$recipe', '$prep', '$cook', '$total', '$ingredients', '$directions');";
        
            $results = $conn->query($query);
        
            if(!$results) echo "<font size='+1'> There was an error while adding the recipe to favorites</font><br>";
            else echo "Recipe Succesfully Added to Favorites!";
        }
    }
    
    // Check if the recipe is already in favorites.
    function already_in_favorites($conn, $email, $recipe, $prep, $cook, $total, $ingredients, $directions)
    {
        $query = "SELECT * FROM user_favorites WHERE email='$email' AND Recipe_Name='$recipe' AND Prepare_Time='$prep' AND Cook_Time='$cook' AND Total_Time='$total' AND Ingredients='$ingredients' AND Directions='$directions';";
        $result = $conn->query($query);
        $rows = $result->num_rows;
        return $rows == 1;
    }

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
    function switch_to_home_page()
    {
        header("Location: home_page.php");
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
    function setup_html_custom_home_page($conn)
    {
        $username = get_sanitized_string($conn, $_SESSION['user']);
        
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
                <div id='setup' class='left top'>
                    <h1> Welcome $username!</h1>
                    <h1> You can search for recipes, and also add them to your favorites!</h1>
                    <font size='+2'>View Favorite Recipes <a href='favorites.php'> <input type="button" value='Favorites'></a> </font><br>
                    <font size='+2'>Click here to log out: <a href='home_page.php'><input type="button" value='logout'></a></font><br><br>
                    <font size='+2'> Use the search bar to search for recipes based on the ingredients it uses.<br>
                    For example, typing "potato" will search for all recipes that use potatoes.<br>
                    Typing "potato, tomato" will search for all recipes that use potatoes and tomatoes.<br></font>
                    <form method='post'>
                    <h3> Ingredients: <input type='text' id='one' name='ingredients' size='80'> </h3>
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
        Directions: $record[7]
        
        <form method="post">
        <input type="hidden" name="recipe_name" value="$record[1]">
        <input type="hidden" name="prep_time" value="$record[3]">
        <input type="hidden" name="cook_time" value="$record[4]">
        <input type="hidden" name="total_time" value="$record[5]">
        <input type="hidden" name="ingredients" value="$record[6]">
        <input type="hidden" name="directions" value="$record[7]">
        <button type="submit" name="add_favorite"> FAVORITE </button>
        </form><br><br><br>
_END;
        }
    }

    $conn->close();
    echo "</body></html>";
?>