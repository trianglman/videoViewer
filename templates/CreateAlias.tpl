<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Edit {{video}}</title>
        {{>head}}
        
        <style>
            .manualEntry{
                color:#000;
            }
            .suggestedEntry{
                color:#666;
            }
        </style>
        <script type="text/javascript" language="JavaScript">
            function removeSuggestion(formField){
                $(formField).removeClass('suggestedEntry')
                            .addClass('manualEntry')
                            .val('')
                            .unbind('focus');
            }
        </script>
    </head>
    <body>
        {{>nav}}
        {{#error}}<strong style="color:red;">{{error}}</strong>{{/error}}
        <form action="createAlias.php" method="POST">
            <input type="hidden" name="videoId" value="{{videoId}}" />
            <table border="1" width="100%">
                <tr>
                    <th>Series:</th>
                    <td>
                        <select name="series" id="seriesSelect">
                            {{#series}}
                            <option value="{{seriesId}}" {{{selected}}}>
                                {{seriesName}}
                            </option>
                            {{/series}}
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Alias:</th>
                    <td>
                        <input type="text" name="alias" value="{{defaultAlias}}"
                               {{#seriesHasAlias}}
                               class="manualEntry"
                               {{/seriesHasAlias}}
                               {{^seriesHasAlias}}
                               class="suggestedEntry"
                               onfocus="removeSuggestion(this)"
                               {{/seriesHasAlias}}
                               />
                    </td>
                </tr>
            </table>
            <input type="submit" value="Save" />
        </form>
        
    </body>
</html>
