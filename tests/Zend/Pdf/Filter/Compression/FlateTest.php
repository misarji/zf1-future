<?php

use PHPUnit\Framework\TestCase;

class Zend_Pdf_Filter_Compression_FlateTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('zlib')) {
            self::markTestSkipped('This test requires zlib');
        }
    }

    public function testEncodeException()
    {
        try {
            Zend_Pdf_Filter_Compression_Flate::encode(null, str_repeat('foo', 1000));
        } catch (Exception $e) {
            self::assertInstanceOf('Zend_Pdf_Exception', $e);
            self::assertStringNotContainsString('Not implemented yet', $e->getMessage());

            return;
        }

        self::fail('This test throw and handle an exception and return early');
    }

    public function testDecodeException()
    {
        try {
            Zend_Pdf_Filter_Compression_Flate::decode(null);
        } catch (Exception $e) {
            self::assertInstanceOf('Zend_Pdf_Exception', $e);
            self::assertStringNotContainsString('Not implemented yet', $e->getMessage());
        }

        self::fail('This test throw and handle an exception and return early');
    }
}
