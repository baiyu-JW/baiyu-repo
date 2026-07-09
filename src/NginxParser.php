<?php

class NginxParser implements LogParserInterface {
    public function parseLine(string $line): ?array {
        if (preg_match('/^(\S+)\s+.*?"\S+\s+\S+\s+\S+"\s+(\d{3})\s+/', $line, $matches)) {
            return ['ip' => $matches[1], 'status' => (int)$matches[2]];
        }
        return null;
    }
}
