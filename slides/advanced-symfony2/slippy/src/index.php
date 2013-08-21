<?php

/**
 * Slippy
 * Copyright (C) 2010, Jordi Boggiano
 * http://seld.be/ - j.boggiano@seld.be
 *
 * Licensed under the new BSD License
 * See the LICENSE file for details
 *
 * Version: 0.9.0
 */

// init
$file = null;
$dir = dirname(__FILE__).'/';
$repositoryTemplate = 'repo.php';
if (file_exists($dir.'config.php')) {
    include $dir.'config.php';
    $dir = rtrim($dir, '/').'/';
}

// handle CLI mode
if (PHP_SAPI === 'cli') {
    if (!isset($_SERVER['argv'][1])) {
        echo "USAGE: index.php <name of your slides html file> [<target file>]\n";
        exit(1);
    } elseif (!file_exists($_SERVER['argv'][1])) {
        echo "File not found: ".$_SERVER['argv'][1]."\n";
        exit(1);
    }

    $file = $_SERVER['argv'][1];
    if (isset($_SERVER['argv'][2])) {
        $targetfile = $_SERVER['argv'][2];
    } else {
        $targetfile = substr($file, 0, strrpos($file, '.')).'_compiled.html';
    }

    if (file_exists($targetfile)) {
        echo "File $targetfile exists. Do you want to overwrite (y/n)? [y]: ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        if (strtolower(trim($line)) !== 'y' && trim($line) !== '') {
            echo "Aborting.\n";
            exit(1);
        }
    }

    $html = compactDeck(cleanDeck($file));
    file_put_contents($targetfile, $html);
    echo "Successfully saved slides to $targetfile\n";
    exit(0);
}

// fetch slide deck
if (isset($_GET['file'])) {
    $file = $dir . basename($_GET['file']);
}

// list slide decks if none is not found
if (!file_exists($file) || !is_file($file) || !is_readable($file)) {
    $decks = array_reverse(glob($dir.'*.html'));
    $decks = fetchDecksData($decks);

    include $repositoryTemplate;
    die;
}

$html = cleanDeck($file);

// handle downloads
if (isset($_GET['download']) && $_GET['download']) {
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    echo compactDeck($html);
    exit(0);
}

echo $html;

/**
 * Prepare slide deck content
 */
function cleanDeck($file)
{
    $html = file_get_contents($file);
    return preg_replace_callback('{(<pre[^>]+>)(.+?)(</pre>)}s', 'slippyRecode', $html);
}

/**
 * Strips the leading whitespace off <pre> tags and html encodes them
 */
function slippyRecode($match)
{
    $whitespace = preg_replace('#^\r?\n?([ \t]*).*#s', '$1', $match[2]);
    $output = preg_replace('/^'.preg_quote($whitespace, '/').'/m', '', $match[2]);
    return $match[1] . htmlspecialchars($output) . $match[3];
}

/**
 * Fetches the data of each deck file passed to it
 */
function fetchDecksData($decks)
{
    foreach ($decks as $idx => $file) {
        $decks[$idx] = array(
            'file' => $file,
            'filename' => basename($file, '.html'),
        );
        $content = file_get_contents($file);
        $content = preg_replace('#</head>.*#s', '</head>', $content) . '</html>';
        if ($content = simplexml_load_string($content)) {
            foreach ($content->head->meta as $meta) {
                if (!$meta->attributes()->name) {
                    continue;
                }
                $name = (string) $meta->attributes()->name;
                if (in_array($name, array('venue', 'date', 'author', 'email'))) {
                    $decks[$idx][$name] = (string) $meta->attributes()->content;
                }
            }
            $decks[$idx]['topic'] = (string) $content->head->title;
        }
        if (!isset($decks[$idx]['topic']) || !$decks[$idx]['topic']) {
            $decks[$idx]['topic'] = $decks[$idx]['filename'];
        }
    }
    return $decks;
}

/**
 * Embeds all dependencies (js, css, images) into a slide deck file and serves it as a download
 *
 * @param string $html the content of the slides html file
 */
function compactDeck($html)
{
    $doc = new DOMDocument();
    @$doc->loadHTML($html);
    $xpath = new DOMXPath($doc);
    $jsFiles = $xpath->evaluate('//script[@type="text/javascript"][@src!=""]');
    foreach ($jsFiles as $js) {
        $node = $doc->createElement('script', '');
        $node->appendChild($doc->createCDATASection(file_get_contents($js->getAttribute('src'))));
        $node->setAttribute('type', 'text/javascript');
        $js->parentNode->replaceChild($node, $js);
    }
    $cssFiles = $xpath->evaluate('//link[@type="text/css"][@rel="stylesheet"][@href!=""]');
    foreach ($cssFiles as $css) {
        $node = $doc->createElement('style', '');
        $node->appendChild($doc->createCDATASection(file_get_contents($css->getAttribute('href'))));
        $node->setAttribute('type', 'text/css');
        $css->parentNode->replaceChild($node, $css);
    }
    $imgFiles = $xpath->evaluate('//img[@src!=""]');
    $imgAttributes = $xpath->evaluate('//*[@data-background!=""]');
    foreach ($imgFiles as $img) {
        $source = $img->getAttribute('src');
        if ($data = convertImage($source)) {
            $img->setAttribute('src', $data);
        }
    }
    foreach ($imgAttributes as $img) {
        $source = $img->getAttribute('data-background');
        if ($data = convertImage($source)) {
            $img->setAttribute('data-background', $data);
        }
    }

    return $doc->saveHTML();
}

function convertImage($url)
{
    static $types = array(
        'png' => 'image/png',
        'gif' => 'image/gif',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
    );

    if (PHP_SAPI !== 'cli') {
        $baseUrl = ($_SERVER['SERVER_PORT'] === 443 ? 'https':'http') .'://'. $_SERVER['HTTP_HOST'].'/index.php';
        $parts = parse_url($baseUrl);
        $imgUrl = $parts['scheme'].'://'.$parts['host'];
        if ($url{0} !== '/') {
            if (substr($parts['path'], -1) === '/') {
                $imgUrl .= $parts['path'];
            } elseif (dirname($parts['path']) === '\\') {
                $imgUrl .= '/';
            } else {
                $imgUrl .= dirname($parts['path']);
            }
        }
    } else {
        // no image path rewriting on cli
        $imgUrl = '';
    }
    $imgUrl .= $url;
    $ext = strtolower(substr($url, strrpos($url, '.') + 1));
    if (isset($types[$ext])) {
        return 'data:'.$types[$ext].';base64,'.base64_encode(file_get_contents(str_replace(' ', '%20', $imgUrl)));
    }
}
