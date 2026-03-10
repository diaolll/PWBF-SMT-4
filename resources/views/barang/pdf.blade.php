<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Tag Harga TnJ 108</title>
    <style>

        @page {
            size: A4 portrait;
            margin: 10.65mm; 
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7pt;
            background-color: #fff; 
            padding: 0;
        }

        table.label-sheet {
            width: 100%;
            border-collapse: separate;
            /* Jarak antar stiker: 2mm horizontal, 3mm vertikal */
            border-spacing: 2mm 3mm; 
            table-layout: fixed;
            margin: 0 auto;
        }

        /* Lebar per kolom: 38mm */
        .label-col { width: 38mm; }

        table.label-sheet td {
            height: 18mm;    
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
            border: 0.5pt solid #e0e0e0; 
            background-color: #ffffff; 
            border-radius: 1mm;
        }

        /* Style untuk cell terisi */
        table.label-sheet td.label-cell {
            padding: 1mm 1mm;
        }

        /* Style untuk cell kosong */
        table.label-sheet td.empty {
            border: 0.3pt dashed #e0e0e0; /* Garis putus-putus untuk slot kosong */
            background-color: #fafafa;
        }

        .label-id {
            font-size: 5.5pt;
            color: #555;
            letter-spacing: 0.5px;
            margin-bottom: 0.5mm;
            text-transform: uppercase;
        }

        .label-name {
            font-size: 7pt;
            font-weight: bold;
            color: #222;
            margin-bottom: 1mm;
            line-height: 1.1;
            overflow: hidden;
            display: block;
        }

        .label-price {
            font-size: 9pt;
            font-weight: bold;
            color: #2bc03a;
            letter-spacing: 0.3px;
        }

        .label-price-label {
            font-size: 5pt;
            color: #999;
            display: block;
            margin-top: 0.3mm;
            font-weight: normal;
        }
    </style>
</head>
<body>

    <table class="label-sheet">
        <colgroup>
            @for($c = 0; $c < 5; $c++)
                <col class="label-col">
            @endfor
        </colgroup>

        @php
            $totalCells = $skipCount + count($selectedBarang);
            $currentCell = 0;
        @endphp

        @for ($r = 0; $r < 8; $r++)
        <tr>
            @for ($c = 0; $c < 5; $c++)
                @php $currentCell++; @endphp
                
                @if ($currentCell <= $skipCount)
                    {{-- Slot yang dilewati --}}
                    <td class="empty"></td>
                @elseif ($currentCell <= $totalCells)
                    {{-- Slot yang berisi barang --}}
                    @php $item = $selectedBarang[$currentCell - $skipCount - 1]; @endphp
                    <td class="label-cell">
                        <div class="label-id">{{ $item->id_barang }}</div>
                        <div class="label-name">{{ $item->nama }}</div>
                        <div class="label-price">
                            Rp {{ number_format($item->harga, 0, ',', '.') }}
                            <span class="label-price-label">HARGA</span>
                        </div>
                    </td>
                @else
                    {{-- Slot sisa yang kosong --}}
                    <td class="empty"></td>
                @endif
            @endfor
        </tr>
        @endfor
    </table>

</body>
</html>