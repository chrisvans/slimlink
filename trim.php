<?php 
  // ini_set('default_mimetype', 'text/css');
  header("Content-Type: text/css");
  header("Content-Type: text/html");
  // Bring in Helper Functions
  require("includes.php");

  // Initialize messages
  $error_message = '';
  $success_message = '';
  $information_message = '';

  // Connect to the MySQL DB
  $dbhost = "localhost";
  $dbuser = "chris_php";
  $dbpass = "bagel";
  $dbname = "php_chris";
  $connection = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

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
    $url = add_http_url($url);

    // Act upon a valid URL entry
    if (is_valid_url($url) === True) {
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
      $success_message = "{$url} successfully slimmed!";
      $information_message = "Access your URL at <a href='http://www.slimlink.us/{$trimmed_url}'>www.slimlink.us/{$trimmed_url}</a>";

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
        $error_message = "Invalid Slimlink Code Provided.";
    // If the code was in the DB, it returned the proper URL
    } else {
        redirect($redirect_url);
    }
  }

?>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <title>Slimlink</title>

  <link href='http://fonts.googleapis.com/css?family=Dosis:300,500,700' rel='stylesheet' type='font/woff'>
  <link href="bootstrap/css/bootstrap.css" media="all" rel="stylesheet" type="text/css">
  <link href="bootstrap/css/bootstrap-responsive.css" media="all" rel="stylesheet" type="text/css">
  <link href="stylesheets/slimlink.css" media="all" rel="stylesheet" type="text/css">

  </head>

  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inverse">
        <div class="navbar-inner">
          <div id="topnav">
            <a class="brand" href="http://www.slimlink.us/index.php">Slimlink</a>
          </div>
        </div>
      </div>
    </div>

    <div class="container" id="contentdiv">
      <div id="left">
      <form class="form-signin" action="index.php" method="post">
        Link: <input class="input-block-level" type="text" name="url" placeholder="Enter your URL here to have it slimmed" />
        <button class="btn btn-small btn-primary" type="submit" name="submit" value="Submit">Slim</button>
        <?php
        diag_echo($error_message);
        diag_echo($success_message);
        diag_echo($information_message);
        ?>
      <form>
      </div>
      <div id="right">
      </div>
    </div>
    
  </body>

</html>