<?php

declare(strict_types=1);

namespace PhpCfdi\SatWsDescargaMasiva\Tests\Unit\PackageReader;

use PhpCfdi\SatWsDescargaMasiva\PackageReader\MetadataItem;
use PhpCfdi\SatWsDescargaMasiva\PackageReader\MetadataPackageReader;
use PhpCfdi\SatWsDescargaMasiva\Tests\TestCase;

class MetadataItemTest extends TestCase
{
    public function testWithEmptyData(): void
    {
        $metadata = new MetadataItem([]);
        $this->assertSame('', $metadata->uuid);
        $this->assertSame('', $metadata->get('uuid'));
        $this->assertSame([], $metadata->all());
    }

    public function testWithContents(): void
    {
        $data = ['uuid' => 'x-uuid', 'oneData' => 'one data'];
        $metadata = new MetadataItem($data);
        $this->assertSame('x-uuid', $metadata->uuid);
        $this->assertSame('x-uuid', $metadata->get('uuid'));
        $this->assertSame('one data', $metadata->get('oneData'));
        $this->assertSame('one data', $metadata->{'oneData'}); /** @phpstan-ignore-line */
        $this->assertSame($data, $metadata->all());
    }

    public function testIsset(): void
    {
        $data = ['uuid' => 'x', 'rfcEmisor' => '', 'fechaCancelacion' => null];
        /** @phpstan-ignore-next-line */
        $metadata = new MetadataItem($data);
        $this->assertTrue(isset($metadata->{'uuid'}), 'uuid has a value');
        $this->assertTrue(isset($metadata->{'rfcEmisor'}), 'rfcEmisor was set as empty string');
        $this->assertFalse(isset($metadata->{'fechaCancelacion'}), 'fechaCancelacion was set as null');
        $this->assertFalse(isset($metadata->{'nombreACuentaTerceros'}), "nombreACuentaTerceros wasn't set");
    }

    public function testReaderCfdiInZip(): void
    {
        $expectedContent = $this->fileContents('zip/metadata.txt');

        $zipFilename = $this->filePath('zip/metadata.zip');
        $packageReader = MetadataPackageReader::createFromFile($zipFilename);
        $extracted = (string) current(iterator_to_array($packageReader->fileContents()));

        // normalize line endings
        $expectedContent = str_replace("\r\n", "\n", $expectedContent);
        $extracted = str_replace("\r\n", "\n", $extracted);
        $this->assertSame($expectedContent, $extracted);
    }
}
