<?php

interface LogParserInterface {
    public function parseLine(string $line): ?array;
}
