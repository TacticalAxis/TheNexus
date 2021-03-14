<?php
  //open connection to peer    
  $YaCyURL="http://localhost:8090/";  
  $cu=$YaCyURL."Yacysearch.rss";
  $cu=$cu."?query=yacy";
  $cu=$cu."&maximumRecords=10";
  $cu=$cu."&startRecord=21";
  $queryServer = curl_init($cu);     
  curl_setopt($queryServer, CURLOPT_HEADER, 0);
  curl_setopt($queryServer, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($queryServer, CURLOPT_USERPWD,$appID);
  $results = curl_exec($queryServer);
  curl_close($queryServer);  
  //now we have xml/json, put it in a simple array
  $resultarray=xml2array($results); 
  //item childgroup 
  $items=$resultarray['rss']['channel']['item'];
  if ($items)
  {
   foreach ($items as $item)
   {   
    echo "<a href=".$item['link'].">".$item['title']."</a>";
   }
  } else {
    echo "no results";
  }
?>