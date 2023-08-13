<?php   // common_helper_methods.php
  
    /* -------------------- HELPER METHODS -------------------- */
    // Common functions used across different webpages are put here.


    // Display an error when the sql connection fails.
    function mysql_fatal_error()
    {
        echo 'OOPS, there has been an error.<br>Please imagine a puppy to comfort yourself during this time of great sadness, thanks!';
    }

    // Sanitize user and server inputs
    function get_sanitized_string($conn, $string)
    {
        return htmlentities($conn->real_escape_string($string));
    }

?>