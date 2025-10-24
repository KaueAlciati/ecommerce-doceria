<?php
function sendTelegramMessage($chat_id, $message) {
    $token = '8120127288:AAEgCyZ-WEDyzXRZqgns7Nx7JmBU2TEUOpE';
    $url = "https://api.telegram.org/bot{$token}/sendMessage";
    
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS => json_encode($data)
    ]);
    $res = curl_exec($ch);
    curl_close($ch);

    return $res;
}

// Teste: envie uma mensagem pro seu Telegram
$chat_id = 1531661746; // seu chat_id
$message = "🍰 *Doce Encanto* informa:\nSeu pedido foi *confirmado com sucesso!* 🎉\nAgradecemos a preferência, Kaue 💖";

sendTelegramMessage($chat_id, $message);
echo "Mensagem enviada!";
?>
