<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>User Video Listing</title>
        <!--default head settings-->
        {{>head}}
        <script type="text/javascript" language="JavaScript">
            function addVideo(vidId)
            {
                $("#episodeInput").val(vidId);
                $("#episodeForm").submit();
            }
        </script>
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        {{#hasVideos}}
        <dl>
            <dt>Unwatched videos</dt>
            {{#videos}}
            <dd>
                {{seriesName}}: 
                <a href="viewEpisode.php?episode={{videoId}}">
                {{episodeName}}</a> (Aired: {{airDate}})
                &mdash; <a href="javascript:addVideo({{videoId}})">Mark watched</a>
            </dd>
            {{/videos}}
        </dl>
        {{/hasVideos}}
        {{^hasVideos}}
        <h3>You have no unwatched videos in your queue.</h3>
        {{/hasVideos}}
        <form action="userHistory.php" method="POST" id="episodeForm">
            <input type="hidden" name="action" value="add" />
            <input type="hidden" name="episode" id="episodeInput" value="" />
        </form>
    </body>
</html>
