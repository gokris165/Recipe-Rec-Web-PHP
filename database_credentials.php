<?php   // database_credentials.php
    
    $hn = 'localhost';              // hostname: 'localhost'
    $un = 'phpserver';              // username: 'phpserver'
    $pw = 'database_connection';    // password: 'database_connection'
    $db = 'cs174_final';            // database: 'cs174_final'

    
    /* -------------------- SQL TABLE CODE -------------------- */
    
    /*
    
    # Creating a table to store user credentials
    
    CREATE TABLE user_credentials (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        email VARCHAR(30) UNIQUE NOT NULL,
        username VARCHAR(30) NOT NULL,
        password VARCHAR(100) NOT NULL
    );
    
    
    # Creating a table to store user favorites
    
    CREATE TABLE user_favorites (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        email VARCHAR(30) NOT NULL,
        Recipe_Name VARCHAR(100) NOT NULL,
        Prepare_Time VARCHAR(15) NOT NULL,
        Cook_Time VARCHAR(15) NOT NULL,
        Total_Time VARCHAR(15) NOT NULL,
        Ingredients VARCHAR(300) NOT NULL,
        Directions VARCHAR(1000) NOT NULL
    );
    
    
    # Creating a table to store all recipes
    # Out of around 12,000 recipes, only 2737 were able to be stored.
    # Encountered the following error:
    # Fatal error: Maximum execution time of 120 seconds exceeded in D:\dev\servers\XAMPP\htdocs\CS174\Final\input_dataset.php on line 22
    # Basically the dataset was too big to be entered into sql database in one attempt I think.
    
    CREATE TABLE recipes (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
        Recipe_Name VARCHAR(100) NOT NULL,
        Recipe_Photo VARCHAR(100) NOT NULL,
        Prepare_Time VARCHAR(15) NOT NULL,
        Cook_Time VARCHAR(15) NOT NULL,
        Total_Time VARCHAR(15) NOT NULL,
        Ingredients VARCHAR(300) NOT NULL,
        Directions VARCHAR(1000) NOT NULL
    );
    
    */
    
?>