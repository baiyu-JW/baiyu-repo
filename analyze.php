<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'LogParserInterface.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'NginxParser.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'ApacheParser.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'JsonlParser.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'LogParserFactory.php';

$configPath = __DIR__ . DIRECTORY_SEPARATOR . 'config.json';

if (!file_exists($configPath)) {
    fwrite(STDERR, "错误: 配置文件不存在: {$configPath}\n");
    exit(1);
}

$config = json_decode(file_get_contents($configPath), true);
if ($config === null) {
    fwrite(STDERR, "错误: 配置文件 JSON 格式无效\n");
    exit(1);
}

$logType = $config['log_type'] ?? 'nginx';
$logFile = $config['log_file'] ?? __DIR__ . DIRECTORY_SEPARATOR . 'access.log';
$topN = $config['top_n'] ?? 10;

if (!file_exists($logFile)) {
    fwrite(STDERR, "错误: 日志文件不存在: {$logFile}\n");
    exit(1);
}

try {
    $parser = LogParserFactory::create($logType);
} catch (InvalidArgumentException $e) {
    fwrite(STDERR, "错误: " . $e->getMessage() . "\n");
    exit(1);
}

$handle = fopen($logFile, 'r');
if (!$handle) {
    fwrite(STDERR, "错误: 无法打开文件: {$logFile}\n");
    exit(1);
}

$totalRequests = 0;
$statusCodes = [];
$ipCounts = [];

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if ($line === '') {
        continue;
    }

    $totalRequests++;

    $result = $parser->parseLine($line);
    if ($result !== null) {
        $ip = $result['ip'];
        $status = $result['status'];

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
$topIps = array_slice($ipCounts, 0, $topN, true);

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

drawTitle('访问量 Top ' . $topN . ' IP', $ipTotalWidth);
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
