<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Edit {{video}}</title>
        <!--default head settings-->
        {{>head}}
        <script type="text/JavaScript">
            function checkVidVsSeries(){
                var url='editVideo.php?id={{videoId}}&ajax=true&req=checkSeries';
                url+='&seriesId='+$('#seriesSelect').val();
                $.getJSON(url, '', function(data,status,xhr){
                   $("#Season").val(data['Season']);
                   $("#Episode").val(data['Episode']);
                   $("#Date").val(data['Date']);
                   $("#Details").val(data['Details']);
                   $("#Name").val(data['Name']);
                });
            }
        </script>
    </head>
    <body>
        <!--default top navigation-->
        {{>nav}}
        {{#hasError}}
        <h3 style="color:red;">{{errorMessage}}</h3>
        {{/hasError}}
        <form action="editVideo.php" method="POST">
            <input type="hidden" name="id" value="{{videoId}}" />
            <table border="1" width="100%">
                <tr>
                    <th>Series:</th>
                    <td>
                        <select name="series" id="seriesSelect" 
                                onChange="$('#lookupSeries').attr('disabled',false);">
                        {{#series}}
                            <option value="{{seriesId}}"
                                {{#seriesSelected}}
                                selected="selcted"
                                {{/seriesSelected}}>{{seriesName}}</option>
                        {{/series}}
                    </select>
                </td>
                <td>
                    <input type="button" name="lookupSeries" id="lookupSeries" value="Check Filename" 
                           disabled="disabled" onClick="checkVidVsSeries();" />
                </td>
            </tr>
            <tr>
                <th>Name:</th>
                <td><input type="text" name="name" value="{{episodeName}}" id="Name" /></td>
            </tr>
            <tr>
                <th>Season:</th>
                <td><input type="text" name="season" value="{{season}}" id="Season" /></td>
            </tr>
            <tr>
                <th>Episode:</th>
                <td><input type="text" name="episode" value="{{episodeNumber}}" id="Episode" /></td>
            </tr>
            <tr>
                <th>Air Date:</th>
                <td><input type="text" name="date" value="{{airDate}}" id="Date" /></td>
            </tr>
            <tr>
                <th>Video Details:</th>
                <td><textarea name="details" id="Details" rows="3" cols="60">{{episodeDetails}}</textarea></td>
            </tr>
            <tr>
                <th>Notes:</th>
                <td><textarea name="notes" id="notes" rows="3" cols="60">{{notes}}</textarea></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="Save" />
                </td>
            </tr>
        </table>
        </form>
    </body>
</html>