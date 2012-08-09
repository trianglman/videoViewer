<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Welcome {{name}}</title>
        {{>head}}
    </head>
    <body>
        {{>nav}}
        <div class="authSeriesList">
            <h3>Available Series</h3>
            {{#series}}
                <a href="seriesSeasonListing.php?series={{seriesId}}">{{seriesName}}</a>
                ({{unwatchedCount}} unwatched episode{{pluralized}})
                <br />
            {{/series}}
            {{#noSeries}}
            No available series
            {{/noSeries}}
        </div>
        {{#admin}}
        <p><a href="listAllSeries.php">View all series</a></p>
        {{/admin}}
        {{#hasRoku}}
        <p>Roku XML file: 
            <a href="/videoViewer{{rokuUrl}}">{{rokuUrl}}</a>
        </p>
        {{/hasRoku}}
    </body>
</html>