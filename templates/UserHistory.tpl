<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>User History</title>
        <!--default head settings-->
        {{>head}}
        <script type="text/javascript" language="JavaScript">
            function deleteVideo(vidId)
            {
                $("#episodeInput").val(vidId);
                $("#episodeForm").submit();
            }
        </script>
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <div class="watchedVidList">
            <h3>Watched videos</h3>
            {{#watched}}
            <p>
                {{seriesName}} 
                <a href="viewEpisode.php?episode={{videoId}}">
                    {{episodeName}}: {{season}} - {{episode}}</a>
                (<a href="javascript:deleteVideo({{videoId}})">
                Remove from history</a>)
            </p>
            {{/watched}}
            {{^watched}}
            No recently watched videos
            {{/watched}}
        </div>
        <form action="userHistory.php" method="POST" id="episodeForm">
            <input type="hidden" name="action" value="del" />
            <input type="hidden" name="episode" id="episodeInput" value="" />
        </form>
    </body>
</html>
