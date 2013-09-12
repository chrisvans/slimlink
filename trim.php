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
  // $connection = mysql_connect($dbhost, $dbuser, $dbpass, $dbname);

  // Connect to Heroku DB

  $url=parse_url(getenv("CLEARDB_DATABASE_URL"));

  $server = $url["host"];
  $username = $url["user"];
  $password = $url["pass"];
  $db = substr($url["path"],1);

  $connection = mysql_connect($server, $username, $password);
            
  mysql_select_db($db);

  // if(mysql_connect_error()) {
  //   die(
  //       mysql_connect_error() . " (" .
  //       mysql_connect_errno() . ")"
  //      );
  // }

  // Act upon a valid POST request
  if (isset($_POST["url"])) {
    $url = $_POST["url"];
    // Helper function to give Http:// to a URL for Validation, but only if it needs it
    $url = add_http_url($url);

    // Act upon a valid URL entry
    if (is_valid_url($url) === True) {
      // Remove Http/s from URL for generic storage in the DB
      // $url = remove_http_url($url);
      // Escape all characters in the URL for proper DB storage
      $url = mysql_real_escape_string($url, $connection);
      $query = "SELECT * FROM slimlink ";
      $result = mysql_query($query, $connection);
      
      if (!$result) {
        die("Database query failed SELECT: " . mysql_error($connection));
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
        $insert_result = mysql_query($query, $connection);

        // Test if there was a query error.
        if (!$insert_result) {
          die("Database query failed INSERT: " . mysql_error($connection));
        }

      }
      // Set messages
      $success_message = "{$url} successfully slimmed!";
      $information_message = "Access your URL at <a href='http://www.slimlink.us/{$trimmed_url}'>www.slimlink.us/{$trimmed_url}</a>";

    } else {
        $error_message = "Sorry, you entered an invalid URL.";
    }

  // If the user has entered in a code via the URI, instead of landing on the main page
  } elseif ("index.php" !== strstr($_SERVER['REQUEST_URI'], "index.php")) {
    diag_echo(strstr($_SERVER['REQUEST_URI'], "trim.php"));
    // Get only the code from the request_uri
    $trim_code = str_replace("/", "", $_SERVER['REQUEST_URI']);
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