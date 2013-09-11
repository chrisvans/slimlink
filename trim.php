<?php 
  
  // Bring in Helper Functions
  require("includes.php");

  // Initialize messages
  $error_message = '';
  $success_message = '';
  $information_message = '';

  // Connect to the MySQL DB
  // $dbhost = "localhost";
  // $dbuser = "chris_php";
  // $dbpass = "bagel";
  // $dbname = "php_chris";
  // $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

  // Connect to Heroku DB

  $url=parse_url(getenv("CLEARDB_DATABASE_URL"));

  $server = $url["host"];
  $username = $url["user"];
  $password = $url["pass"];
  $db = substr($url["path"],1);

  $connection = mysql_connect($server, $username, $password);
            
  mysql_select_db($db);

  if(mysqli_connect_error()) {
    die(
        mysqli_connect_error() . " (" .
        mysqli_connect_errno() . ")"
       );
  }

  // Act upon a valid POST request
  if (isset($_POST["url"])) {
    $url = $_POST["url"];
    // Helper function to give Http:// to a URL for Validation, but only if it needs it
    // $url = add_http_url($url);

    // Act upon a valid URL entry
    if (is_valid_url($url) === True) {
      // Remove Http/s from URL for generic storage in the DB
      // $url = remove_http_url($url);
      // Escape all characters in the URL for proper DB storage
      $url = mysqli_real_escape_string($connection, $url);
      $query = "SELECT * FROM slimlink ";
      $result = mysqli_query($connection, $query);
      
      if (!$result) {
        die("Database query failed SELECT: " . mysqli_error($connection));
      }
        
      // Check against the DB to see if the URL already exists
      $db_data = get_array_from_result($result);
      $url_exists = url_exists_in_db($db_data, $url);

      if ($url_exists === False) {
        // Generate a 6 character unique code that refers to a URL
        $trimmed_url = generate_unique_trimmed_url();
        $url = $url;
        // Save the URL and 'code'
        $query = "INSERT INTO slimlink (trimmed_url, url) ";
        $query .= "VALUES ('{$trimmed_url}', '{$url}')";
        $insert_result = mysqli_query($connection, $query);

        // Test if there was a query error.
        if (!$insert_result) {
          die("Database query failed INSERT: " . mysqli_error($connection));
        }

      }
      // Set messages
      $success_message = "{$url} successfully trimmed!";
      $information_message = "Access your URL at www.slimlink.com/{$trimmed_url}";

    } else {
        $error_message = "Invalid URL.";
    }

  // If the user has entered in a code via the URI, instead of landing on the main page
  } elseif ("trim.php" !== strstr($_SERVER['REQUEST_URI'], "trim.php")) {
    // Get only the code from the request_uri
    $trim_code = str_replace("/slimlink/", "", $_SERVER['REQUEST_URI']);
    // Check and see if the code is valid, and if so, return the corresponding URL from the DB
    $redirect_url = get_link_url($trim_code);

    // If the code was invalid, it returned False.
    if ($redirect_url === False) {
        $error_message = "Invalid Slimlink Provided.";
    // If the code was in the DB, it returned the proper URL
    } else {
        redirect($redirect_url);
    }
  }

?>
<html>

  <title>
    Slimlink
  </title>

  <body>
    
    <form action="index.php" method="post">
        Link: <input type="text" name="url" value="" /><br />
        <input type="submit" name="submit" value="Submit" />
        <?php
        diag_echo($error_message);
        diag_echo($success_message);
        diag_echo($information_message);
        ?>
    </form>
    
  </body>

</html>