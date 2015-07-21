<?php

namespace SqlParser\Tests\Parser;

use SqlParser\Exceptions\ParserException;
use SqlParser\Parser;
use SqlParser\Token;
use SqlParser\TokensList;

use SqlParser\Tests\TestCase;

class ParserTest extends TestCase
{

    public function testParse()
    {
        $this->runParserTest('parse');
    }

    public function testUnrecognizedStatement()
    {
        $parser = new Parser('SELECT 1; FROM');
        $this->assertEquals(
            'Unrecognized statement type.',
            $parser->errors[0]->getMessage()
        );
    }

    public function testUnrecognizedKeyword()
    {
        $parser = new Parser('SELECT 1 FROM foo PARTITION(bar, baz) AS');
        $this->assertEquals(
            'Unrecognized keyword.',
            $parser->errors[0]->getMessage()
        );
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testError()
    {
        $parser = new Parser(new TokensList());

        $parser->error(__('error #1'), new Token('foo'), 1);
        $parser->error(sprintf(__('%2$s #%1$d'), 2, 'error'), new Token('bar'), 2);

        $this->assertEquals(
            $parser->errors,
            array(
                new ParserException('error #1', new Token('foo'), 1),
                new ParserException('error #2', new Token('bar'), 2),
            )
        );
    }

    /**
     * @expectedException SqlParser\Exceptions\ParserException
     * @expectedExceptionMessage strict error
     * @expectedExceptionCode 3
     */
    public function testErrorStrict()
    {
        $parser = new Parser(new TokensList());
        $parser->strict = true;

        $parser->error(__('strict error'), new Token('foo'), 3);
    }
}
