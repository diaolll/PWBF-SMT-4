<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label Barang TnJ 108</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            margin: 0;
            width: 210mm;
            height: 167mm;
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: separate;
            border-spacing: 2mm 2mm;
            margin: 0 auto;
        }

        td {
            width: 38mm;
            height: 18mm;
            border: 0.1pt solid #ccc;
            vertical-align: middle;
            text-align: center;
            box-sizing: border-box;
            overflow: hidden;
            background-color: white;
            border-radius: 5px;
            padding: 1px 2px;
        }

        .empty-cell {
            border: 0.1pt solid #f2f2f2;
        }

        .nama {
            font-size: 6pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 1px;
            word-wrap: break-word;
        }

        .harga {
            font-size: 8pt;
            font-weight: bold;
            display: block;
        }

        .barcode img {
            width: 90%;
            height: 14px;
            display: block;
            margin: 1px auto;
        }

        .id {
            font-size: 5.5pt;
            color: #666;
            text-transform: uppercase;
            line-height: 1;
        }
    </style>
</head>
<body>

    <table>
        @php $itemIndex = 0; @endphp

        @for ($row = 0; $row < 8; $row++)
            <tr>
                @for ($col = 0; $col < 5; $col++)
                    @php $currentCell = ($row * 5) + $col; @endphp

                    @if ($currentCell >= $skipCount && $itemIndex < count($selectedBarang))
                        @php $b = $selectedBarang[$itemIndex]; @endphp
                        <td>
                            @if(isset($barcodes[$b->id_barang]))
                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ $barcodes[$b->id_barang] }}">
                                </div>
                            @endif

                            <div class="nama">{{ $b->nama }}</div>
                            <div class="harga">Rp {{ number_format($b->harga, 0, ',', '.') }}</div>
                            <div class="id">{{ $b->id_barang }}</div>

                            @php $itemIndex++; @endphp
                        </td>
                    @else
                        <td class="empty-cell">&nbsp;</td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>

</body>
</html>