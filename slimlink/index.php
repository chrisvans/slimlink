<?php

  function generate_unique_trimmed_url() {
    global $connection;

    $trimmed_url_exists = True;

    while ($trimmed_url_exists === True) {
      $random = substr(md5(microtime()),rand(0,26),6);
      $test_query = "SELECT trimmed_url = '{$random}' FROM slimlink ";
      $test_result = mysqli_query($connection, $test_query);

      if (!$test_result) {
        die("Database query failed SELECT: " . mysqli_error($connection));
      }

      $trimmed_url_exists = exists($test_result);
    }
    return $random;
  }

  function exists($result) {
    return empty($result);
  }

  function diag_echo($string) {
    echo '<br />' . $string . '<br />';
  }

  function result_diag_echo($result) {
    while($row = mysqli_fetch_assoc($result)) {
      var_dump($row);
      echo "<hr />";
    }
  }

  function get_true_or_false($bool) {
    if ($bool === True) {
        return 'True';
    } else {
        return 'False';
    }
  }

  function add_http_url($url) {
    if (strpos($url, "http://") !== True) {
      $new_url = str_replace($url, "http://" . $url, $url);
      return $new_url;
    } else {
      return $url;
    }
  }

  function remove_http_url($url) {
    if (strpos($url, "http://") !== False) {
      $new_url = str_replace("http://", "", $url);
      return $new_url;
    } elseif (strpos($url, "https://") !== False) {
      $new_url = str_replace("https://", "", $url);
      return $new_url;
    }
  }

  function is_valid_url($url) {
    return (filter_var($url, FILTER_VALIDATE_URL) !== false);
  }

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
    $url = $_POST["url"];
    $url = add_http_url($url);
    if (is_valid_url($url) === True) {
      $url = remove_http_url($url);
      $query = "SELECT url = '{$url}' FROM slimlink ";
      // Get resource - Collection of DB Rows
      $result = mysqli_query($connection, $query);
      // Test if there was a query error, but does not test if the query was empty.
      
      if (!$result) {
        die("Database query failed SELECT: " . mysqli_error($connection));
      }
      
      diag_echo('From $result:');
      result_diag_echo($result);
      echo '-----';
      echo '<br />';
      $url_exists = exists($result);
      echo get_true_or_false($url_exists);
      echo '<br />';
      echo '-----';

      $random = generate_unique_trimmed_url();
      diag_echo($random);
      // if ($url_exists === False) {
      //   $url = mysqli_real_escape_string($connection, $url);
      //   $query = "INSERT INTO slimlink (trimmed_url, url) ";
      //   $query .= "VALUES ('{$trimmed_url}', '{$url}')";
      //   $insert_result = mysqli_query($connection, $query);
      //   // Test if there was a query error.
      //   if (!$insert_result) {
      //     die("Database query failed INSERT: " . mysqli_error($connection));
      //   }
      // }

    } else {
        $error_message = "Invalid URL.";
    }

  }

  diag_echo($_POST["url"]);

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