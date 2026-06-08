<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach($files as $file) {
    if (is_array($file)) $file = $file[0];
    
    $content = file_get_contents($file);
    
    // Perform staggered replacements to avoid overlapping
    $newContent = str_replace(
        ['text-gray-400', 'text-gray-500', 'border-gray-200', 'border-gray-300'],
        ['TMP_G600', 'TMP_G700', 'TMP_B300', 'TMP_B400'],
        $content
    );
    
    $newContent = str_replace(
        ['TMP_G600', 'TMP_G700', 'TMP_B300', 'TMP_B400'],
        ['text-gray-600', 'text-gray-700', 'border-gray-300', 'border-gray-400'],
        $newContent
    );
    
    // Additional generic bright text on orange fixes to enhance readability
    // Replaces cases like bg-orange-500 with bg-orange-600 where appropriate, actually we overrode it in tailwind config so we are fine.
    
    if ($content !== $newContent) {
        file_put_contents($file, $newContent);
        echo "Updated: " . basename($file) . "\n";
        $count++;
    }
}
echo "Total updated files: $count\n";
