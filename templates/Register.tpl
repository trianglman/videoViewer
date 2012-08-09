<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Register for Video Viewer</title>
        <!--default head settings-->
        {{>head}}
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <h2>Register</h2>
        {{#hasError}}
        <strong style="color:red;">{{errorMessage}}</strong>
        {{/hasError}}
        <form action="register.php" method="post">
            Name: <input type="text" name="name" /><br />
            Log in: <input type="text" name="login" /><br />
            Password: <input type="password" name="pass" /><br />
            Re-enter Password: <input type="password" name="pass2" /><br />
            <a href="register.php">Register</a><br />
            <input type="submit" value="Register" />
        </form>
    </body>
</html>
