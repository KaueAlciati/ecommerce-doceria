<?php
// includes/telegram.php
// >>> Preencha seu token e (opcional) um chat padrão para fallback <<<

const TG_BOT_TOKEN = '8120127288:AAEgCyZ-WEDyzXRZqgns7Nx7JmBU2TEUOpE'; // seu token do bot
const TG_DEFAULT_CHAT = '1531661746'; // opcional (pode deixar '')

// Envia texto para um chat_id. Retorna true/false.
function telegram_send(string $text, ?string $chatId = null): bool {
    $token  = TG_BOT_TOKEN;
    $chatId = $chatId ?: TG_DEFAULT_CHAT;

    if (!$token || !$chatId) return false;

    $url  = "https://api.telegram.org/bot{$token}/sendMessage";
    $data = [
        'chat_id'                  => $chatId,
        'text'                     => $text,
        'parse_mode'               => 'Markdown',
        'disable_web_page_preview' => true,
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
    ]);
    $res = curl_exec($ch);
    $ok  = $res && ($json = @json_decode($res, true)) && !empty($json['ok']);
    curl_close($ch);
    return $ok;
}
