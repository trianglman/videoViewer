<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Available Series</title>
        <!--default head settings-->
        {{>head}}
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <ul>
        {{#seriesOpt}}
            {{#unmatched}}
            <li><a href="viewUnmatchedVideos.php">Unmatched videos</a></li>
            {{/unmatched}}
            {{^unmatched}}
            <li>
                <a href="seriesSeasonListing.php?series={{seriesId}}">{{seriesName}}</a> 
                (<a href="grantAccess.php?series={{seriesId}}">Grant Access</a>)
            </li>
            {{/unmatched}}
        {{/seriesOpt}}
        </ul>
    </body>
</html>
