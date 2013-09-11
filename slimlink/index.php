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
  $test_string2 = "http://www.google.com";
  
  function is_valid_url($url) {
    return (filter_var($url, FILTER_VALIDATE_URL) !== false);
  }

  function get_true_or_false($bool) {
    if ($bool === True) {
        return 'True';
    } else {
        return 'False';
    }
  }

  function edit_url($url) {
    if (strpos($url, "http://") !== False) {
      $valid_string = str_replace("http://", "", $url);
      return $valid_string;
    } elseif (strpos($url, "https://") !== False) {
      $valid_string = str_replace("https://", "", $url);
      return $valid_string;
    }
  }

  echo edit_url($test_string);
  echo '<br />';
  echo edit_url($test_string2);

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