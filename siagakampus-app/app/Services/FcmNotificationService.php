<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    /**
     * Send push notification to user device via Firebase Cloud Messaging (FCM).
     */
    public static function sendReportStatusNotification(
        User $user,
        Report $report,
        string $newStatus,
        ?string $reason = null
    ): bool {
        $title = match ($newStatus) {
            'Resolved' => 'Laporan Banjir Telah Diselesaikan',
            'Rejected' => 'Pemberitahuan: Laporan Ditolak',
            'Handling' => 'Laporan Banjir Sedang Ditangani',
            'Verified' => 'Laporan Banjir Telah Terverifikasi',
            default => 'Pembaruan Status Laporan Banjir',
        };

        $body = match ($newStatus) {
            'Resolved' => "Laporan genangan Anda (#{$report->code} - {$report->title}) telah berhasil ditangani oleh tim lapangan.",
            'Rejected' => "Laporan (#{$report->code}) ditolak. Alasan: " . ($reason ?? 'Tidak memenuhi kriteria validasi.'),
            'Handling' => "Petugas tanggap darurat telah dikerahkan ke lokasi (#{$report->code}).",
            'Verified' => "Laporan (#{$report->code}) valid dan masuk dalam antrian penanganan.",
            default => "Status laporan (#{$report->code}) berubah menjadi {$newStatus}.",
        };

        // Log push notification payload for development & audit
        Log::info('[FCM Push Notification Dispatch]', [
            'recipient_user_id' => $user->id,
            'recipient_name' => $user->name,
            'report_id' => $report->id,
            'report_code' => $report->code,
            'status' => $newStatus,
            'notification_title' => $title,
            'notification_body' => $body,
            'timestamp' => now()->toIso8601String(),
        ]);

        $fcmServerKey = config('services.fcm.server_key') ?? env('FCM_SERVER_KEY');
        $fcmToken = $user->fcm_token ?? null;

        // If FCM credentials are configured and user device token is available
        if ($fcmServerKey && $fcmToken) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'key=' . $fcmServerKey,
                    'Content-Type' => 'application/json',
                ])->timeout(5)->post('https://fcm.googleapis.com/fcm/send', [
                    'to' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'icon' => asset('icons/icon-192x192.png'),
                        'click_action' => url('/reports/' . $report->id),
                    ],
                    'data' => [
                        'report_id' => (string) $report->id,
                        'report_code' => $report->code,
                        'status' => $newStatus,
                        'reason' => (string) $reason,
                    ],
                ]);

                return $response->successful();
            } catch (\Throwable $e) {
                Log::warning('[FCM Send Error] ' . $e->getMessage());
                return false;
            }
        }

        // Successfully queued/logged in local/development environment
        return true;
    }
}

