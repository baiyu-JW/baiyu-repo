<?php

use PHPUnit\Framework\TestCase;

class ApacheParserTest extends TestCase {
    private $parser;

    protected function setUp(): void {
        $this->parser = new ApacheParser();
    }

    public function testParseValidCombinedFormat(): void {
        $line = '127.0.0.1 user-identifier frank [10/Oct/2000:13:55:36 -0700] "GET /apache_pb.gif HTTP/1.0" 200 2326';
        $result = $this->parser->parseLine($line);

        $this->assertNotNull($result);
        $this->assertSame('127.0.0.1', $result['ip']);
        $this->assertSame(200, $result['status']);
    }

    public function testParseAnotherValidLine(): void {
        $line = '10.0.0.2 - bob [01/Jan/2021:00:00:00 +0000] "POST /submit HTTP/1.1" 500 12';
        $result = $this->parser->parseLine($line);

        $this->assertNotNull($result);
        $this->assertSame('10.0.0.2', $result['ip']);
        $this->assertSame(500, $result['status']);
    }

    public function testEmptyLineReturnsNull(): void {
        $this->assertNull($this->parser->parseLine(''));
    }

    public function testMalformedLineReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('completely invalid apache log'));
    }

    public function testLineMissingStatusReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('127.0.0.1 user-identifier frank [10/Oct/2000:13:55:36 -0700] "GET /x HTTP/1.0"'));
    }

    public function testLineMissingTimestampReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('127.0.0.1 user-identifier frank "GET /x HTTP/1.0" 200 2326'));
    }

    public function testInvalidInputDoesNotThrow(): void {
        $inputs = ['', 'garbage', '127.0.0.1 - -', "\n"];

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
