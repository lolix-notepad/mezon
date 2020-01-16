<?php
require_once ('autoload.php');

class SecurityRulesUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing edge cases of getFileValue
     */
    public function testGetEmptyFileValue(): void
    {
        // setup
        $_FILES = [
            'test-file' => [
                'size' => 0
            ]
        ];
        $SecurityRules = $this->getMockBuilder(\Mezon\Security\SecurityRules::class)
            ->setMethods([
            '_prepareFs',
            'filePutContents',
            'moveUploadedFile'
        ])
            ->setConstructorArgs([])
            ->getMock();

        // test body
        $Result = $SecurityRules->getFileValue('test-file', false);

        // assertions
        $this->assertEquals('', $Result);
    }

    /**
     * Data provider for the testGetFileValue test
     *
     * @return array data for test testGetFileValue
     */
    public function getFileValueProvider(): array
    {
        return ([
            [
                true,
                [
                    'test-file' => [
                        'size' => 1,
                        'file' => '1',
                        'name' => '1'
                    ]
                ]
            ],
            [
                false,
                [
                    'test-file' => [
                        'size' => 1,
                        'file' => '1',
                        'name' => '1'
                    ]
                ]
            ],
            [
                true,
                [
                    'test-file' => [
                        'size' => 1,
                        'tmp_name' => '1',
                        'name' => '1'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Testing edge cases of getFileValue
     *
     * @param bool $StoreFile
     *            do we need to store file
     * @param array $Files
     *            file ddescription
     * @dataProvider getFileValueProvider
     */
    public function testGetFileValue(bool $StoreFile, array $Files): void
    {
        // setup
        $_FILES = $Files;
        $SecurityRules = $this->getMockBuilder(\Mezon\Security\SecurityRules::class)
            ->setMethods([
            '_prepareFs',
            'filePutContents',
            'moveUploadedFile'
        ])
            ->setConstructorArgs([])
            ->getMock();

        if ($StoreFile) {
            if (isset($Files['test-file']['tmp_name'])) {
                $SecurityRules->expects($this->once())
                    ->method('moveUploadedFile');
            } else {
                $SecurityRules->expects($this->once())
                    ->method('filePutContents');
            }
        }

        // test body
        $Result = $SecurityRules->getFileValue('test-file', $StoreFile);

        // assertions
        if ($StoreFile) {
            $this->assertStringContainsString('/data/files/' . date('Y/m/d/'), $Result);
        } else {
            $this->assertEquals(1, $Result['size']);

            $this->assertEquals('1', $Result['name']);
            if (isset($Files['test-file']['tmp_name'])) {
                $this->assertEquals('1', $Result['tmp_name']);
            } else {
                $this->assertEquals('1', $Result['file']);
            }
        }
    }

    /**
     * Data provider for the testStoreFileContent
     *
     * @return array data for the testStoreFileContent test
     */
    public function storeFileContentProvider(): array
    {
        return [
            [
                true
            ],
            [
                false
            ]
        ];
    }

    /**
     * Testing storeFileContent method
     *
     * @dataProvider storeFileContentProvider
     */
    public function testStoreFileContent(bool $Decoded): void
    {
        // setup
        $SecurityRules = $this->getMockBuilder(\Mezon\Security\SecurityRules::class)
            ->setMethods([
            '_prepareFs',
            'filePutContents',
            'moveUploadedFile'
        ])
            ->setConstructorArgs([])
            ->getMock();
        $SecurityRules->method('_prepareFs')->willReturn('prepared');
        $SecurityRules->expects($this->once())
            ->method('filePutContents');

        // test body
        $Result = $SecurityRules->storeFileContent('content', 'prefix', $Decoded);

        // assertions
        $this->assertStringContainsString('/data/files/'.date('Y/m/d/'), $Result);
    }
}
