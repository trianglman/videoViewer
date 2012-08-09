<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{{episodeName}} - {{seriesName}}</title>
        <!--default head settings-->
        {{>head}}
        <script type="text/javascript" src="http://html5.kaltura.org/js" > </script> 
        <style type="text/css">
            .videoContainer{
                postion:relative;
                left:0;right:0;
                margin:0 auto;
                width:700px;
            }
        </style>
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <div class="videoContainer">
            <video id="video" style="width:700px;"
                   poster="http://www.ipoots.com/static/images/video-placeholder-big.jpg">
                <source type="video/ogg" src="{{oggPath}}" />
                <source type="video/h264" src="{{mp4Path}}" />
            </video>
        </div>
        {{#isAdmin}}
        <br clear="all" />
        <a href="viewVideoDetails.php?id={{videoId}}">View Details</a>
        {{/isAdmin}}
    </body>
</html>
