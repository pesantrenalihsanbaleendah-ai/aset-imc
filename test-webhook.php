<?php
/**
 * Test WhatsApp Webhook
 * Script untuk testing webhook WhatsApp dengan data dummy
 */

// URL webhook (sesuaikan dengan environment Anda)
$webhookUrl = 'http://localhost/aset-imc/public/webhook/whatsapp';

echo "=== WhatsApp Webhook Tester ===\n\n";

// Test Case 1: Approval Message (ketik "1")
echo "Test 1: Mengirim pesan approval (1)\n";
echo "--------------------------------------\n";

$approvalData = [
    'message' => '1',
    'sender' => '6281234567890',  // Ganti dengan nomor yang ada di database users
    'from' => '6281234567890',
    'timestamp' => time()
];

$response1 = sendWebhookRequest($webhookUrl, $approvalData);
echo "Response: " . json_encode($response1, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 2: Rejection Message (ketik "2")
echo "Test 2: Mengirim pesan rejection (2)\n";
echo "--------------------------------------\n";

$rejectionData = [
    'message' => '2',
    'sender' => '6281234567890',
    'from' => '6281234567890',
    'timestamp' => time()
];

$response2 = sendWebhookRequest($webhookUrl, $rejectionData);
echo "Response: " . json_encode($response2, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 3: Group Message (dengan @g.us)
echo "Test 3: Mengirim dari Group WhatsApp\n";
echo "--------------------------------------\n";

$groupData = [
    'message' => '1',
    'sender' => '120363123456789@g.us',  // Format group ID
    'from' => '120363123456789@g.us',
    'timestamp' => time()
];

$response3 = sendWebhookRequest($webhookUrl, $groupData);
echo "Response: " . json_encode($response3, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 4: Invalid Message (bukan "1" atau "2")
echo "Test 4: Mengirim pesan invalid (bukan 1 atau 2)\n";
echo "--------------------------------------\n";

$invalidData = [
    'message' => 'Halo, apa kabar?',
    'sender' => '6281234567890',
    'from' => '6281234567890',
    'timestamp' => time()
];

$response4 = sendWebhookRequest($webhookUrl, $invalidData);
echo "Response: " . json_encode($response4, JSON_PRETTY_PRINT) . "\n\n";

// Test Case 5: Missing Data
echo "Test 5: Data tidak lengkap (missing sender)\n";
echo "--------------------------------------\n";

$missingData = [
    'message' => '1',
    // sender tidak ada
];

$response5 = sendWebhookRequest($webhookUrl, $missingData);
echo "Response: " . json_encode($response5, JSON_PRETTY_PRINT) . "\n\n";

echo "=== Test Selesai ===\n";
echo "\nCatatan:\n";
echo "- Pastikan aplikasi Laravel sudah running\n";
echo "- Cek log di storage/logs/laravel.log untuk detail\n";
echo "- Sesuaikan nomor sender dengan data user di database\n";

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
