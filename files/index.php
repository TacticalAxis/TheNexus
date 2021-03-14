<?php
session_start();

if(isset($_POST['submit'])) { 
    $searchval = $_POST['search'];
    if(str_ireplace(' ','',$searchval) == "") {
        header("Location: /");
    } else {
        $_SESSION['search'] = $searchval;
        header("Location: /search");
    }
}
?>
<!DOCTYPE html>
<html>
    <title>The Nexus | Home</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    
    <style>
        html {
            overflow: hidden;
            height: 100%;
        }
        body {
            overflow: auto;
            height: 100%;
        }
        body,h1 {
            font-family: "Raleway", sans-serif;
        }
        body, html {
            height: 100%;
        }
        .bgimg {
            background-image: url('/img/wallpaper.png');
            min-height: 100%;
            background-position: center;
            background-size: cover;
        }
        .main-btn {
            display: inline-block;
            padding: 10px 35px;
            margin: 3px;
            border: 2px solid transparent;
            border-radius: 3px;
            -webkit-transition: 0.2s opacity;
            transition: 0.2s opacity;
            background: #6195FF;
            color: #FFF;
        }
        input[type="text"] {
            height: 40px;
            width: 100%;
            border: none;
            background: #F4F4F4;
            border-bottom: 2px solid #EEE;
            color: #354052;
            padding: 0px 10px;
            opacity: 0.5;
            -webkit-transition: 0.2s border-color, 0.2s opacity;
            transition: 0.2s border-color, 0.2s opacity;
        }

        input[type="text"]:focus {
            border-color: #6195FF;
            opacity: 1;
        }

        .noselect {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media only screen and (min-width: 0px) {
            .title-logo {
                width: 90vw;
            }
            .credits {
                font-size: 100%;
            }
            .searchbox {
                height: 4vw;
                width: 10vw;
            }
        }
        @media only screen and (min-width: 600px) {
            .title-logo {
                width: 80vw;
            }
            .credits {
                font-size: 100%;
            }
            .searchbox {
                height: 4vw;
                width: 10vw;
            }
        }
        @media only screen and (min-width: 1080px) {
            .title-logo {
                width: 60vw;
            }
            .credits {
                font-size: 100%;
            }
            .searchbox {
                height: 4vw;
                width: 10vw;
            }
        }

    </style>
    <body>

        <div class="bgimg w3-display-container w3-animate-opacity w3-text-white noselect">
            <div class="w3-display-middle">
                <img src="img/logo.png" alt="logo" class="title-logo">
                <br><br><br>
                <form action='<?php echo $_SERVER['PHP_SELF']; ?>' method='post' style="text-align:center;">
                    <input class="searchbox" type='text' size='35vw' name='search' autocomplete='off' placeholder='The world is at your fingertips...'></br></br>
                    <button type='submit' name='submit' value='SEARCH' class='main-btn noselect'>
                        SEARCH
                    </button>
                    <br><br>
                    <a href="https://yacy.net/" class="credits" target="_blank" style="text-decoration: none;"> Powered by YaCy</a>
                </form>
                <br>
            </div>
            <br>
        </div>
    </body>
</html>