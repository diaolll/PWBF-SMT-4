<!DOCTYPE html>
<html>
<head>
    <title>Cetak Tag Harga TnJ 108</title>
    <style>
        @page { 
            size: A4;
            margin: 0; 
        }
        
        body { 
            margin: 0; 
            padding: 0;
            width: 210mm;
            height: 297mm;
            /* Kalibrasi jarak dari tepi kertas ke stiker pertama */
            padding-top: 13.5mm; 
            padding-left: 5.5mm;
            font-family: 'Helvetica', 'Arial', sans-serif;
            background-color: #ffffff;
        }

        table {
            border-collapse: separate; 
            /* Jarak antar stiker (area kuning di kertas) */
            border-spacing: 2mm 1.5mm; 
            table-layout: fixed;
            width: 195mm;
        }

        td {
            width: 38mm;    /* Lebar stiker putih */
            height: 18.5mm; /* Tinggi stiker putih */
            padding: 1.5mm;
            box-sizing: border-box;
            vertical-align: middle;
            text-align: center;
            overflow: hidden;
            /* Border abu-abu tipis sebagai penanda grid */
            border: 0.1pt solid #cccccc; 
            background-color: #ffffff;
        }

        /* Styling teks agar pas di kotak kecil */
        .id { font-size: 7px; color: #777; display: block; margin-bottom: 1px; }
        .nama { 
            font-size: 9px; 
            font-weight: bold; 
            display: block; 
            line-height: 1; 
            max-height: 18px; 
            overflow: hidden; 
        }
        .harga { font-size: 11px; font-weight: bold; color: #000; display: block; margin-top: 2px; }

        /* Sel kosong tetap punya border tapi tanpa teks */
        .empty-grid {
            border: 0.1pt solid #eeeeee; /* Lebih tipis/muda warnanya */
        }
    </style>
</head>
<body>
    <table>
        @php
            $totalCells = $skipCount + count($selectedBarang);
            $currentCell = 0;
        @endphp

        @for ($row = 0; $row < 8; $row++)
            <tr>
                @for ($col = 0; $col < 5; $col++)
                    @php $currentCell++; @endphp
                    
                    @if ($currentCell <= $skipCount)
                        {{-- Kotak yang dilewati (Skip) tetap tampil gridnya --}}
                        <td class="empty-grid"></td>
                    @elseif ($currentCell <= $totalCells)
                        {{-- Kotak yang berisi data barang --}}
                        @php $item = $selectedBarang[$currentCell - $skipCount - 1]; @endphp
                        <td>
                            <span class="id">{{ $item->id_barang }}</span>
                            <span class="nama">{{ Str::limit($item->nama, 25) }}</span>
                            <span class="harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                        </td>
                    @else
                        {{-- Sisa kotak kosong di baris terakhir tetap tampil gridnya --}}
                        <td class="empty-grid"></td>
                    @endif
                @endfor
            </tr>
        @endfor
    </table>
</body>
</html>