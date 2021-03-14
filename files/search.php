<?php
session_start();
include ('client.php');
include ('yacy.php');
// include ('config.php');

$client_ip = client::get_ip();
$client_os = client::get_os();
$client_browser = client::get_browser();
$client_device = client::get_device();

// $conn = new mysqli($servername, $username, $password, $dbname);
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
//     header("Location: /");
// }

header("content-type: text/html; charset=UTF-8");
$useragent = $_SERVER['HTTP_USER_AGENT'];
$search = lcfirst(trim($_SESSION['search']));
$a = strtolower($search);
$a = str_ireplace('what is ', '', $a);
$a = str_ireplace('what is', '', $a);
$a = str_ireplace(' what is', '', $a);
$a = str_ireplace('who is ', '', $a);
$a = str_ireplace('who is', '', $a);
$a = str_ireplace(' who is', '', $a);
$a = strtolower(trim(str_ireplace(' ', '+', $a)));
$desc = "";

if (isset($_POST['submit'])) {
	$searchval = $_POST['search'];
	if (str_ireplace(' ', '', $searchval) == "") {
		header("Location: /");
	}
	else {
		$_SESSION['search'] = $searchval;
		header("Location: /search");
	}
}

if (strlen($a) < 1) {
	header("Location: /");
}
else {
	// GET THE DESCRIPTION
	try {
		$safeurl = str_replace('+', '%20', $a);
		$json_string = file_get_contents("http://en.wikipedia.org/w/api.php?action=query&prop=extracts&format=json&exintro=&titles=" . $safeurl);
        $parsed_json = json_decode($json_string);
		foreach($parsed_json->query->pages as $k) {
            if ($k->extract == null) {
                $desc = "No description was found for this item: \"" . $a . "\"";
            } else {
                $desc = $k->extract;
            }
		}
	} catch(Exception $ignored) {}

    // GET THE SEARCH RESULTS
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "http://localhost:8090/yacysearch.json?&rows=100&query=$a",
        CURLOPT_RETURNTRANSFER => true,
        // CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
    ]);

    $response = curl_exec($curl);
    $response = json_decode($response);
    
    $resultsFound = count($response->channels[0]->items);
    
    $resultsFoundMessage = "$resultsFound Results Found";
    $image = null;

	?>
        <!DOCTYPE html>
        <html>
        <title>The Nexus | Search</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
        <link rel="stylesheet" href="/css/styles.css">
        <style>
            body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
            html {
                overflow: hidden;
                height: 100%;
            }
            body {
                overflow: auto;
                height: 100%;
            }
            .bgimg {
                background-image: url('/img/wallpaper.png');
                min-height: 100%;
                background-position: center;
                background-size: cover;
            }
            input[type=text] {
                height: 46px;
            }
            .blocktitle {
                line-height:6vw;
            }
            @media only screen and (min-width: 0px) {
                .blocktitle {
                    line-height:100%;
                }
            }
            @media only screen and (min-width: 600px) {
                .blocktitle {
                    line-height:100%;
                }
            }
            @media only screen and (min-width: 1080px) {
                .blocktitle {
                    line-height:100%;
                }
            }
        </style>

        <body class="w3-light-grey bgimg">
        <div class="w3-content" style="max-width:1400px;">

            <!-- Header -->
            <header class="w3-container w3-center w3-padding-32"> 
                <a href="/"><img src="img/logo.png" alt="logo" style="width:90%;"></a>
            </header>

            <br>

            <!-- Results -->
            <div class="w3-row">
                <div class="w3-col l4 m6 s12">
                    <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
                        <table class="w3-table">
                            <tr>
                                <td><input style="width:100%;;" type='text' name='search' autocomplete='off' value="<?php echo $_SESSION['search'] ?>"></td>
                                <td><button style="" type='submit' name='submit' value='SEARCH' class='main-btn noselect'>SEARCH</button></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="w3-col l8 m6 s12">
                    <h3 style="color: white; text-align: center;"><?php echo $resultsFoundMessage; ?></h3>
                </div>
            </div>
            <div class="w3-row">
                <?php
                if ($desc != null) {
                    $descript = strip_tags($desc);
                    $descript = mb_strimwidth($descript, 0, 250, "...");
                    ?>
                    <div class="w3-col l4 s12">
                        <!-- About -->
                        <a href="<?php echo 'https://en.wikipedia.org/wiki/' . $safeurl;?>" style="text-decoration: none;">
                            <div class="w3-card-4 w3-margin w3-white">
                            <?php if ($image != null) {echo '<img src="' . $image . '" style="height:30vw;display:block;margin-left:auto;margin-right:auto;">';}?>
                                <div class="w3-container">
                                    <h4><b><?php echo ucwords($a); ?></b></h4>
                                    <p><?php echo $descript; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php } ?>

                <div class="w3-col l8 s12">
                    <?php
                    if (count($response->channels[0]->items) > 0) {
                        foreach ($response->channels[0]->items as $row) {
                            $title = $row->title;
                            $url = $row->link;
                            $url_short = mb_strimwidth($url, 0, 50, "...");
                            $content = "";
                            $content = $row->description;
                            $content = str_ireplace($a, '<b>' .$a .'</b>', $content);
                            $content = mb_strimwidth($content, 0, 250, "...");
                            if (strlen(trim($content)) == 0) {
                                $content = "No Description";
                            }
                            $updated = $row->pubDate;
                            $updated = explode(" ",$updated)[0] . " " . explode(" ",$updated)[1] . " " . explode(" ",$updated)[2] . " " . explode(" ",$updated)[3];
                            $updated = '<b>[' . $updated . ']</b>';
                            if ($title != null) {
                                ?>
                                    <!-- Result entry <?php echo $title;?>-->
                                    <div class="w3-card-4 w3-margin w3-white">
                                        <div class="w3-container" style="">
                                            <br style="line-height: 0.0000001%;">
                                            <a href="<?php print_r($url); ?>" style="text-decoration: none;!important; line-height:0.2vw;">
                                                <h7 style=" word-wrap: break-word;"><?php print_r($url_short); ?></h7>
                                                <h3><b class="blocktitle"><?php print_r($title); ?></b></h3>
                                                <p style="line-height:3vh;"><?php print_r($updated); ?> <?php print_r($content); ?></p>
                                            </a>
                                        </div>
                                    </div>
                                <?php
                            }
                        }
                    } ?>
                </div>
            </div>
            <br>
        </div>
        </body>
        </html>
        <?php
}
?>
