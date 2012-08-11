<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Create New Series</title>
        <!--default head settings-->
        {{>head}}
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <h3>The TVDB suggests these series for that video:</h3>
        <ul>
            {{#seriesOpt}}
            <li>
                <img src="temp/{{seriesId}}.jpg" /><br clear="all" />
                <a href="{{{TVDBUrl}}}" target="_BLANK">{{name}}</a><br />
                <a href="javascript:$('#seriesId').val({{seriesId}});
                                    $('#hiddenform').submit();">Select</a>
            </li>
            {{/seriesOpt}}
        </ul>
        <form method="POST" id="hiddenform" action="createSeries.php">
            <input type="hidden" name="seriesId" id="seriesId" value="" />
            <input type="hidden" name="videoId" id="videoId" value="{{videoId}}" />
        </form>
    </body>
</html>
