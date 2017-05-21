<?php
declare(strict_types=1);

namespace Infection\Tests\TestFramework\PhpUnit\Coverage;


use Infection\TestFramework\PhpUnit\Coverage\CoverageXmlParser;
use PHPUnit\Framework\TestCase;

class CoverageXmlParserTest extends TestCase
{
    /**
     * @var CoverageXmlParser
     */
    private $parser;

    private $tempDir = __DIR__ . '/../../../Files/phpunit/coverage-xml';

    private $srcDir = __DIR__ . '/../../../Files/phpunit/coverage-xml';

    protected function setUp()
    {
        $this->parser = new CoverageXmlParser($this->tempDir, [$this->srcDir]);
    }

    protected function getXml()
    {
        $xml = file_get_contents(__DIR__ . '/../../../Files/phpunit/coverage-xml/index.xml');

        // replace dummy source path with the real path
        return preg_replace(
            '/(source=\").*?(\")/',
            sprintf('$1%s$2', realpath($this->srcDir)),
            $xml
        );
    }

    public function test_it_collects_data_recursively_for_all_files()
    {
        $coverage = $this->parser->parse($this->getXml());

        // zeroLevel / firstLevel / secondLevel
        $this->assertCount(3, $coverage);
    }

    public function test_it_has_correct_coverage_data_for_each_file()
    {
        $coverage = $this->parser->parse($this->getXml());

        $zeroLevelAbsolutePath = realpath($this->tempDir . '/zeroLevel.php');
        $firstLevelAbsolutePath = realpath($this->tempDir . '/FirstLevel/firstLevel.php');
        $secondLevelAbsolutePath = realpath($this->tempDir . '/FirstLevel/SecondLevel/secondLevel.php');

        $this->assertArrayHasKey($zeroLevelAbsolutePath, $coverage);
        $this->assertArrayHasKey($firstLevelAbsolutePath, $coverage);
        $this->assertArrayHasKey($secondLevelAbsolutePath, $coverage);

        $this->assertCount(0, $coverage[$zeroLevelAbsolutePath]);
        $this->assertCount(4, $coverage[$firstLevelAbsolutePath]);
        $this->assertCount(1, $coverage[$secondLevelAbsolutePath]);

        $this->assertSame(
            'Infection\Tests\Mutator\Arithmetic\PlusTest::test_it_should_not_mutate_plus_with_arrays',
            $coverage[$firstLevelAbsolutePath][30][1]
        );
    }
}