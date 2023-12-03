<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body onload="window.print()">
    <table border="0" style="padding: 0; margin: 0;">
        <tr>
            <td style="text-align: center; width: 16%;"><img src="{{ asset ('assets/dist/img/tutwuri.png') }}" alt="" width="80px" height="80px">
            </td>
            <td style="text-align: center; width: 44%;">
                <h2 style="font-size: 28px; line-height: 0;">CV. ANDRA PRATAMA</h2>
                <p style=" font-size: 12px;">Jl. Melati III Linkungan IV No. 023 Rt.06 Rw.04 Pringsewu Timur <br>
                    Kec. Pringsewu - Lampung 35373 Telp. (0729) 7081967<br>
                    Contact Person : 0811 70 9009 / 0853 8023 3151
                </p>
            </td>
            <td style="text-align: center;"><img src="{{ asset ('assets/dist/img/concept.png') }}" alt="" width="120px" height="120px"></td>
            <td style="text-align: center;">
                <div style="background-color: black; color: white; padding: 3px; margin-bottom: 2px; border-radius: 5px 5px 5px 5px;">
                    BUKTI PEMBAYARAN
                </div>
                <div style="margin-left: 20px; margin-right: 20px; margin-bottom: 20px;">
                    <div style="border: solid black 1px;"> NO NOTA</div>
                    <div style="border: solid black 1px;"> {{ $invoice->id }}</div>
                </div>
                <div style="background-color: black; color: white; padding: 3px; margin-bottom: 2px; border-radius: 5px 5px 5px 5px;">
                    Rincian Biaya</div>
            </td>
        </tr>
        <tr>
            <td>Nama Penyewa</td>
            <td>: {{ $invoice->penyewa->nama }}</td>
            <td>Biaya</td>
            <td>: Rp. {{ number_format($invoice->biaya) }}</td>
        </tr>
        <tr>
            <td>NO. HP</td>
            <td>: {{ $invoice->penyewa->no_hp }}</td>
            <td>Over Time</td>
            <td>: Rp. {{ number_format($invoice->over_time) }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $invoice->penyewa->alamat }}</td>
            <td>DP</td>
            <td style="border-bottom: solid black 2px;">: Rp. {{ number_format($invoice->dp) }}</td>
        </tr>
        <tr>
            <td>Tujuan Kota</td>
            <td>: {{ $invoice->kota_tujuan }}</td>
            <td>Sisa</td>
            <td>: Rp. {{ number_format($invoice->sisa) }}</td>
        </tr>
        <tr>
            <td>Kendaraan</td>
            <td>: {{ $invoice->kendaraan->jenis->nama }} No.Pol : {{ $invoice->kendaraan->no_kendaraan }}</td>
        </tr>
        <tr>
            <td>Lama Sewa</td>
            <td>: {{ $invoice->lama_sewa }}</td>
            <td colspan="2" style="text-align: center;">Pringsewu, {{ date('d-m-Y'); }}</td>
        </tr>
        <tr>
            <td>Hari/Tanggal Berangkat </td>
            <td>: {{ strftime('%d %B %Y', strtotime($invoice->keberangkatan)) }} / Jam :{{ date('H:i:s', strtotime($invoice->keberangkatan)) }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Hari/Tanggal Pulang </td>
            <td>: {{ strftime('%d %B %Y', strtotime($invoice->kepulangan)) }} / Jam :{{ date('H:i:s', strtotime($invoice->kepulangan)) }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td colspan="2" style="text-align: center;">{{ Auth()->user()->name }}</td>
        </tr>
    </table>
</body>

</html>