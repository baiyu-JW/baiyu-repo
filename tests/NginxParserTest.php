<?php

use PHPUnit\Framework\TestCase;

class NginxParserTest extends TestCase {
    private $parser;

    protected function setUp(): void {
        $this->parser = new NginxParser();
    }

    public function testParseValidCombinedFormat(): void {
        $line = '127.0.0.1 - - [10/Oct/2000:13:55:36 -0700] "GET /index.html HTTP/1.1" 200 2326 "http://referer" "Mozilla/5.0"';
        $result = $this->parser->parseLine($line);

        $this->assertNotNull($result);
        $this->assertSame('127.0.0.1', $result['ip']);
        $this->assertSame(200, $result['status']);
    }

    public function testParseAnotherValidLine(): void {
        $line = '192.168.1.50 - - "POST /api/login HTTP/1.0" 404 0';
        $result = $this->parser->parseLine($line);

        $this->assertNotNull($result);
        $this->assertSame('192.168.1.50', $result['ip']);
        $this->assertSame(404, $result['status']);
    }

    public function testEmptyLineReturnsNull(): void {
        $this->assertNull($this->parser->parseLine(''));
    }

    public function testMalformedLineReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('this is not a valid nginx log line'));
    }

    public function testLineMissingStatusReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('127.0.0.1 - - "GET /index.html HTTP/1.1"'));
    }

    public function testLineMissingQuotedRequestReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('127.0.0.1 - - 200 2326'));
    }

    public function testInvalidInputDoesNotThrow(): void {
        $inputs = ['', 'garbage', '127.0.0.1 just ip', "\t\n"];

        foreach ($inputs as $input) {
            try {
                $result = $this->parser->parseLine($input);
                $this->assertNull($result);
            } catch (\Throwable $e) {
                $this->fail('parseLine() threw an exception on invalid input: ' . $e->getMessage());
            }
        }
    }
}
