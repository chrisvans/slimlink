<?php


  // Redirect to stored webpage upon valid code entry through URI
  function redirect($url, $status_code = 303)
  {
    header('Location: ' . $url, true, $status_code);
    die();
  }


  // Take the user's code input from URI and get the desired ( associated ) URL from the DB
  function get_link_url($trim_code) {
    // Bring in DB $connection for use
    global $connection;

    $query = "SELECT * FROM slimlink";
    $result = mysql_query($query, $connection);

    // Error detection for bad query
    if (!$result) {
      die("Database query failed SELECT: " . mysql_error($connection));
    }

    // Store data in an array for use, rather than re-using the result itself
    $db_data = get_array_from_result($result);

    foreach($db_data as $row) {
        
        // Find the associated URL in the DB, return it
        if ($row["trimmed_url"] === $trim_code) {
            $url = $row["url"];
            // $url = add_http_url($url);
            return $url;
        }

    }

    return False;

  }


  // Helper function for producing an associative array from a DB query result
  function get_array_from_result($result) {
    $db_data = array();
    
    while ($row = mysql_fetch_assoc($result)) {
        array_push($db_data, $row);
    }

    return $db_data;
  }


  // Helper function for generating a random 6 character string, without confusing characters such as i, I, or l
  function generate_random_string($length = 6) {
      $characters = 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNOPQRSTUVWXYZ';
      $random_string = '';
      for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
      }
    return $random_string;
  }


  // Generate a unique 6 letter string based on existing DB entries, and use it as a code for finding a URL
  function generate_unique_trimmed_url() {
    global $connection;
    $trimmed_url_exists = True;

    while ($trimmed_url_exists === True) {
      $random = generate_random_string();
      $random = mysql_real_escape_string($random, $connection);
      $test_query = "SELECT * FROM slimlink ";
      $test_result = mysql_query($test_query, $connection);

      if (!$test_result) {
        die("Database query failed SELECT: " . mysql_error($connection));
      }
      
      $db_data = get_array_from_result($test_result);
      
      $exist_check = False;
      
      foreach($db_data as $row) {

        if ($row["trimmed_url"] === $random) {
            $exist_check = True;
        }

      }
      
      if (!($exist_check === True)) {
        $trimmed_url_exists = False;
      }

    }

    return $random;
  }


  // Helper function to see if a URL already exists in the DB
  function url_exists_in_db($db_data, $url) {

    foreach($db_data as $row) {

        if ($row["url"] === $url) {
            global $trimmed_url;
            $trimmed_url = $row["trimmed_url"];
            return True;
        }

    }
    
    return False;
  }


  // Diagnostic function to display a string with spacing
  function diag_echo($string) {
    echo '<br />' . $string . '<br />';
  }


  // Diagnostic function to display DB data
  // Note: Do not use this in production code, or more than once on the same data.
  function result_diag_echo($result) {
    $diag_result = $result;

    while($row = mysql_fetch_assoc($diag_result)) {
      var_dump($row);
      echo "<hr />";
    }

  }


  // Get a better readout for True and False values, as 1's and blank spaces can be confusing to diagnose with
  function get_true_or_false($bool) {

    if ($bool === True) {
        return 'True';
    } else {
        return 'False';
    }

  }


  // Helper function for adding http to a string, and detecting if it needs it or not
  // function add_http_url($url) {

  //   if (strpos($url, "http://") !== True) {
  //     $new_url = str_replace($url, "http://" . $url, $url);
  //     return $new_url;
  //   } else {
  //     return $url;
  //   }

  // }


  // Helper function for removing http from a string, and detecting if it needs it or not
  // function remove_http_url($url) {

  //   if (strpos($url, "https://") !== False) {
  //     $new_url = str_replace("https://", "", $url);
  //     return $new_url;
  //   } elseif (strpos($url, "http://") !== False) {
  //     $new_url = str_replace("http://", "", $url);
  //     return $new_url;
  //   }

  // }


  // Helper function to verify that a URL is valid before acting upon it
  function is_valid_url($url) {
    return (filter_var($url, FILTER_VALIDATE_URL) !== false);
  }


?>