<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceVerificationService
{
    private string $apiUrl;
    private float  $threshold;
    private int    $timeout;

    public function __construct()
    {
        $this->apiUrl    = rtrim(config('services.face_api.url', 'http://127.0.0.1:5001'), '/');
        $this->threshold = (float) config('services.face_api.threshold', 0.4);
        $this->timeout   = (int)   config('services.face_api.timeout', 15);
    }

    /**
     * Cek apakah Python Face API aktif.
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->apiUrl}/health");
            return $response->successful();
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Verifikasi wajah selfie terhadap foto referensi mahasiswa.
     *
     * @param  string $referencePath  Path relatif storage Laravel, contoh: 'students/foto.jpg'
     * @param  string $selfieBase64   Gambar selfie dalam format base64 (boleh sertakan header data URI)
     * @return array{
     *   success: bool,
     *   verified: bool,
     *   distance: float|null,
     *   confidence: string,
     *   message: string,
     *   error?: string
     * }
     */
    public function verify(string $referencePath, string $selfieBase64): array
    {
        // ── Cek apakah API aktif ──────────────────────────────────────
        if (!$this->isAvailable()) {
            Log::error('FaceVerificationService: Python API tidak dapat dijangkau di ' . $this->apiUrl);
            return [
                'success'    => false,
                'verified'   => false,
                'distance'   => null,
                'confidence' => 'Tidak Diketahui',
                'message'    => 'Layanan verifikasi wajah tidak aktif. Hubungi administrator.',
                'error'      => 'api_unavailable',
            ];
        }

        // ── Kirim request ke Python API ───────────────────────────────
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->apiUrl}/verify", [
                    'reference_path' => $referencePath,
                    'selfie_base64'  => $selfieBase64,
                ]);

            $body = $response->json();

            // Tangani error dari Python API (4xx/5xx)
            if (!$response->successful()) {
                $errorMsg = $body['error'] ?? 'Terjadi kesalahan pada layanan verifikasi wajah.';
                Log::warning("FaceVerificationService: API error {$response->status()} — {$errorMsg}");

                return [
                    'success'    => false,
                    'verified'   => false,
                    'distance'   => null,
                    'confidence' => 'Tidak Diketahui',
                    'message'    => $errorMsg,
                    'error'      => 'api_error',
                ];
            }

            // Respons berhasil
            return [
                'success'    => true,
                'verified'   => (bool)   ($body['verified']   ?? false),
                'distance'   => isset($body['distance']) ? (float) $body['distance'] : null,
                'confidence' => (string) ($body['confidence'] ?? 'Tidak Diketahui'),
                'message'    => (string) ($body['message']    ?? ''),
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("FaceVerificationService: Koneksi gagal — {$e->getMessage()}");
            return [
                'success'    => false,
                'verified'   => false,
                'distance'   => null,
                'confidence' => 'Tidak Diketahui',
                'message'    => 'Tidak dapat terhubung ke layanan verifikasi wajah.',
                'error'      => 'connection_failed',
            ];
        } catch (\Exception $e) {
            Log::error("FaceVerificationService: Exception — {$e->getMessage()}");
            return [
                'success'    => false,
                'verified'   => false,
                'distance'   => null,
                'confidence' => 'Tidak Diketahui',
                'message'    => 'Terjadi kesalahan sistem saat verifikasi wajah.',
                'error'      => 'exception',
            ];
        }
    }
}
