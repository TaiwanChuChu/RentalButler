<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Laravel\Facades\LINEMessagingApi;

class NotifyModemReboot extends Command
{
    protected $signature = 'iot:notify-reboot';
    protected $description = '發送小烏龜重啟通知到 LINE 群組';

    public function handle(): int
    {
        $groupId = config('line-group.testing');

        if (!$groupId) {
            Log::error('LINE_GROUP_ID_FOR_RESIDENCE 未設定');
            $this->error('LINE_GROUP_ID_FOR_RESIDENCE 未設定');
            return Command::FAILURE;
        }

        try {
            // 建立文字訊息
            $textMessage = new TextMessage([
                'type' => 'text',
                'text' => "🐢 小烏龜即將重啟！\n⚠️ 5分鐘後網路會短暫中斷\n⏰ 預計 23:55 執行重啟"
            ]);

            // 建立推送請求
            $request = new PushMessageRequest([
                'to' => $groupId,
                'messages' => [$textMessage]
            ]);

            // 發送訊息
            $response = LINEMessagingApi::pushMessage($request);

            Log::info('LINE 重啟通知發送成功', [
                'response' => $response,
            ]);
            $this->info('LINE 通知已發送！');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            Log::error('LINE 通知發送失敗: ' . $e->getMessage());
            $this->error('LINE 通知發送失敗: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
