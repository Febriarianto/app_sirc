<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        tr.border_bottom td {
            border-bottom: 2px solid black;
        }
    </style>
</head>

<body onload="window.print()">
    <table border="0" width="100%">
        <tr class="border_bottom">
            <td style=" text-align: center;"><img src="{{ asset ('assets/dist/img/tutwuri.png') }}" alt="" width="80px" height="80px">
            </td>
            <td style="text-align: center;">
                <h2 style="font-size: 28px; line-height: 0;">CV. ANDRA PRATAMA</h2>
                <p style=" font-size: 12px;">Jl. Melati III Linkungan IV No. 023 Rt.06 Rw.04 Pringsewu Timur <br>
                    Kec. Pringsewu - Lampung 35373 Telp. (0729) 7081967<br>
                    Contact Person : 0811 70 9009 / 0853 8023 3151
                </p>
            </td>
            <td style="text-align: center;"><img src="{{ asset ('assets/dist/img/concept.png') }}" alt="" width="80px" height="120px"></td>
        </tr>
        <tr class="border_bottom">
            <td colspan="3" style="text-align: center;">INVOICE SEWA MOBIL</td>
        </tr>
        <tr>
            <td>No Kwitansi</td>
            <td colspan="2">: {{ $invoice->id }}</td>
        </tr>
        <tr>
            <td>Diterima dari</td>
            <td colspan="2">: {{ $invoice->penyewa->nama }}</td>
        </tr>
        <tr>
            <td>Uang Sejumlah</td>
            <td colspan="2">: Rp. {{ number_format($invoice->sisa) }}</td>
        </tr>
        <tr>
            <td>Untuk Pembayaran</td>
            <td colspan="2">: Sewa Mobil</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2"> - {{ $invoice->kendaraan->jenis->nama }} No.Pol : {{ $invoice->kendaraan->no_kendaraan }}</td>
        </tr>
        <tr>
            <td>Lama Sewa</td>
            <td colspan="2">: {{ $invoice->lama_sewa }}</td>
        </tr>
        <tr>
            <td>Total Harga</td>
            <td colspan="2">: Rp. {{ number_format($invoice->biaya) }}</td>
        </tr>
        <tr class="border_bottom">
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <td>Pringsewu, {{ date('d-m-Y'); }}
                <br>
                <br>
                <br>
                <br>
                <br>
                {{ Auth()->user()->name }}
            </td>
        </tr>
    </table>
</body>

</html>