<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>{{seriesName}} Season Listing</title>
        {{>head}}
    </head>
    <body>
        {{>nav}}
        <h1>{{seriesName}}</h1>
        <img src="{{bannerURL}}" alt="{{seriesName}}" /><br />
        <p>{{seriesDesc}}</p>
        <h3>Seasons available:</h3>
        <ul>
            {{#seasons}}
            <li><a href="seriesSeasonEpisodes.php?series={{seriesId}}&season={{seasonNumber}}">
                Season {{seasonNumber}}</a> ({{epCount}} episodes available)</li>
            {{/seasons}}
        </ul>
    </body>
</html>
