<?php


  function redirect($url, $status_code = 303)
  {
    header('Location: ' . $url, true, $status_code);
    die();
  }


  function get_link_url($trim_code) {
    global $connection;

    $query = "SELECT * FROM slimlink";
    $result = mysqli_query($connection, $query);

    if (!$result) {
      die("Database query failed SELECT: " . mysqli_error($connection));
    }

    $db_data = get_array_from_result($result);

    foreach($db_data as $row) {

        if ($row["trimmed_url"] === $trim_code) {
            $url = $row["url"];
            $url = add_http_url($url);
            return $url;
        }

    }

    return False;

  }

  function get_array_from_result($result) {
    $db_data = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        array_push($db_data, $row);
    }

    return $db_data;
  }


  function generate_random_string($length = 6) {
      $characters = 'abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNOPQRSTUVWXYZ';
      $random_string = '';
      for ($i = 0; $i < $length; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
      }
    return $random_string;
  }


  function generate_unique_trimmed_url() {
    global $connection;
    $trimmed_url_exists = True;

    while ($trimmed_url_exists === True) {
      $random = generate_random_string();
      $random = mysqli_real_escape_string($connection, $random);
      $test_query = "SELECT * FROM slimlink ";
      $test_result = mysqli_query($connection, $test_query);

      if (!$test_result) {
        die("Database query failed SELECT: " . mysqli_error($connection));
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


  function diag_echo($string) {
    echo '<br />' . $string . '<br />';
  }


  function result_diag_echo($result) {
    $diag_result = $result;

    while($row = mysqli_fetch_assoc($diag_result)) {
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


?>