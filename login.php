<?php
   ob_start();
   session_start();
            $msg = '';
            //lets check to see if the user has submitted username and password
            //if they have we need to check them against coreect username and password pairs in database
            if (isset($_POST['login']) && !empty($_POST['loginusername']) 
               && !empty($_POST['loginpassword']))
			    try {
			        //connection details for database held in config.ini file
			        //parse the ini file to retrieve connection details
			        $config = parse_ini_file("config.ini",true);
                    $host = $config['mysqlConnection']['host'];
                    $dbname = $config['mysqlConnection']['name'];
                    $user = $config['mysqlConnection']['user'];
                    $pass = $config['mysqlConnection']['pass'];
                    //create new PDO object using connection details
                    $db = new PDO("mysql:host=$host;dbname=$dbname",$user,$pass);
                    //we want PDO to throw an informative exception if there is a problem
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    //we are going to use prepared statements for clean coding and to defend against SQL injection
                    $stmt = $db->prepare("select * from users where username = ? and password = ?");
                    $stmt->execute(array($_POST['loginusername'],$_POST['loginpassword']));
                    //lets check that it matches a valid username and password
                    $matchcount = $stmt->rowCount();
                    if ($matchcount == 1) {
                        //OK we have a valid username and password combination
                        //Lets validate the session 
                        $_SESSION['valid'] = true;
                        //Lets set a 5 minute timeout
                        $_SESSION['timeout'] = time() + 5*60;
                        //Lets copy the username to the session
                        $_SESSION['username'] = $_POST['loginusername'];
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            //Lets add the first and last name of the user so we can personalise their welcome
                            $_SESSION['firstname'] = $row['firstname'];
                            $_SESSION['lastname'] = $row['lastname'];
                            //Lets add the last login time so we can let them know when they last logged in
                            $_SESSION['lastlogindatetime'] = $row['lastlogindatetime'];
                        }
                        //Now we update the last login time and date in the database for this user
                        //We use the existing handle on the database with a new prepared statement 
                        $stmt = $db->prepare("UPDATE `users` SET `lastlogindatetime`= now() WHERE `username`= ? ");
                        $stmt->execute(array($_POST['loginusername']));
                        $stmt->closeCursor;
                        header("Location: signedin.php");
                        //echo 'You have entered valid username and password';
                    } else {
                        $msg = 'Wrong username or password';
                    }
                } 
                catch (PDOException $e) {
                    printf("We have a problem: %s\n ", $e->getMessage());
                }
        
           ?>

<html lang = "en">
   
   <head>
      <title>MySQL / PHP Login Page</title>
      <link href = "../css/bootstrap.min.css" rel = "stylesheet">
      <link href = "../css/login.css" rel = "stylesheet">

   </head>
	
   <body>
      
     
      <h2>Enter Username and Password</h2> 
      <div class = "container form-signin">
         
         
      </div> <!-- /container -->
      
      <div class = "container">
      
         <form class = "form-signin" role = "form" 
            action = "<?php echo htmlspecialchars($_SERVER['PHP_SELF']); 
            ?>" method = "post">
            <h4 class = "form-signin-heading"><?php echo $msg; ?></h4>
            <input type = "text" class = "form-control" 
               name = "loginusername" placeholder = "loginusername = user1" 
               required autofocus></br>
            <input type = "password" class = "form-control"
               name = "loginpassword" placeholder = "loginpassword = Password1" required>
            <button class = "btn btn-lg btn-primary btn-block" type = "submit" 
               name = "login">Login</button>
         </form>
			
      </div> 
      
   </body>
</html>