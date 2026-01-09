<?php
/**
 * Plugin Name:       Web-Fuse mail From fix
 * Plugin URI:        https://web-fuse.nl/wordpress
 * Description:       Overwrite the mail from header when sending mail using wp_mail function
 * Version:           1.0
 * Requires at least: 7.0
 * Requires PHP:      7.4
 * Author:            Web-Fuse
 * Author URI:        https://web-fuse.nl
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */

class mail_filter {
  private $mysqldb;
  private $conn;
  private $message;
  private $detected_spam;
  private $log_id;

  function __construct() {
    add_action('phpmailer_init', array($this, 'align_return'));
    add_filter('wp_mail_from', array($this, 'wp_sender_email'));
  }

   // Function to change email address
   // very likely this can be moved to the phpmailer_init action too
  function wp_sender_email($original_email_address) {
    // $this->sender_mail = $this->conn->real_escape_string($original_email_address);

    $strArr = explode(".", $_SERVER['HTTP_HOST']);
    $domain = $strArr[count($strArr) - 2];

    // $this->log();

    return $domain . '@web-fuse.nl';
  }

  function connect() {
    $mysqlserver = "172.0.0.80";
    $mysqluser = "mail";
    $mysqlpass = "ViwS2Jxp5GmbASaY";
    $this->mysqldb = "mail_log";

    $this->conn = new mysqli($mysqlserver, $mysqluser, $mysqlpass, $this->mysqldb);

    if ($this->conn->connect_error) die("Connection failed: " . $conn->connect_error);

    $this->check_table();
  }

  function check_table() {
    $db = $this->mysqldb;
    $sql = "SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE (TABLE_NAME = 'log' AND TABLE_SCHEMA LIKE '$db');";
    $result = $this->conn->query($sql);
    if (!$result) die("error: " . $this->conn->error);

    if ($result->num_rows == 0) $this->create_table();
  }

  function create_table() {
    $sql = 'CREATE TABLE log (log_id int(11) NOT NULL AUTO_INCREMENT, spam BOOLEAN NOT NULL DEFAULT false, rcpt_to varchar(255) NOT NULL, subject varchar(255) NOT NULL, message TEXT NOT NULL, headers TEXT NOT NULL, from_name varchar(255) NOT NULL, mail_from varchar(255) NOT NULL, remote_addr varchar(15) NOT NULL, host varchar(255) NOT NULL, query varchar(255) NOT NULL, datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (log_id));';
    if (!$this->conn->query($sql)) die("error: " . $this->conn->error);

    $sql = "CREATE TABLE urls (url_id int(11) NOT NULL AUTO_INCREMENT, url varchar(255) NOT NULL UNIQUE, log_id int(11) NOT NULL, datetime TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (url_id), FOREIGN KEY (log_id) REFERENCES log(log_id));";
    if (!$this->conn->query($sql)) die("error: " . $this->conn->error);
  }

  function align_return($phpmailer) {
    $this->connect();

    $phpmailer->Sender = $phpmailer->From;

    $to = "";
    $recipients = $phpmailer->getAllRecipientAddresses();
    foreach($recipients as $key => $val) {
      echo "\nkey: " . $key;
      $to .= $key . ", ";
    }
    $to = $this->conn->real_escape_string(trim($to, " ,\n\t\0\r"));
    $subject = $this->conn->real_escape_string($phpmailer->Subject);
    $message = $this->conn->real_escape_string($phpmailer->Body);
    $this->$message = $message;
    if (!empty($phpmailer->getCustomHeaders())) {
      $headers = $this->conn->real_escape_string(print_r($phpmailer->getCustomHeaders(), true));
    } else {
      $headers = "";
    }

    $remote_addr = $_SERVER['REMOTE_ADDR'];
    $host = $_SERVER['HTTP_HOST'];
    $query = $_SERVER['REQUEST_URI'];

    $from_name = $phpmailer->FromName;
    $mail_from = $phpmailer->From;

    $spamIP = $this->get_spam_ip($remote_addr);

    $sql = "INSERT INTO log (rcpt_to, spam, subject, message, headers, from_name, mail_from, remote_addr, host, query) VALUES ('$to', '$spamIP','$subject', '$message', '$headers', '$from_name', '$mail_from', '$remote_addr', '$host', '$query') RETURNING log_id;";
    if (!$result = $this->conn->query($sql)) die("error: " . $this->conn->error);

    $this->log_id = $result->fetch_assoc()["log_id"];

    if ($spamIP) {
      $this->get_sus_url();

      status_header(500);
    } else {
      $this->scan_sus_url();
      $this->scan_sus_regex();
    }
  }

  /**
   * check if the ip previously send spam in the last month
   * @return {int} 0 or 1 like boolean
   */
  function get_spam_ip($ip) {
    $sql = "SELECT COUNT(log_id) FROM log WHERE remote_addr = '$ip' AND datetime > ";
    $result = $this->conn->query($sql);
    $count = $result->fetch_assoc();

    if ($count[0] > 0) return 1;
    return 0;
  }

  /**
   * find any links that are most likely suspicious and store them in the database
   */
  function get_sus_url() {
    preg_match_all("/(https?:\/\/|[\s'\"])([\w.-]+\.\w+)([\/?#][\S]+[^'\"\/?#])*/", $this->message, $matches); // should match all urls
    $hostArr = explode(".", $_SERVER['HTTP_HOST']);
    $host = $hostArr[count($hostArr) - 2];
    $good_host = array($host, "google", "w3.com", "w3.org", "microsoft", "gmail", "outlook", "youtu.be", "youtube", "goog.le");
    $good_cnt = count($good_host);

    for ($i = 0; $i < count($matches[2]); $i++) {
    // foreach($matches[0] as $match) {
      $match = $matches[0][$i];
      $match_host = $matches[2][$i];
      // // remove
      // if (strpos($match, "https://")) $match = substr($match, 8);
      // else $match = ltrim($match, " \n\t\v\r\0\"'");

      // $match_host = preg_split("[/?#]", $match, 1)[0]; // only get the host part of the match
      $good = 0;

      foreach($good_host as $url) {
        // expect to increase this number with all urls but one
        // if if matches a good_host then it should be ignored
        if (strpos($match_host, $url)) $good++;
      }

      if ($good == 0) {
        // add to bad urls list
        $esc_match = $this->conn->real_escape_string($match);
        $log_id = $this->log_id;
        $sql = "INSERT IGNORE INTO urls (url, log_id) VALUES ('$esc_match', '$log_id');";
        if (!$this->conn->query($sql)) die("error: " . $this->conn->error);
      }
    }
  }

  /**
   * get common url's from known spam senders and test if they exist in this message
   * set detected_spam to 1 if it has found any
   */
  function scan_sus_url() {
    $sql = "SELECT url FROM urls"; // maybe add a date limiter
    if (!$result = $this->conn->query($sql)) die("error: " . $this->conn->error);

    while ($url = $result->fetch_assoc()["url"]) {
      if (strpos($this->message, $url)) {
        $this->set_spam();
        break;
      }
    }
  }

  /**
   * get common regex from sql and test if they exist in this message
   * set detected_spam to 1 if it has found any
   */
  function scan_sus_regex() {

  }

  /**
   * mark message as spam in log
   * return 500 status
   */
  function set_spam() {
    $this->detected_spam = 1;
    $log_id = $this->log_id;
    $sql = "UPDATE log SET spam = 1 WHERE log_id = $log_id;";
    if (!$this->conn->query($sql)) die("error: " . $this->conn->error);
    status_header(500);
  }
}

new mail_filter();