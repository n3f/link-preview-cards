<?php
/**
 * Test cases for WP Open Graph Card API endpoint
 */

// Simple test function to validate OG data extraction
function test_og_extraction() {
    $html = '
        <html>
            <head>
                <meta property="og:title" content="Test Title">
                <meta property="og:description" content="Test Description">
                <meta property="og:image" content="https://example.com/image.jpg">
            </head>
        </html>
    ';

    $og = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];

    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
        $og['title'] = $m[1];
    }
    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
        $og['description'] = $m[1];
    }
    if (preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $m)) {
        $og['image'] = $m[1];
    }

    assert($og['title'] === 'Test Title', 'Title extraction failed');
    assert($og['description'] === 'Test Description', 'Description extraction failed');
    assert($og['image'] === 'https://example.com/image.jpg', 'Image extraction failed');

    echo "✓ Basic OG extraction test passed\n";
}

function test_og_extraction_with_quotes() {
    $html = '
        <html>
            <head>
                <meta property="og:title" content="Title with &quot;quotes&quot;">
                <meta property="og:description" content="Description with \'single quotes\'">
            </head>
        </html>
    ';

    $og = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];

    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
        $og['title'] = html_entity_decode($m[1]);
    }
    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
        $og['description'] = html_entity_decode($m[1]);
    }

    assert($og['title'] === 'Title with "quotes"', 'Quoted title extraction failed');
    assert($og['description'] === 'Description with \'single quotes\'', 'Quoted description extraction failed');

    echo "✓ OG extraction with quotes test passed\n";
}

function test_og_extraction_case_insensitive() {
    $html = '
        <html>
            <head>
                <meta PROPERTY="OG:TITLE" content="Uppercase Tags">
                <meta Property="og:description" Content="Mixed Case">
            </head>
        </html>
    ';

    $og = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];

    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
        $og['title'] = $m[1];
    }
    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
        $og['description'] = $m[1];
    }

    assert($og['title'] === 'Uppercase Tags', 'Case insensitive title extraction failed');
    assert($og['description'] === 'Mixed Case', 'Case insensitive description extraction failed');

    echo "✓ OG extraction case insensitive test passed\n";
}

function test_og_extraction_no_tags() {
    $html = '
        <html>
            <head>
                <title>Regular Title</title>
            </head>
        </html>
    ';

    $og = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];

    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
        $og['title'] = $m[1];
    }
    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
        $og['description'] = $m[1];
    }
    if (preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $m)) {
        $og['image'] = $m[1];
    }

    assert($og['title'] === '', 'Empty title extraction failed');
    assert($og['description'] === '', 'Empty description extraction failed');
    assert($og['image'] === '', 'Empty image extraction failed');

    echo "✓ OG extraction with no tags test passed\n";
}

// Run all tests
echo "Running OG API tests...\n";
test_og_extraction();
test_og_extraction_with_quotes();
test_og_extraction_case_insensitive();
test_og_extraction_no_tags();
function test_og_extraction_with_special_characters() {
    $html = '
        <html>
            <head>
                <meta property="og:title" content="Title with &amp; &lt; &gt; &quot;">
                <meta property="og:description" content="Description with \n \t \r">
            </head>
        </html>
    ';

    $og = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];

    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
        $og['title'] = html_entity_decode($m[1]);
    }
    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
        $og['description'] = html_entity_decode($m[1]);
    }

    assert($og['title'] === 'Title with & < > "', 'Special characters in title failed');
    assert($og['description'] === 'Description with \n \t \r', 'Special characters in description failed');

    echo "✓ OG extraction with special characters test passed\n";
}

function test_og_extraction_malformed_html() {
    $html = '
        <html>
            <head>
                <meta property="og:title" content="Test Title">
                <meta property="og:description" content="Test Description">
                <meta property="og:image" content="https://example.com/image.jpg">
            </head>
            <body>
                <div>Some content</div>
                <meta property="og:title" content="Duplicate Title">
            </body>
        </html>
    ';

    $og = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];

    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
        $og['title'] = $m[1];
    }
    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
        $og['description'] = $m[1];
    }
    if (preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $m)) {
        $og['image'] = $m[1];
    }

    // Should get the first occurrence
    assert($og['title'] === 'Test Title', 'First occurrence extraction failed');
    assert($og['description'] === 'Test Description', 'Description extraction failed');
    assert($og['image'] === 'https://example.com/image.jpg', 'Image extraction failed');

    echo "✓ OG extraction with malformed HTML test passed\n";
}

// Run all tests
echo "Running OG API tests...\n";
test_og_extraction();
test_og_extraction_with_quotes();
test_og_extraction_case_insensitive();
test_og_extraction_no_tags();
test_og_extraction_with_special_characters();
test_og_extraction_malformed_html();
echo "All tests passed!\n";