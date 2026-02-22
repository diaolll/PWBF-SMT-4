<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function generateSertifikat()
    {
        // Data yang ingin ditampilkan di PDF
        $data = [
            'nama' => 'David Greymaax',
            'peran' => 'Fullstack Developer',
            'tanggal' => date('d F Y')
        ];

        // Load view sertifikat dan set ukuran kertas A4 Landscape
        $pdf = Pdf::loadView('pdf.sertifikat', $data)
                  ->setPaper('a4', 'landscape');

        // Menampilkan PDF di browser (stream)
        return $pdf->stream('Sertifikat-Digital.pdf');
    }

        public function generateSurat(Request $request)
    {
        $data = [
            'nomor_surat' => '001/FTI/UNIV/2026',
            'tanggal' => date('d F Y'),
            'perihal' => 'Undangan Rapat Kurikulum'
        ];

        $pdf = Pdf::loadView('pdf.surat', $data)->setPaper('a4', 'portrait');

        // Jika user klik link unduh
        if ($request->get('action') == 'download') {
            return $pdf->download('Surat-Undangan.pdf');
        }

        // Default: Buka preview di browser
        return $pdf->stream('Surat-Undangan.pdf');
    }
}