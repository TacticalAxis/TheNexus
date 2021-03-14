<?php
session_start();
include ('client.php');
include ('config.php');

$client_ip = client::get_ip();
$client_os = client::get_os();
$client_browser = client::get_browser();
$client_device = client::get_device();

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
    header("Location: /");
}

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
    // $b = explode(" ", $a);
    $sql = "SELECT `updated`,`title`,`url`,`content` FROM link_content WHERE content LIKE '%$a%'";
    $response = $conn->query($sql);
    $resultsFound = $response->num_rows;
    
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
        </style>
        <body class="w3-light-grey bgimg">
        <div class="w3-content" style="max-width:1400px;">

            <!-- Header -->
            <header class="w3-container w3-center w3-padding-32"> 
                <a href="/"><img src="img/logo.png" alt="logo" style="width:60vw;"></a>
                <span>
                    <div style="position: absolute; margin-top:0%; margin-left:3%">
                        <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post'>
                            <table id="customers">
                                <tr>
                                    <th><input type='text' name='search' autocomplete='off' value="<?php echo $_SESSION['search'] ?>"></th>
                                    <th><button style="text-align: center;" type='submit' name='submit' value='SEARCH' class='main-btn noselect'>SEARCH</button></th>
                                </tr>
                            </table>
                        </form>
                    </div>
                </span>
            </header>

            <br>

            <!-- Results -->
            <div class="w3-row">
                <?php
                if ($desc != null) {
                    $test = strip_tags($desc);
                    $test = mb_strimwidth($test, 0, 250, "...");
                    ?>
                    <a href="<?php echo 'https://en.wikipedia.org/wiki/' . $safeurl;?>">
                        <div class="w3-col l4">
                            <!-- About -->
                            <div class="w3-card w3-margin w3-margin-top">
                            <?php if ($image != null) {echo '<img src="' . $image . '" style="height:30vw;display:block;margin-left:auto;margin-right:auto;">';}?>
                                <div class="w3-container w3-white">
                                    <h4><b><?php echo ucwords($a); ?></b></h4>
                                    <p><?php echo $test; ?></p>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php } ?>

                <div class="w3-col l8 s12">
                    <h3 style="color: white; text-align: center;"><?php echo $resultsFoundMessage; ?></h3>
                    <?php
                    if ($response->num_rows > 0) {
                        while($row = $response->fetch_assoc()) {
                            $title = $row["title"];
                            $url = $row["url"];
                            $url_short = mb_strimwidth($url, 0, 100, "...");
                            $content = "";
                            $contentExplode = preg_split("/" . mysqli_real_escape_string($conn, $a) . "/i", $row["content"]);
                            $content = "<b>" . $a . "</b>" . mb_strimwidth($contentExplode[1], 0, 250, "...");
                            $updated = $row["updated"];
                            if ($title != null) {
                                ?>
                                    <!-- Result entry <?php echo $title;?>-->
                                    <div class="w3-card-4 w3-margin w3-white">
                                        <div class="w3-container">
                                            <a href="<?php print_r($url); ?>">
                                                <h7 style="line-height:0.7; text-decoration: none;"><?php print_r($url_short); ?></h7>
                                                <h3><b> <?php print_r($title); ?></b></h3>
                                            </a>
                                        </div>
                                        <div class="w3-container">
                                            <p><?php print_r(explode(" ",$updated)[0]); ?> <?php print_r($content); ?></p>
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
$conn->close();
?>
