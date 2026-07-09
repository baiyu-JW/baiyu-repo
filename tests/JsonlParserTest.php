<?php

use PHPUnit\Framework\TestCase;

class JsonlParserTest extends TestCase {
    private $parser;

    protected function setUp(): void {
        $this->parser = new JsonlParser();
    }

    public function testParseValidJsonObject(): void {
        $line = '{"ip":"127.0.0.1","status":200}';
        $result = $this->parser->parseLine($line);

        $this->assertNotNull($result);
        $this->assertSame('127.0.0.1', $result['ip']);
        $this->assertSame(200, $result['status']);
    }

    public function testParseValidJsonWithExtraFields(): void {
        $line = '{"ip":"10.0.0.5","status":404,"method":"GET","path":"/missing"}';
        $result = $this->parser->parseLine($line);

        $this->assertNotNull($result);
        $this->assertSame('10.0.0.5', $result['ip']);
        $this->assertSame(404, $result['status']);
    }

    public function testEmptyLineReturnsNull(): void {
        $this->assertNull($this->parser->parseLine(''));
    }

    public function testMalformedJsonReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('{not valid json'));
    }

    public function testMissingFieldReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('{"ip":"127.0.0.1"}'));
        $this->assertNull($this->parser->parseLine('{"status":200}'));
    }

    public function testNonObjectJsonReturnsNull(): void {
        $this->assertNull($this->parser->parseLine('[1,2,3]'));
        $this->assertNull($this->parser->parseLine('"just a string"'));
        $this->assertNull($this->parser->parseLine('42'));
    }

    public function testInvalidInputDoesNotThrow(): void {
        $inputs = ['', 'not json', '{broken', '{"ip":"1.2.3.4"}'];

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
