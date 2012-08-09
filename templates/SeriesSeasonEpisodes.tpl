<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{{seriesName}} Season {{selectedSeason}}</title>
        <!--default head settings-->
        {{>head}}
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        <h1>{{seriesName}}</h1>
        <img src="{{seriesImage}}" alt="{{seriesName}}" /><br />
        <ul>
        {{#episodes}}
            <li>{{episodeNumber}}: 
                <a href="viewEpisode.php?episode={{videoId}}">{{episodeName}}</a> 
                (Aired: {{airDate}})
            </li>
        {{/episodes}}
        </ul>
    </body>
</html>
