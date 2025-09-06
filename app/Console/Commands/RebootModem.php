<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Laravel\Facades\LINEMessagingApi;

class RebootModem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iot:reboot-modem';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '重新啟動小烏龜';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $scriptFileName = 'reboot-router.sh';
        $scriptFullPath = storage_path('app/private/scripts/' . $scriptFileName);

        if (!file_exists($scriptFullPath)) {
            \Log::error('腳本檔案不存在：' . $scriptFullPath);
            $this->error('腳本檔案不存在：' . $scriptFullPath);
            return Command::FAILURE;
        }

        if (!is_executable($scriptFullPath)) {
            \Log::warning('腳本檔案不可執行，嘗試設定權限：' . $scriptFullPath);
            chmod($scriptFullPath, 0755);
        }

        $result = shell_exec($scriptFullPath);

        if (!$result) {
            \Log::error('腳本執行失敗，無輸出結果');
            $this->error('腳本執行失敗');

            try {
                // 建立文字訊息
                $textMessage = new TextMessage([
                    'type' => 'text',
                    'text' => "重啟小烏龜腳本執行失敗"
                ]);

                // 建立推送請求
                $request = new PushMessageRequest([
                    'to' => config('line-group.residence'),
                    'messages' => [$textMessage]
                ]);

                // 發送訊息
                LINEMessagingApi::pushMessage($request);
            } catch (\Exception $exception) {
                \Log::error('訊息發送失敗：' . $exception->getMessage());
            }

            return Command::FAILURE;
        }

        try {
            // 建立文字訊息
            $textMessage = new TextMessage([
                'type' => 'text',
                'text' => "重啟小烏龜腳本執行完成，重啟過程約 120 秒",
            ]);

            // 建立推送請求
            $request = new PushMessageRequest([
                'to' => config('line-group.residence'),
                'messages' => [$textMessage]
            ]);

            // 發送訊息
            LINEMessagingApi::pushMessage($request);
        } catch (\Exception $exception) {
            \Log::error('訊息發送失敗：' . $exception->getMessage());
        }

        \Log::info('小烏龜重啟腳本執行完成，輸出：' . $result);
        $this->info('小烏龜重啟完成！輸出：' . $result);
        return Command::SUCCESS;
    }
}
