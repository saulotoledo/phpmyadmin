<?php
/**
 * Tests for PhpMyAdmin\FileListing
 * @package PhpMyAdmin\Tests
 */

namespace PhpMyAdmin\Tests;

use PhpMyAdmin\FileListing;
use PHPUnit\Framework\TestCase;

/**
 * Class FileListingTest
 * @package PhpMyAdmin\Tests
 */
class FileListingTest extends TestCase
{
    /**
     * @var FileListing $fileListing
     */
    private $fileListing;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        $this->fileListing = new FileListing();
    }

    /**
     * @return void
     */
    public function testGetDirContent(): void
    {
        $this->assertFalse($this->fileListing->getDirContent('nonexistent directory'));

        $fixturesDir = ROOT_PATH . 'test/classes/_data/file_listing';

        $this->assertSame(
            array_values([
                'one.txt',
                'two.md',
            ]),
            array_values($this->fileListing->getDirContent($fixturesDir))
        );
    }

    /**
     * @return void
     */
    public function testGetFileSelectOptions(): void
    {
        $fixturesDir = ROOT_PATH . 'test/classes/_data/file_listing';

        $this->assertFalse($this->fileListing->getFileSelectOptions('nonexistent directory'));

        $expectedHtmlWithoutActive = <<<HTML
  <option value="one.txt">
    one.txt
  </option>
  <option value="two.md">
    two.md
  </option>

HTML;

        $this->assertSame(
            $expectedHtmlWithoutActive,
            $this->fileListing->getFileSelectOptions($fixturesDir)
        );

        $expectedHtmlWithActive = <<<HTML
  <option value="one.txt">
    one.txt
  </option>
  <option value="two.md" selected="selected">
    two.md
  </option>

HTML;

        $this->assertSame(
            $expectedHtmlWithActive,
            $this->fileListing->getFileSelectOptions($fixturesDir, '', 'two.md')
        );

        $expectedFilteredHtml = <<<HTML
  <option value="one.txt">
    one.txt
  </option>

HTML;

        $this->assertSame(
            $expectedFilteredHtml,
            $this->fileListing->getFileSelectOptions($fixturesDir, '/.*\.txt/')
        );
    }

    /**
     * @return void
     */
    public function testSupportedDecompressions(): void
    {
        $GLOBALS['cfg']['ZipDump'] = false;
        $GLOBALS['cfg']['GZipDump'] = false;
        $GLOBALS['cfg']['BZipDump'] = false;
        $this->assertEmpty($this->fileListing->supportedDecompressions());

        $GLOBALS['cfg']['ZipDump'] = true;
        $GLOBALS['cfg']['GZipDump'] = true;
        $GLOBALS['cfg']['BZipDump'] = true;
        $this->assertEquals('gz|bz2|zip', $this->fileListing->supportedDecompressions());
    }
}
