<?php

class JsonlParser implements LogParserInterface {
    public function parseLine(string $line): ?array {
        $data = json_decode($line, true);
        if ($data === null || !is_array($data)) {
            return null;
        }
        if (isset($data['ip'], $data['status'])) {
            return ['ip' => $data['ip'], 'status' => (int)$data['status']];
        }
        return null;
    }
}
