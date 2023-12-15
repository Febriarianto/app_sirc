<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @page {
            size: 58mm 100mm
        }

        /* output size */
        body.receipt .sheet {
            font-size: 8px;
            width: 58mm;
            height: 100mm
        }

        /* sheet size */
        @media print {
            body.receipt {
                width: 58mm
            }
        }

        /* fix for Chrome */
        tr.border_bottom td {
            border-bottom: 2px solid black;
        }

        .print {
            page-break-after: always;

        }
    </style>
</head>

<body onload="window.print()">
    <table border="0" style="font-size: 10px;">
        <tr class="border_bottom">
            <td style="text-align: center;"><img src="{{ asset ('assets/dist/img/logo.png') }}" alt="" width="25px" height="25px">
            </td>
            <td style="text-align: center;">
                <h3 style="">CV. ANDRA PRATAMA</h3>
            </td>
            <td style="text-align: center;"><img src="{{ asset ('assets/dist/img/concept.png') }}" alt="" width="40px" height="55px"></td>
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
            <td colspan="2"> - {{ $invoice->kendaraan->jenis->nama }} - No.Pol : {{ $invoice->kendaraan->no_kendaraan }}</td>
        </tr>
        <tr>
            <td>Lama Sewa</td>
            <td colspan="2">: {{ date('d-m-Y', strtotime($invoice->keberangkatan)) . " s/d " . date('d-m-Y', strtotime($invoice->kepulangan)) }}</td>
        </tr>
        <tr>
            <td>Paket</td>
            <td colspan="2">: {{ $invoice->paket }}</td>
        </tr>
        <tr>
            <td>Total Harga</td>
            <td colspan="2">: Rp. {{ number_format($invoice->biaya) }}</td>
        </tr>
        <tr class="border_bottom">
            <td colspan="3"></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" style="text-align: center;">Pringsewu, {{ date('d-m-Y'); }}
                <br>
                <br>
                <br>
                {{ Auth()->user()->name }}
            </td>
        </tr>
    </table>
</body>

</html>