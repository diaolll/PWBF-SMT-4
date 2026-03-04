<!DOCTYPE html>
<html>
<head>
    <title>Cetak Tag Harga TnJ 108</title>
    <style>
        @page { 
            size: 210mm 165mm;
            margin: 0; 
        }
        
        body { 
            margin: 0; 
            padding: 0;
            width: 210mm;
            height: 165mm;
            /* Kalibrasi Sketsa: Atas 0,2cm (2mm) | Pinggir 0,3cm (3mm) */
            padding-top: 2mm; 
            padding-left: 3mm;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #ffffff;
        }

        table {
            border-collapse: separate; 
            /* Jarak antar stiker (area kuning di foto): 2mm horizontal, 2mm vertikal */
            border-spacing: 2mm 2mm; 
            table-layout: fixed;
            width: auto;
        }

        td {
            /* Ukuran Label: 38mm x 18mm */
            width: 38mm;    
            height: 18mm; 
            padding: 1mm;
            box-sizing: border-box;
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
        }

        /* Label terisi: Opacity 100% dengan border tegas */
        .filled-label {
            border: 0.4pt solid #333;
            opacity: 1.0;
            background-color: #fff;
        }

        /* Label kosong: Opacity 50% untuk panduan posisi saja */
        .empty-grid {
            border: 0.1pt solid #b3b3b3;
            opacity: 0.4;
        }

        .id { font-size: 6px; color: #888; display: block; margin-bottom: 1px; }
        .nama { 
            font-size: 8px; 
            font-weight: bold; 
            display: block; 
            line-height: 1;
            max-height: 16px; /* Mencegah teks nama terlalu panjang merusak layout */
            overflow: hidden;
        }
        .harga { font-size: 10px; font-weight: bold; color: #000; display: block; margin-top: 2px; }
    </style>
</head>
<body>
    <table>
        @php
            $totalCells = $skipCount + count($selectedBarang);
            $currentCell = 0;
        @endphp

        {{-- Looping 8 Baris x 5 Kolom sesuai layout TnJ 108 --}}
        @for ($row = 0; $row < 8; $row++)
            <tr>
                @for ($col = 0; $col < 5; $col++)
                    @php $currentCell++; @endphp
                    
                    @if ($currentCell <= $skipCount)
                        <td class="empty-grid"></td>
                    @elseif ($currentCell <= $totalCells)
                        @php $item = $selectedBarang[$currentCell - $skipCount - 1]; @endphp
                        <td class="filled-label">
                            <span class="id">{{ $item->id_barang }}</span>
                            <span class="nama">{{ Str::limit($item->nama, 30) }}</span>
                            <span class="harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                        </td>
                    @else
                        <td class="empty-grid"></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
</body>
</html>