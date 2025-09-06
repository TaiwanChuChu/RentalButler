<?php

use Illuminate\Support\Facades\Route;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Laravel\Facades\LINEMessagingApi;

Route::post('/receive-line-bot-notify', function (\Illuminate\Http\Request $request) {
    \Log::debug('$request', [
        '$request' => $request->all()
    ]);
    $events = $request->events;
    foreach ($events as $event) {
        $type = $event['type'] ?? null;
        \Log::debug('type', [
            $type,
        ]);

        // 只針對訊息回覆
        if ($type !== 'message') {
            return response()->noContent();
        }

        /** @var array $messageObject */
        $messageObject = $event['message'];
        \Log::debug('messageObject', [
            $messageObject,
        ]);
        $messageType = $messageObject['type'] ?? null;
        $message = $messageObject['text'] ?? null;
        $replyMessage = 'Love U';

        if ($messageType === 'sticker') {
            $replyMessage = '別一直傳貼圖敷衍我好嗎？';
        }

        if ($messageType === 'image') {
            $replyMessage = '傳什麼鳥照？難看死了！';
        }

        if ($message === '今天天氣如何') {
            $replyMessage = '天曉得，不會自己去 Google 哦';
        }

        $replyToken = $event['replyToken'] ?? null;
        if (!$replyToken) {
            \Log::error('沒有解析到 replyToken，無法回傳訊息', [
                'event' => $event,
            ]);
        }
        $pushMessageRequest = ReplyMessageRequest::fromAssocArray([
            'replyToken' => $replyToken,
            'messages' => [
                [
                    'type' => 'text',
                    'text' => $replyMessage,
                ],
            ],
        ]);
        $response = LINEMessagingApi::replyMessage($pushMessageRequest);
        \Log::info('response: ', [$response]);
    }

//    $response = LINEMessagingApi::replyMessageRequest($pushMessageRequests);
//    \Log::info('response: ', [
//        $response,
//    ]);
});
