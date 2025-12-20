<?php

declare(strict_types=1);

namespace JulienLinard\Carousel\Tests;

use PHPUnit\Framework\TestCase;

class CMSIntegrationTest extends TestCase
{
    public function testWordPressPluginFileExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../plugins/wordpress/php-carousel-wordpress.php');
    }

    public function testWordPressPluginHasRequiredStructure(): void
    {
        $content = file_get_contents(__DIR__ . '/../plugins/wordpress/php-carousel-wordpress.php');
        
        $this->assertStringContainsString('Plugin Name:', $content);
        $this->assertStringContainsString('class PHP_Carousel_WordPress', $content);
        $this->assertStringContainsString('add_shortcode', $content);
        $this->assertStringContainsString('render_carousel', $content);
    }

    public function testPrestaShopModuleFileExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../plugins/prestashop/phpcarousel.php');
    }

    public function testPrestaShopModuleHasRequiredStructure(): void
    {
        $content = file_get_contents(__DIR__ . '/../plugins/prestashop/phpcarousel.php');
        
        $this->assertStringContainsString('class PHPCarousel extends Module', $content);
        $this->assertStringContainsString('hookDisplayHome', $content);
        $this->assertStringContainsString('Carousel::productShowcase', $content);
    }

    public function testDrupalModuleFileExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../plugins/drupal/php_carousel.module');
    }

    public function testDrupalModuleHasRequiredStructure(): void
    {
        $content = file_get_contents(__DIR__ . '/../plugins/drupal/php_carousel.module');
        
        $this->assertStringContainsString('function php_carousel_block_info', $content);
        $this->assertStringContainsString('function php_carousel_render_carousel', $content);
        $this->assertStringContainsString('Carousel::', $content);
    }

    public function testDrupalInfoFileExists(): void
    {
        $this->assertFileExists(__DIR__ . '/../plugins/drupal/php_carousel.info.yml');
    }

    public function testDrupalInfoFileHasRequiredFields(): void
    {
        $content = file_get_contents(__DIR__ . '/../plugins/drupal/php_carousel.info.yml');
        
        $this->assertStringContainsString('name:', $content);
        $this->assertStringContainsString('type: module', $content);
        $this->assertStringContainsString('PHP Carousel', $content);
    }

    public function testAllPluginsCheckForLibrary(): void
    {
        $wordpress = file_get_contents(__DIR__ . '/../plugins/wordpress/php-carousel-wordpress.php');
        $prestashop = file_get_contents(__DIR__ . '/../plugins/prestashop/phpcarousel.php');
        $drupal = file_get_contents(__DIR__ . '/../plugins/drupal/php_carousel.module');
        
        $this->assertStringContainsString('class_exists', $wordpress);
        $this->assertStringContainsString('class_exists', $prestashop);
        $this->assertStringContainsString('class_exists', $drupal);
    }

    public function testAllPluginsUseAutoloader(): void
    {
        $wordpress = file_get_contents(__DIR__ . '/../plugins/wordpress/php-carousel-wordpress.php');
        $prestashop = file_get_contents(__DIR__ . '/../plugins/prestashop/phpcarousel.php');
        $drupal = file_get_contents(__DIR__ . '/../plugins/drupal/php_carousel.module');
        
        $this->assertStringContainsString('vendor/autoload.php', $wordpress);
        $this->assertStringContainsString('vendor/autoload.php', $prestashop);
        $this->assertStringContainsString('vendor/autoload.php', $drupal);
    }
}

