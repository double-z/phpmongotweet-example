<html>
  <head>
    <title>PHPMongoTweet - MongoDB + tweets</title>
    <link href="css/styles.css" rel="stylesheet" type="text/css" />
    <link href="css/tipTip.css" rel="stylesheet" type="text/css" />
    <script src="scripts/jquery-1.7.1.min.js"></script>
    <script src="scripts/sorttable.js"></script>
    <script src="scripts/jquery.tipTip.js"></script>
    <script type="text/javascript">
        function loadmo(term) {
           document.forms["loadform"].q.value = term;
           if ($.trim($("#q").val()) != "") {
              $("#x").fadeIn();
           }
        };

        $(document).ready(
           function() {
              $("td, th").hover(
                 function() { $(this).css("background-color", "#FDDC80"); },
                 function() { $(this).css("background-color", ""); }
              );

              $(function() {
                  $(".homelink").tipTip();
                  $(".searchterm").tipTip();
                  $(".loadtweets").tipTip();
                  $(".who").tipTip();
                  $(".trend").tipTip();
                }
              );

              //  Pretentious ... do^od-le
              $("#q").keyup(
                 function() {
                    $("#x").fadeIn();
                    if ($.trim($("#q").val()) == "") {
                       $("#x").fadeOut();
                    }
                 }
              );

              $("#x").click(
                 function() {
                    $("#q").val("");
                    $(this).hide();
                 }
              );
           }
        );
    </script>
  </head>
  <body>
    <div id="loaddiv">
       <a href="/">
          <img class="homelink" src="images/twitter.jpeg" align="left"
               title="Back to home page">
       </a>
       <h5 id="wip">#search-and-load-tweets-into-mongo</h5>
       <form name="loadform" method="get" action="load-tweets.php">
          <input id="q" class="searchterm" name="q" type="text"
                 title="Find tweets matching search criteria and load into MongoDB"/>
          <div id="delete"><span id="x">x</span></div>
          <input id="submit" class="loadtweets" name="submit" type="submit"
                 value="Load tweets"
                 title="Search and load tweets into MongoDB"/>
       </form>
    </div>
    <br>

<?php
include 'common.php';

// Get tweets collection in MongoDB.
$collection = get_collection(TWEETS_COLLECTION);
$cursor     = $collection->find();
$resarray   = iterator_to_array($cursor);

?>

    <div id="contentdiv">
      <div class="floaterdiv">
        <table id="twtable" class="sortable" cellspacing="0"
               summary="Saved tweets">
<?php
    echo "<caption>MongoDB: A timeline of saved tweets [" . count($resarray) .
         "]<br />an <a href=\"https://openshift.redhat.com/app/\" target=\"_new\">OpenShift</a> demo application with MongoDB -- follow us <a href=\"https://twitter.com/#!/openshift\" target=\"_new\">@openshift</a></caption>\n";
?>
           <tr>
             <th scope="col" abbr="tweets" class="nobackground">timeline</th>
             <th scope="col" abbr="@when">@when</th>
             <th scope="col" abbr="tag">Tag</th>
             <th scope="col" abbr="@who">@who</th>
             <th scope="col" abbr="username">User Name</th>
             <th scope="col" abbr="text">Tweeted</th>
           </tr>

<?php
foreach ($resarray as $d) {
   echo "<tr id='tweetrow'>\n";
   echo "  <td class='when' colspan='2' sorttable_customkey='" .  
               strtotime($d['created_at']) . "'>" .  $d['created_at'] .
        "  </td>\n";
   echo "  <td class='searchtag'>" . $d['search_term'] . "</td>\n";
   echo "  <td class='who' title='search for tweets by user'>" .
        "    <a href='#' onClick='javascript:loadmo(\"" .  $d['from_user'] .
                "\");'>" .  $d['from_user'] . "</a>" .
        "  </td>\n";
   echo "  <td class='username'>" . $d['from_user_name'] . "</td>\n";
   echo "  <td class='tweet'>". $d['text'] . "</td>\n";
   echo "</tr>\n";
}

?>

        </table>
      </div>
      <div class="floaterdiv">
        <table id="trendtable">
          <tr>
            <th scope="col" abbr="trends" class="nobackground">Trending now</th>
            <th scope="col" abbr="term">Tag</th>
          </tr>
<?php

$twitter_uri = "http://api.twitter.com/1/trends/1.json";
$zeecurl = curl_init($twitter_uri);
curl_setopt($zeecurl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($zeecurl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$resp = curl_exec($zeecurl);
curl_close($zeecurl);
$decoded_resp = json_decode($resp);

//  Find trends ... 
foreach ($decoded_resp as $idx => $val) {
   foreach ($val as $k => $v) {
      // echo "<br><p>Processing key " . $k . " ... \n";
      if ($k == "trends") {
         foreach($v as $idx => $trend) {
            // echo "<br><p>trend #" . $idx . " : " . print_r($trend) . "...\n";
            $t = convertToArray($trend);
            echo "<tr id='trendrow'>\n";
            echo "  <td class='trend' colspan='2' title='search for trending tweets'>" .
                 "    <a href='#' onClick='javascript:loadmo(\"" . $t["name"] .
                 "\");'>" .  $t["name"] . "</a>" .
                 "  </td>\n";
            echo "</tr>\n";
         }
      }
   }
}

?>

        </table>
      </div>
      <div id="clearalignment">&nbsp;</div>
    </div>
  </body>
</html>
