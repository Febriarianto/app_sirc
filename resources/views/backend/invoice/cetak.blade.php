<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <table border="0" style="height: 907.08661417px; width: 1058.2677165px; padding: 0; margin: 0;">
        <tr>
            <td style="text-align: center; width: 16%;"><img src="{{ public_path ('assets/dist/img/tutwuri.png') }}" alt="" width="80px" height="80px">
            </td>
            <td style="text-align: center; width: 44%;">
                <h2 style="font-size: 28px; line-height: 0;">CV. ANDRA PRATAMA</h2>
                <p style=" font-size: 12px;">Jl. Melati III Linkungan IV No. 023 Rt.06 Rw.04 Pringsewu Timur <br>
                    Kec. Pringsewu - Lampung 35373 Telp. (0729) 7081967<br>
                    Contact Person : 0811 70 9009 / 0853 8023 3151
                </p>
            </td>
            <td style="text-align: center;"><img src="{{ public_path ('assets/dist/img/concept.png') }}" alt="" width="120px" height="120px"></td>
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
            <td>: {{ $invoice->transaksi->penyewa->nama }}</td>
            <td colspan="2" style="text-align: center;"><img src="data:image/png;base64,{{ $barcode }}"></td>
        </tr>
        <tr>
            <td>NO. HP</td>
            <td>: {{ $invoice->transaksi->penyewa->no_hp }}</td>
            <td>Biaya</td>
            <td>: Rp. {{ number_format($invoice->biaya) }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $invoice->transaksi->penyewa->alamat }}</td>
            <td>DP</td>
            <td style="border-bottom: solid black 2px;">: Rp. {{ number_format($invoice->transaksi->dp) }}</td>
        </tr>
        <tr>
            <td>Tujuan Kota</td>
            <td>: {{ $invoice->transaksi->kota_tujuan }}</td>
            <td>Sisa</td>
            <td>: </td>
        </tr>
        <tr>
            <td>Kendaraan</td>
            <td>: {{ $invoice->transaksi->kendaraan->jenis->nama }} No.Pol : {{ $invoice->transaksi->kendaraan->no_kendaraan }}</td>
        </tr>
        <tr>
            <td>Lama Sewa</td>
            <td>: {{ $invoice->transaksi->lama_sewa }}</td>
            <td>Kondisi BBM</td>
            <td>: .....................................................</td>
        </tr>
        <tr>
            <td>Hari/Tanggal Berangkat </td>
            <td>: {{ strftime('%d %B %Y', strtotime($invoice->transaksi->keberangkatan)) }} / Jam :{{ date('H:i:s', strtotime($invoice->transaksi->keberangkatan)) }}</td>
            <td>Dongkrak</td>
            <td>: .....................................................</td>
        </tr>
        <tr>
            <td>Hari/Tanggal Pulang </td>
            <td>: {{ strftime('%d %B %Y', strtotime($invoice->transaksi->kepulangan)) }} / Jam :{{ date('H:i:s', strtotime($invoice->transaksi->kepulangan)) }}</td>
            <td>Ban Cadangan</td>
            <td>: .....................................................</td>
        </tr>
        <tr>
            <td>Over Time</td>
            <td>: {{ $invoice->transaksi->biaya_overtime }}</td>
            <td>Kelengkapan</td>
            <td>: </td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Jaminan:</td>
            <td style="text-align: end;">CEK FISIK :</td>
            <td colspan="2" rowspan="3" style="text-align: center;;"><img src="{{ public_path ('assets/dist/img/car-view.png')}}" alt="" width="190px" height="80px"></td>
        </tr>
        <tr>
            <td style="padding-left: 10px;" colspan="2">
                <div style="border: solid black 2px; display: inline-block; padding: 5px;"></div> E-KTP
                <div style="border: solid black 2px; display: inline-block; padding: 5px; margin-left: 10px;"></div>
                KK
                <div style="border: solid black 2px; display: inline-block; padding: 5px; margin-left: 10px;"></div>
                MOTOR
            </td>
        </tr>
        <tr>
            <td style="padding-left: 10px;" colspan="2">
                <div style="border: solid black 2px; display: inline-block; padding: 5px;"></div> SIM
                <div style="border: solid black 2px; display: inline-block; padding: 5px; margin-left: 10px;"></div>
                KTA
                <div style="border: solid black 2px; display: inline-block; padding: 5px; margin-left: 10px;"></div>
                STNK
            </td>
        </tr>
        <tr>
            <td colspan="2" rowspan="5">
                <p>PENTING, DIKETAHUI KETENTUAN-KETENTUAN DI BAWAH INI:</p>
                <ol style="font-size: 14px;">
                    <li>Kendaraan (Mobil) yang tersebut di atas (yang disewakan) tidak dapat dipindah tangankan
                        kepada
                        pihak lain / kedua tanpa seizin pemilik kendaraan (Mobil)</li>
                    <li>Kendaraan (Mobil) tersebut diatas tidak dapat dijadikan jaminan / digadaikan dengan tujuan
                        apapun kepada siapapun</li>
                    <li>Pelanggaran No. 1 & 2 akan diproses memalui jalur pidana dan pemilik kendaraan (Mobil)
                        berhak
                        untuk mengambil kembali kendaraan (Mobil) apabila terjadi pelanggaran No. 1 & 2 atau
                        terdapat
                        kejanggalan lainnya mengenai pemakaian kendaraan (Mobil) dimana hal ini dirasakan oleh
                        pemilik
                        kendaraan (Mobil)</li>
                    <li>
                        Pengambalian kendaraan (Mobil) harus dalam keadaan seperti pada saat di tanda tanganinya
                        surat
                        tanda terima ini, jika terjadi tabrakan / kerusakan adalah tanggung jawab penyewa
                    </li>
                    <li>Jika ada keterlambatan pengembalian kendaraan (Mobil) akan dikenakan denda perjam sebesar
                        Rp.20.000 dst</li>
                    <li>
                        Keterangan Body .......................................................................
                    </li>
                </ol>
            </td>
            <td colspan="2" style="text-align: justify; line-height: 0%;">
                BBM : E <div style="border: solid black 2px; display: inline-block; padding: 10px; margin: 0;"></div>
                <div style="border: solid black 2px; display: inline-block; padding: 10px; margin: 0;"></div>
                <div style="border: solid black 2px; display: inline-block; padding: 10px; margin: 0;"></div>
                <div style="border: solid black 2px; display: inline-block; padding: 10px; margin: 0;"></div>
                <div style="border: solid black 2px; display: inline-block; padding: 10px; margin: 0;"></div>
                <div style="border: solid black 2px; display: inline-block; padding: 10px; margin: 0;"></div>
                <div style="border: solid black 2px; display: inline-block; padding: 10px; margin: 0;"></div> F

            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">
                Pringsewu , {{ strftime('%d %B %Y', strtotime($invoice->transaksi->keberangkatan)) }}
            </td>
        </tr>
        <tr>
            <td style="text-align: center; line-height: 1;">Pemilik Kendaraan Atau Yang diberi Kuasa</td>
            <td style="text-align: center;">Penyewa Kendaraan menyetujui ketentuan tersebut diatas</td>
        </tr>
        <tr>
            <td colspan="2"><br><br><br><br></td>
        </tr>
        <tr style="text-align: center;">
            <td>.........................</td>
            <td>{{ $invoice->transaksi->penyewa->nama }}</td>
        </tr>
    </table>
    <script src="{{ asset ('assets/dist/js/JsBarcode.all.min.js') }}"></script>
    <script>
        JsBarcode("#code128", "Hi!");
    </script>

</body>

</html>