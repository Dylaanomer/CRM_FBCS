<html>
  <head>
    <title> Login -  CRM FBCS </title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <link rel="stylesheet" href="css/login.css">
  </head>
  <body>
    <form class="logincontainer">
      <img src="img/fbcsorig.png" alt="FBCS" style="margin-left: 10%; width: 80%;"/>
      <div id="login-title">Log In</div>

      <div id="errormessage"> </div>

      <label for="username"> Gebruiker </label>
      <input id="username" type="text" name="username" autofocus>

      <label for="password"> Wachtwoord </label>
      <input id="password" type="password" name="password"><br/>

      <label for="remember"> Onthoud mij </label>
      <input type="checkbox" id="remember" class="switch"/>

      <button type="submit">Log In</button>

      <!--<div id="toggleState">Register</div>-->
    </form>
  </body>
</html>

///