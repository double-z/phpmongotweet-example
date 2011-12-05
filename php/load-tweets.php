<html>
  <head>
    <title>Tweet loader</title>
    <meta http-equiv="refresh" content="10; url=/">
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link href="css/tipTip.css" rel="stylesheet" type="text/css" />
    <script src="scripts/jquery-1.7.1.min.js"></script>
    <script src="scripts/sorttable.js"></script>
    <script src="scripts/jquery.tipTip.js"></script>
    <script type="text/javascript">
        $(document).ready(
           function() {
              $(function() { $(".homelink").tipTip(); });
           }
        );
    </script>
  </head>
  <body>

    <a href="/">
       <img class="homelink" src="images/twitter.jpeg" align="left"
            title="Back to home page">
    </a>
    <br><br>
    <p class="hint">
       Hint: useable options are [-d|-delta] [-r|-recreate] [-q|-query <term>]
    <br><br>

<?php

include 'common.php';

//  Check if we need to recreate the collection.
$r_opts = array('r', 'recreate');
if (true == is_option_set($r_opts) ) {
   $collection = get_collection(TWEETS_COLLECTION);
   $collection->drop();
}

//  Get tweets collection in MongoDB and last tweet ID.
$collection = get_collection(TWEETS_COLLECTION);
$lastobj    = $collection->findOne();
$lastid     = $lastobj["id"];

//  Set the search term.
$search_term = "openshift";
$q_opts = array('q', 'query');
if (true == is_option_set($q_opts) ) {
   $search_term = get_option_value($q_opts);
}

//  Set twitter search api uri.
// $twitter_uri = "http://api.twitter.com/1/statuses/user_timeline.json?screen_name=" . $screen_name;
$twitter_uri = "http://search.twitter.com/search.json?q=" . $search_term . "&rpp=70";

$d_opts = array('d', 'delta');
if ((true == is_option_set($d_opts))  && ($lastid != null) ) {
   $twitter_uri .= "&since_id=" . $lastid;
}

//  Search for newer tweets.
$zeecurl = curl_init($twitter_uri);
curl_setopt($zeecurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($zeecurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$resp = curl_exec($zeecurl);
curl_close($zeecurl);

$decoded_resp = json_decode($resp);

//  Insert each tweet into the MongoDB collection. 
$beancounter = 0;
foreach ($decoded_resp as $k => $v) {
   // echo "<br><p>Processing key " . $k . " ... \n";
   if ($k == "results") {
      foreach($v as $idx => $tweet) {
         // echo "<br><p>loading item #" . $idx . " : " . print_r($tweet) . "...\n";
         $entry = convertToArray($tweet);
         $entry["search_term"] =  $search_term;
         $collection->insert($entry);
         $beancounter++;
      }
   }
}

echo "<p><b>Search URI: </b><font size='-1'>" . $twitter_uri . "</font>\n";
echo "<br/>\n";
echo "<p><b>Tweets Loaded: " . $beancounter . "</b>\n";

?>

    <br><br/>
    You will soon be automatically redirected back to the home page ...
  </body>
</html>
