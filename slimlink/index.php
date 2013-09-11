<?php
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
  // Logic to check against POST, valid POST, generate 6 character string.
  // Validate url
  // Check for 'http' in url
  // Remove http:// or https:// from url for generic DB save
  // Check to see if the URL already exists in the DB

  if (isset($_POST["url"])) {
    print_r($_POST); echo '<br />';
    $post_url = $_POST["url"];
    $query = "SELECT url = '{$post_url}' FROM slimlink ";
    // Get resource - Collection of DB Rows
    $result = mysqli_query($connection, $query);
    // Test if there was a query error, not if the query was empty.
    if (!$result) {
      die("Database query failed SELECT: " . mysqli_error($connection));
    }
  }

  $test_string = "https://www.google.com";

  if (strpos($test_string, "http://") !== False) {
    $valid_string = str_replace("http://", "", $test_string);
    echo $valid_string;
  } elseif (strpos($test_string, "https://") !== False) {
    $valid_string = str_replace("https://", "", $test_string);
    echo $valid_string;
  }

  // // If the user sent a valid url via POST, generate a 
  // // trimmed_url and insert it into the DB along with
  // // the base url
  // $trimmed_url
  // $url
  // $query = "INSERT INTO slimlink (trimmed_url, url) ";
  // $query .= "VALUES ('{$trimmed_url}', {$url})";
  // $insert_result = mysqli_query($connection, $query);
  // // Test if there was a query error.
  // if (!$insert_result) {
  //   die("Database query failed");
  // }

?>
<html>

  <title>
    Slimlink
  </title>

  <body>
    
    <form action="bagels.php" method="post">
        Link: <input type="text" name="link" value="" /><br />
        <input type="submit" name="submit" value="Submit" />
    </form>
    
  </body>

</html>