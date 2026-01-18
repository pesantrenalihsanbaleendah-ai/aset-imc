<?php
/**
 * Test WhatsApp Webhook - Approval Flow
 * Script untuk testing approval/rejection dengan data yang sudah di-setup
 */

// URL webhook (sesuaikan dengan environment Anda)
$webhookUrl = 'http://localhost/aset-imc/public/webhook/whatsapp';

echo "=== WhatsApp Webhook Approval Test ===\n\n";

// Nomor yang sudah di-setup
$testPhone = '6281234567890';

// Test 1: Approval
echo "Test 1: Approval (ketik 1)\n";
echo "--------------------------------------\n";

$approvalData = [
    'message' => '1',
    'sender' => $testPhone,
    'from' => $testPhone,
    'timestamp' => time()
];

$response = sendWebhookRequest($webhookUrl, $approvalData);
echo "HTTP Code: " . $response['http_code'] . "\n";
echo "Response: " . json_encode($response['response'], JSON_PRETTY_PRINT) . "\n\n";

if ($response['http_code'] == 200 && isset($response['response']['status']) && $response['response']['status'] == 'success') {
    echo "✓ APPROVAL BERHASIL!\n\n";
    
    // Tunggu sebentar sebelum test rejection
    sleep(2);
    
    // Buat loan pending baru untuk test rejection
    echo "Membuat loan pending baru untuk test rejection...\n";
    exec('php setup-webhook-test.php > nul 2>&1');
    sleep(1);
    
    // Test 2: Rejection
    echo "\nTest 2: Rejection (ketik 2)\n";
    echo "--------------------------------------\n";
    
    $rejectionData = [
        'message' => '2',
        'sender' => $testPhone,
        'from' => $testPhone,
        'timestamp' => time()
    ];
    
    $response2 = sendWebhookRequest($webhookUrl, $rejectionData);
    echo "HTTP Code: " . $response2['http_code'] . "\n";
    echo "Response: " . json_encode($response2['response'], JSON_PRETTY_PRINT) . "\n\n";
    
    if ($response2['http_code'] == 200 && isset($response2['response']['status']) && $response2['response']['status'] == 'success') {
        echo "✓ REJECTION BERHASIL!\n\n";
    } else {
        echo "✗ Rejection gagal\n\n";
    }
} else {
    echo "✗ Approval gagal. Kemungkinan:\n";
    echo "   - Tidak ada loan pending\n";
    echo "   - User tidak ditemukan\n";
    echo "   - Jalankan dulu: php setup-webhook-test.php\n\n";
}

echo "=== Test Selesai ===\n";
echo "\nCek log di storage/logs/laravel.log untuk detail lengkap\n";

/**
 * Fungsi untuk mengirim request ke webhook
 */
function sendWebhookRequest($url, $data) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => json_decode($response, true) ?? $response
    ];
}
