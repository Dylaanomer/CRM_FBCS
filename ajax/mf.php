<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message =
"If you ever need Negative SEO to de-rank any site, you can hire us here\n" .
"https://www.speed-seo.net/product/negative-seo-service
Hi, this is Chris.
Who win all online casinos by using FREE BONUS.

Witch mean, I don�t really spend money in online casinos.

But I win every time, and actually, everybody can win by following my directions.

even you can win!

So, if you�re the person, who can listen to someone really smart, you should just try!!

The best online casino, that I really recommend is, Vera&John.
Established in 2010 and became best online casino in the world.

They give you free bonus when you charge more than $50.
If you charge $50, your bonus is going to be $50.

If you charge $500, you get $500 Free bonus.
You can bet up to $1000.

Just try roulette, poker, black jack�any games with dealers.
Because dealers always have to make some to win and, only thing you need to do is to be chosen.

Don�t ever spend your bonus at slot machines.
YOU�RE GONNA LOSE YOUR MONEY!!

Next time, I will let you know how to win in online casino against dealers!!

Don�t forget to open your VERA&JOHN account, otherwise you�re gonna miss even more chances!!


Open Vera&John account (free)
https://bit.ly/3wZkpco

Hi, this is Anna. I am sending you my intimate photos as I promised. https://tinyurl.com/yhexa7ln
";

echo $message . "<br/>\n";

/**
   * find any links that are most likely suspicious and store them in the database
   */
  function get_sus_url($message) {
    preg_match_all("/(https?:\/\/|[\s'\"])([\w.-]+\.\w+)([\/?#][\S]+[^'\"\/?#])*/g", $message, $matches); // should match all urls
    $hostArr = explode(".", $_SERVER['HTTP_HOST']);
    $host = $hostArr[count($hostArr) - 2];
    $good_host = array($host, "google", "w3.com", "w3.org", "microsoft", "gmail", "outlook");
    $good_cnt = count($good_host);

    var_dump($matches);
    echo "<br/>\n";

    for ($i = 0; $i < count($matches[2]); $i++) {
    // foreach($matches[0] as $match) {
      $match = $matches[0][$i];
      $match_host = $matches[2][$i];
      // // remove
      // if (strpos($match, "https://")) $match = substr($match, 8);
      // else $match = ltrim($match, " \n\t\v\r\0\"'");

      // $match_host = preg_split("[/?#]", $match, 1)[0]; // only get the host part of the match
      $good = 0;

      echo "match: " . $match . "<br/>\n";
      echo "host:  " . $match_host . "<br/>\n";

      foreach($good_host as $url) {
        // expect to increase this number with all urls but one
        // if if matches a good_host then it should be ignored
        if (strpos($match_host, $url)) $good++;
      }

      if ($good == 0) {
        // add to bad urls list
        echo "bad:   " . $match . "<br/>\n";
      }
    }
  }

function scan_sus_url($message) {
    $urls = array("https://www.speed-seo.net/product/negative-seo-service", "google.com");

    foreach ($urls as $url) {
      if (strpos($message, $url)) {
        echo "found spam<br/>\n";
        break;
      }
    }
  }

get_sus_url($message);
scan_sus_url($message);