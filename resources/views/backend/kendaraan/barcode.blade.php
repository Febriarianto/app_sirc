<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Barcode</title>
</head>

<body onload="window.print()">
    <table width="100%" style="text-align: center;" border="0">
        <tr>
            <td>No Kendaraan</td>
            <td>Barcode</td>
        </tr>
        @foreach($arr as $d)
        <tr>
            <td>{{$d['no_pol']}}</td>
            <td style="padding: 10px;"><img src="data:image/png;base64,{{ $d['barcode'] }}" width="300px" height="50px"></td>
        </tr>
        @endforeach
    </table>
</body>

</html>