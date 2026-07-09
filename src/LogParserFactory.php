<?php

class LogParserFactory {
    public static function create(string $logType): LogParserInterface {
        $map = [
            'nginx' => NginxParser::class,
            'apache' => ApacheParser::class,
            'jsonl' => JsonlParser::class,
        ];

        $class = $map[$logType] ?? null;
        if ($class === null) {
            throw new InvalidArgumentException("不支持的日志类型: {$logType}");
        }

        return new $class();
    }
}
