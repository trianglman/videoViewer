<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>View Video Details - {{video}}</title>
        <!--default head settings-->
        {{>head}}
        <script type="text/javascript" src="http://html5.kaltura.org/js" > </script> 
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <table border="1" width="100%">
            <tr>
                <td colspan="2" align="center">
                    <video id="video" style="width:640px;height:360px;"
                           poster="http://www.ipoots.com/static/images/video-placeholder-big.jpg">
                        <source type="video/ogg" src="{{oggPath}}" />
                        <source type="video/h264" src="{{mp4Path}}" />
                    </video>
                </td>
            </tr>
            <tr>
                <th>Series:</th>
                <td>{{seriesName}}</td>
            </tr>
            <tr>
                <th>Season:</th>
                <td>{{season}}</td>
            </tr>
            <tr>
                <th>Episode:</th>
                <td>{{episodeNumber}}</td>
            </tr>
            <tr>
                <th>Air Date:</th>
                <td>{{airDate}}</td>
            </tr>
            <tr>
                <th>Video Details:</th>
                <td>{{details}}</td>
            </tr>
            <tr>
                <th>Notes:</th>
                <td>{{notes}}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="display:block;float:left;text-align: center;width:33%;">
                        <a href="createSeries.php?videoId={{videoId}}">Create a Series</a>
                    </div>
                    <div style="display:block;float:left;text-align: center;width:33%;">
                        <a href="createAlias.php?videoId={{videoId}}">Create an Alias</a>
                    </div>
                    <div style="display:block;float:left;text-align: center;width:33%;">
                        <a href="editVideo.php?id={{videoId}}">Edit video</a>
                    </div>
                </td>
            </tr>
        </table>
    </body>
</html>
