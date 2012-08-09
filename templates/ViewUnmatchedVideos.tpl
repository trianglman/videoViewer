<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>List Unmatched Videos</title>
        <!--default head settings-->
        {{>head}}
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <table border="1" width="100%">
        {{#videos}}
            <tr>
                <td>{{filename}}</td>
                <td>{{notes}}</td>
                <td>
                    <a href="viewVideoDetails.php?id={{videoId}}">Edit Video Details</a>
                </td>
            </tr>
        {{/videos}}
        </table>
    </body>
</html>
