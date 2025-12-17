<?php
use PHPUnit\Framework\TestCase;

class FileTypeTest extends TestCase
{
    private $projectFiles = [
        'index.php','news.php','prakiraan.php','bantuan.php','peta.php','detail.php'
    ];

    public function test_files_exist()
    {
        foreach ($this->projectFiles as $file) {
            $this->assertFileExists($file, "File $file tidak ditemukan!");
        }
    }

    public function test_php_files_contain_php_code()
    {
        foreach ($this->projectFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($file);
                $this->assertStringContainsString('<?php', $content, "File $file tidak mengandung kode PHP!");
            }
        }
    }

    public function test_html_files_contain_html_tags()
    {
        foreach ($this->projectFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($file);

                $this->assertMatchesRegularExpression(
                    '/<html|<head|<body|<div|<p|<span/i',
                    $content,
                    "File $file bukan HTML yang valid!"
                );
            }
        }
    }

    
    public function test_php_files_not_empty()
    {
        foreach ($this->projectFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $this->assertGreaterThan(
                    0,
                    filesize($file),
                    "File $file kosong!"
                );
            }
        }
    }

    
    public function test_php_files_have_html_closing_tag()
    {
        foreach ($this->projectFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($file);

                $this->assertStringContainsString(
                    '</html>',
                    strtolower($content),
                    "File $file tidak memiliki tag penutup </html>!"
                );
            }
        }
    }
}
