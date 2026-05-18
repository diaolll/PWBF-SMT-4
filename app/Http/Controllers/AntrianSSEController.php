<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class AntrianSSEController extends Controller
{
    // Polling endpoint - lighter alternative to SSE
    public function poll()
    {
        $data = Cache::get('antrian_data', [
            'menunggu' => [],
            'dipanggil' => null,
            'terlambat' => [],
            'selesai' => [],
        ]);

        return response()->json($data);
    }

    public function stream()
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        set_time_limit(0);
        ignore_user_abort(true);

        // Send initial data immediately
        $data = Cache::get('antrian_data', [
            'menunggu' => [],
            'dipanggil' => null,
            'terlambat' => [],
            'selesai' => [],
        ]);
        echo "data: " . json_encode($data) . "\n\n";
        ob_flush();
        flush();

        $lastData = json_encode($data);
        $iterations = 0;
        $maxIterations = 300; // 5 minutes max
        $emptyIterations = 0;
        $sleepTime = 2;

        while ($iterations < $maxIterations) {
            if (connection_aborted()) {
                break;
            }

            $data = Cache::get('antrian_data', [
                'menunggu' => [],
                'dipanggil' => null,
                'terlambat' => [],
                'selesai' => [],
            ]);

            $jsonData = json_encode($data);

            if ($jsonData !== $lastData) {
                echo "data: $jsonData\n\n";
                $lastData = $jsonData;
                $emptyIterations = 0;
                $sleepTime = 2;
            } else {
                $emptyIterations++;
                // Increase sleep time if no changes for 15 seconds
                if ($emptyIterations > 7) {
                    $sleepTime = 3;
                }
            }

            echo ": ping\n\n";

            ob_flush();
            flush();
            sleep($sleepTime);
            $iterations++;
        }

        echo "event: close\ndata: Connection timeout, please refresh\n\n";
        exit;
    }
}
