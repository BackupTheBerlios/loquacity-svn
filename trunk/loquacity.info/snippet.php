<?php
  /*
  Display Last 5 Phorum Posts
  tested on on phorum 5.0.2 alpha only.
  only bbcode mod is taken care off - bbcode stripped out.
  7 March 2004 (2)
  boyd~at~eldritchdark~dot~com (Boyd Pearson).
  free to be used and abused as you feel fit.
  */
define('PHORUM', true);

include_once("/home/content/l/o/q/loqadmin/html/forum/include/db/config.php"); //change to match your file system
$host 	= $PHORUM[DBCONFIG][server];
$dbuser	= $PHORUM[DBCONFIG][user];
$dbpass	= $PHORUM[DBCONFIG][password];
$dbname	= $PHORUM[DBCONFIG][name];
$table 	= $PHORUM[message_table];
//$forum_table = $PHORUM["forums_table"];
//$date_format = $PHORUM[short_date]; //changes to short_date if prefered
$number	=	25; // number of messages to show.
$count 	= 45; //words per message to show
@$con=mysql_connect($host,$dbuser,$dbpass) or die ("cannot connect to MySQL");
$select_db = mysql_select_db($dbname,$con);

$result = mysql_query("select * from phorum_messages m, phorum_forums f where m.forum_id = f.forum_id order by m.datestamp desc limit $number") or die("Failed Query of ". $result. mysql_error());

//include_once phorum_get_template("header");

while($row = mysql_fetch_array($result)){
    var_dump($row);
    unset($ellipsis); //remove this line to if your not using the ellipsis
    if ($PHORUM[mods][bbcode] ==1){
        $row[body] = preg_replace( "|</*[a-z][^>]*>|i", "", $row[body]);
        $row[body] = preg_replace( "|\[/*[a-z][^\]]*\]|i", "", $row[body]);
    }
    $words = split('[ ]+', $row[body]);
    if (sizeof($words) > $count) {
        $words = array_slice($words, 0, $count);
        $ellipsis = " ..."; //to indicate message body truncation remove if unwanted
    }

    $row[body] = implode($words, ' ');
    $row[body] = trim($row[body]);
    $row[body] = nl2br($row[body]);
    $row[body] = str_replace("<br />"," ",$row[body]);
    $row[body] .= $ellipsis; //remove this line to if you're not using the ellipsis
    $row[datestamp] = strftime($PHORUM["short_date"], $row["datestamp"]);

    //format below to match your site
    echo "<p><strong>$row[name]: <a
    href=\"".$PHORUM[SETTINGS][http_path]."/read.php?".$row[forum_id].",".$row[
    thread].",".$row[message_id]."#".$row[message_id]."\">" .$row[subject].
    "</a>\n";
    echo $row[datestamp];
    echo " by ". $row[author]. "</strong>\n";
    echo "<br>". $row[body]. "</p><hr noshade size='1'>\n";
}//end while
//include phorum_get_template("footer");
?>