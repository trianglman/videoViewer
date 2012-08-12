<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{{seriesName}} User Access Listing</title>
        <!--default head settings-->
        {{>head}}
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <h1>{{seriesName}}</h1>
        <img src="{{seriesImage}}" alt="{{seriesName}}" /><br />
        <h3>Users who have access:</h3>
        <form action="grantAccess.php" method="POST">
            <input type="hidden" name="series" value="{{seriesId}}" />
            {{#users}}
            <input type="checkbox" name="authUser[]" id="auth_{{userId}}" 
                    value="{{userId}}"
                    {{#userHasAccess}}
                    checked="checked"
                    {{/userHasAccess}} />
            <label for="auth_{{userId}}">{{userName}}</label><br />
            {{/users}}
        <input type="submit" value="Update" />
        </form>
    </body>
</html>
