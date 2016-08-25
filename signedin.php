<?php
    session_start();
    //Lets check the session is set, is valid, has a username and has not timed out 
    if (!isset($_SESSION) || !$_SESSION['valid'] || !isset($_SESSION['username']) || (($_SESSION['timeout']-time()) < 5*60)) {
        //If session is not all good we redirect to the script logout.php to destroy the session and redirect to the login page
        header("location: logout.php");
        exit();
    }
?>
<html lang = "en">
   
   <head>
      <title>PHP / MySQL Welcome</title>
      <link href = "../css/bootstrap.min.css" rel = "stylesheet">
      <link href = "../css/login.css" rel = "stylesheet">

   </head>
	
   <body>
       <div> 
        <p>
        <?php
            //From above the session is valid, the username is set and the session has not timed out
            //Lets welcome the user personally
            echo '<h2> Welcome ' . $_SESSION['username'] . ' - you have successfully logged in!</h2>';
            if (!isset($_SESSION['lastlogindatetime']) || $_SESSION['lastlogindatetime']== '')  {
                //Since the lastlogindatetime is not set, this must be the first time the user has logged in
                echo '<h2> This must be the first time you have logged in. </h2>';
            } else {
                //lastlogindatetime is set so lets retrieve it from the session object and contruct a DateTime object using it
                $phpdate = new DateTime($_SESSION['lastlogindatetime']);
                //Let set the correct time zone so that we get daylight saving adjusted time
                date_timezone_set($phpdate, timezone_open('Europe/London'));
                //Lets inform the user when they last logged in 
                echo '<h2> The last time you logged in was ' . date_format( $phpdate, 'H:i:s d M Y ' ) . '  </h2>';
            }    
         ?>
            <h2><a href="logout.php"> click here to log out</a></h2>
            </p>
        </div>
    </body>        
</html>
