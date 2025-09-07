<?php

use Illuminate\Support\Facades\Route;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Laravel\Facades\LINEMessagingApi;

Route::post('/receive-line-bot-notify', function (\Illuminate\Http\Request $request) {
    $events = $request->events;
    try {
        foreach ($events as $event) {
            $type = $event['type'] ?? null;
            $userId = Arr::get($event, 'source.userId');
            $ownerId = config('app.owner_line_user_id');

            if ($userId !== $ownerId) {
                \Log::debug('不是開發者，不回應');
                return response()->noContent(200);
            }

            $groupId = Arr::get($event, 'source.groupId');
            $testingGroupId = config('line-group.testing');
            if ($groupId !== $testingGroupId) {
                \Log::debug('當前並非在測試群組，不回應');
                return response()->noContent(200);
            }

            // 只針對訊息回覆
            if ($type !== 'message') {
                return response()->noContent(200);
            }

            /** @var array $messageObject */
            $messageObject = $event['message'];
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
                return response()->noContent(200);
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

            LINEMessagingApi::replyMessage($pushMessageRequest);
        }
    } catch (\Throwable $th) {
        \Log::error('發生錯誤', [
            'request' => $request,
            'trace' => $th->getTraceAsString(),
            'error' => $th->getMessage(),
        ]);
    }

    return response()->noContent(200);
});
