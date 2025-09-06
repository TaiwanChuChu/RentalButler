<?php

use Illuminate\Support\Facades\Schedule;

// 每週日晚上 23:50 發送重啟小烏龜的 LINE 通知至中清套房群組
Schedule::command('iot:notify-reboot')->cron('50 23 * * 0');

// 每週日晚上 23:55 重新啟動小烏龜
Schedule::command('iot:reboot-modem')->cron('55 23 * * 0');
