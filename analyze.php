<?php

/**
 * Nginx access.log 分析工具
 * 用法: php analyze.php [access.log 路径]
 * 默认读取当前目录下的 access.log
 */

$logFile = $argv[1] ?? __DIR__ . DIRECTORY_SEPARATOR . 'access.log';

if (!file_exists($logFile)) {
    fwrite(STDERR, "错误: 日志文件不存在: {$logFile}\n");
    exit(1);
}

$totalRequests = 0;
$statusCodes = [];
$ipCounts = [];

$handle = fopen($logFile, 'r');
if (!$handle) {
    fwrite(STDERR, "错误: 无法打开文件: {$logFile}\n");
    exit(1);
}

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    $totalRequests++;

    $matches = [];
    if (preg_match('/^(\S+)\s+.*?"\S+\s+\S+\s+\S+"\s+(\d{3})\s+/', $line, $matches)) {
        $ip = $matches[1];
        $status = (int)$matches[2];

        if (!isset($statusCodes[$status])) {
            $statusCodes[$status] = 0;
        }
        $statusCodes[$status]++;

        if (!isset($ipCounts[$ip])) {
            $ipCounts[$ip] = 0;
        }
        $ipCounts[$ip]++;
    }
}

fclose($handle);

arsort($ipCounts);
$topIps = array_slice($ipCounts, 0, 10, true);

function drawBorder($widths) {
    $line = '+';
    foreach ($widths as $w) {
        $line .= str_repeat('-', $w + 2) . '+';
    }
    echo $line . PHP_EOL;
}

function drawRow($cols, $widths) {
    $line = '|';
    foreach ($cols as $i => $col) {
        $line .= ' ' . str_pad($col, $widths[$i]) . ' |';
    }
    echo $line . PHP_EOL;
}

function drawTitle($title, $totalWidth) {
    echo str_repeat('=', $totalWidth) . PHP_EOL;
    echo str_pad($title, $totalWidth, ' ', STR_PAD_BOTH) . PHP_EOL;
    echo str_repeat('=', $totalWidth) . PHP_EOL;
}

$widths = [8, 8];
$totalWidth = array_sum($widths) + count($widths) * 3 + 1;

drawTitle('Access Log 分析结果', $totalWidth);
echo PHP_EOL;

drawBorder($widths);
drawRow(['总请求数', $totalRequests], $widths);
drawBorder($widths);

echo PHP_EOL;

$codeWidths = [8, 8];
$codeTotalWidth = array_sum($codeWidths) + count($codeWidths) * 3 + 1;

drawTitle('状态码统计', $codeTotalWidth);
echo PHP_EOL;

drawBorder($codeWidths);
drawRow(['状态码', '数量'], $codeWidths);
drawBorder($codeWidths);

foreach ($statusCodes as $code => $count) {
    drawRow([$code, $count], $codeWidths);
}

drawBorder($codeWidths);

echo PHP_EOL;

$ipWidths = [20, 8];
$ipTotalWidth = array_sum($ipWidths) + count($ipWidths) * 3 + 1;

drawTitle('访问量 Top 10 IP', $ipTotalWidth);
echo PHP_EOL;

drawBorder($ipWidths);
drawRow(['IP 地址', '访问次数'], $ipWidths);
drawBorder($ipWidths);

$rank = 1;
foreach ($topIps as $ip => $count) {
    drawRow([$rank . '. ' . $ip, $count], $ipWidths);
    $rank++;
}

drawBorder($ipWidths);

echo PHP_EOL;
