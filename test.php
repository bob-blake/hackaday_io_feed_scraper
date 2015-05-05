<?php
// test.php
// Test file for hackaday.io global feed scraper.  
// Duplicates functionality of scraper.php but prints output to screen instead of saving to db.
// http://www.bobblake.me/had_scraper/test.php
// Author: Bob Blake
// Date: April 30, 2015

// NOTE: several quick updates don't necessarily make several posts on the feed - the time just updates
// TODO: Error checking and handling!

require("../../php/had_scraper/vars.php");
require("/phpQuery/phpQuery.php");

$finished = 0;
$pagenum = 1;

// This is deprecated, I know I know
//$server = mysql_connect(DB_HOST,DB_USER,DB_PASS);
//$dbcnx = @mysql_select_db(DB_NAME);

echo "<html>
      <head></head>
      <body>
        <p>";

while($finished == 0 && $pagenum < 20){ // Max 20 pages (arbitrarily large, just in case)
  $doc = phpQuery::newDocumentFileHTML("https://hackaday.io/feed?page=$pagenum");
  $now = time();
             
  foreach(pq("div#feeds ul.feed-list > li") as $feed_item){
    if(pq($feed_item)->find(".feed-time")->html() == "an hour ago"){   // Only grab an hour of posts to get to-the-minute precision
      $finished = 1;    
      break;
    }

    // Get post information
    $post_class = pq($feed_item)->attr("class");
    $post_title = htmlspecialchars(pq($feed_item)->find(".feed-title")->html());   // "contributorsAdded" posts have a bug - h3 encloses the feed-info class
    $post_user = ltrim(pq($feed_item)->find(".feed-meta a")->attr("href"),"/hacker/");

    // Parse time of post
    $post_time_str = pq($feed_item)->find(".feed-time")->html();

    if($post_time_str == "a few seconds ago")  // strtotime can't handle "a few seconds ago"
      $post_time = $now;
    else if($post_time_str == "a minute ago")  // strtotime also can't handle "a minute ago"
      $post_time = strtotime("1 minute ago",$now);
    else
      $post_time = strtotime($post_time_str,$now);

    $mysql_time = gmdate("Y-m-d H:i:s", $post_time); // Convert for MySQL and make sure it's in GMT


    echo "<br />-----New Item-----<br />";
    echo "Class: $post_class <br />";
    echo "Title: " . htmlspecialchars_decode($post_title) . "<br />";
    echo "Time: $mysql_time  <br />";
    echo "User: $post_user  <br />";

    //$query = "INSERT INTO feed_items (class, title, date_time, orig_user) VALUES ('" . 
    // $post_class . "', '" . $post_title . "', '" . $mysql_time . "', '" . $post_user . "')"; 

    //$add_data = mysql_query($query);

    //$err = mysql_error();
    //if($err){
    //  $file = 'datalog_errors.txt';
    //  file_put_contents($file, $err, FILE_APPEND | LOCK_EX);
    //}

    }
  $pagenum++;
}

//mysql_close($server);

echo "      </p>
      </body>
      </html>";
?>