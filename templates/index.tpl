<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        {{>head}}
    </head>
    <body>
        <h2>Log In</h2>
        {{#hasError}}
        <h3 style="color:red;">{{errorMessage}}</h3>
        {{/hasError}}
        <form action="index.php" method="post">
            Log in: <input type="text" name="login" /><br />
            Password: <input type="password" name="pass" /><br />
            <a href="register.php">Register</a><br />
            <input type="submit" value="Log in" />
        </form>
    </body>
</html>
