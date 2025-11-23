<?php

/**
 * Example usage of PHP Carousel
 */

require_once __DIR__ . '/vendor/autoload.php';

use JulienLinard\Carousel\Carousel;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Carousel Examples</title>
</head>
<body>
    <h1>PHP Carousel Examples</h1>

    <h2>Image Carousel</h2>
    <?php
    $imageCarousel = Carousel::image('image-carousel', [
        'https://via.placeholder.com/800x400/0066cc/ffffff?text=Image+1',
        'https://via.placeholder.com/800x400/cc0066/ffffff?text=Image+2',
        'https://via.placeholder.com/800x400/66cc00/ffffff?text=Image+3',
    ], [
        'height' => '400px',
        'autoplay' => true,
        'autoplayInterval' => 3000,
    ]);
    echo $imageCarousel->render();
    ?>

    <h2>Card Carousel</h2>
    <?php
    $cardCarousel = Carousel::card('card-carousel', [
        [
            'id' => '1',
            'title' => 'Card 1',
            'content' => 'This is the content of card 1',
            'image' => 'https://via.placeholder.com/300x200/0066cc/ffffff?text=Card+1',
            'link' => '#card1',
        ],
        [
            'id' => '2',
            'title' => 'Card 2',
            'content' => 'This is the content of card 2',
            'image' => 'https://via.placeholder.com/300x200/cc0066/ffffff?text=Card+2',
            'link' => '#card2',
        ],
        [
            'id' => '3',
            'title' => 'Card 3',
            'content' => 'This is the content of card 3',
            'image' => 'https://via.placeholder.com/300x200/66cc00/ffffff?text=Card+3',
            'link' => '#card3',
        ],
    ], [
        'itemsPerSlide' => 3,
        'itemsPerSlideDesktop' => 3,
        'itemsPerSlideTablet' => 2,
        'itemsPerSlideMobile' => 1,
        'gap' => 20,
    ]);
    echo $cardCarousel->render();
    ?>

    <h2>Testimonial Carousel</h2>
    <?php
    $testimonialCarousel = Carousel::testimonial('testimonial-carousel', [
        [
            'id' => '1',
            'title' => 'John Doe',
            'content' => 'This is an amazing product! I highly recommend it to everyone.',
            'image' => 'https://via.placeholder.com/100/0066cc/ffffff?text=JD',
        ],
        [
            'id' => '2',
            'title' => 'Jane Smith',
            'content' => 'Excellent quality and great customer service. Will buy again!',
            'image' => 'https://via.placeholder.com/100/cc0066/ffffff?text=JS',
        ],
    ], [
        'transition' => 'fade',
        'autoplayInterval' => 5000,
    ]);
    echo $testimonialCarousel->render();
    ?>
</body>
</html>

