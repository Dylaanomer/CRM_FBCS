<?php 

include 'dbh.php';

function checkLoginCookie() {
  global $conn;
  
  //check if cookies are set 
  if (isset($_COOKIE["name"]) && isset($_COOKIE["uuid"])) {
    $name = htmlspecialchars($_COOKIE["name"]);
    $uuid = htmlspecialchars($_COOKIE["uuid"]);
    
    //find cookie in database
    $sql = "SELECT expires, session FROM cookies WHERE (username LIKE '$name' AND uuid LIKE '$uuid');";
    if (!$result = $conn->query($sql)) {
      return returnResponse("error", $sql."<br/>".$conn->error);
    }
  
    if ($result->num_rows < 1) {
      return returnResponse("error", "the cookie was not found");
    }
  
    //if the cookie was found
    $row = $result->fetch_assoc();
    $expires = date("Y-m-d h:i:s", strtotime($row["expires"]));
    $now = new DateTime();
    $now = $now->format("Y-m-d h:i:s");

    $path = '/';
    $domain = '';
    $secure = false;
    
    //check if the cookie has expired
    if ($row["session"] === "1") {
      return returnResponse("success", "session is valid");
    } else if ($now > $expires) {
      $sql = "DELETE FROM cookies WHERE (username LIKE '$name' AND uuid LIKE '$uuid');";
      if (!$conn->query($sql)) {
        return returnResponse("error", "cookie was expired but could not be removed $conn->error");
      }

      //remove the current cookies
      setcookie("name", "", time()-3600, $path, $domain, $secure);
      setcookie("uuid", "", time()-3600, $path, $domain, $secure);
  
      return returnResponse("error", "cookie has expired");
    } else {
      $week = new DateInterval("P7D");
      $nextWeek = new DateTime();
      $nextWeek->add($week);
      $nextWeek = $nextWeek->format("Y-m-d H:i:s");

      $expires = time() + (86400 * 7); //86400 is one day in seconds

      if (!setcookie("name", $name, $expires, $path, $domain, $secure)) {
        return returnResponse("error", "could not set name cookie");
      }
      
      if (!setcookie("uuid", $uuid, $expires, $path, $domain, $secure)) {
        return returnResponse("error", "could not set uuid cookie");
      }

      $sql = "UPDATE cookies SET expires = '$nextWeek' WHERE uuid = '$uuid'";
      if (!$conn->query($sql)) {
        return returnResponse("error", $sql."<br/>".$conn->error);
      }
    
      return returnResponse("success", "the cookie is valid for another week");
    }
  }
  
  return returnResponse("error", "no cookies");
}

function returnResponse($status, $msg) {
  global $conn;
  $conn->close();
  $res = array("status" => $status,
                "msg" => $msg);
  return json_encode($res);
}

?>