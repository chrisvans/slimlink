<?php 
  
  require("includes.php");

  $error_message = '';
  $success_message = '';
  $information_message = '';

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

  if (isset($_POST["url"])) {
    $url = $_POST["url"];
    $url = add_http_url($url);

    if (is_valid_url($url) === True) {
      $url = remove_http_url($url);
      $url = mysqli_real_escape_string($connection, $url);
      $query = "SELECT * FROM slimlink ";
      // Get resource - Collection of DB Rows
      $result = mysqli_query($connection, $query);
      // Test if there was a query error, but does not test if the query was empty.
      
      if (!$result) {
        die("Database query failed SELECT: " . mysqli_error($connection));
      }
        
      $db_data = get_array_from_result($result);
      $url_exists = url_exists_in_db($db_data, $url);

      if ($url_exists === False) {
        $trimmed_url = generate_unique_trimmed_url();
        $url = $url;
        $query = "INSERT INTO slimlink (trimmed_url, url) ";
        $query .= "VALUES ('{$trimmed_url}', '{$url}')";
        $insert_result = mysqli_query($connection, $query);
        // Test if there was a query error.

        if (!$insert_result) {
          die("Database query failed INSERT: " . mysqli_error($connection));
        }

      }
      $success_message = "{$url} successfully trimmed!";
      $information_message = "Access your URL at www.slimlink.com/{$trimmed_url}";

    } else {
        $error_message = "Invalid URL.";
    }

  } elseif ("trim.php" === strstr($_SERVER['REQUEST_URI'], "trim.php")) {
    $trim_code = str_replace("/slimlink/", "", $_SERVER['REQUEST_URI']);
    $redirect_url = get_link_url($trim_code);

    if ($redirect_url === False) {
        $error_message = "Invalid Slimlink Provided.";
    } else {
        echo $redirect_url;
        // redirect($redirect_url);
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