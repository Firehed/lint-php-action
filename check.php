<?php

declare(strict_types=1);

use RecursiveDirectoryIterator as RDI;

function debug(string $message)
{
    echo "::debug::$message\n";
}

// Create a map of normalized file extensions
$extensionsToCheck = array_flip(array_map('strtolower', array_map('trim', explode(',', getenv('INPUT_FILE_EXTENSIONS')))));

debug("File extensions: " . implode(', ', array_keys($extensionsToCheck)));

// This assumes that the action doesn't do anything too silly
$rdi = new RecursiveDirectoryIterator('.', RDI::SKIP_DOTS);
$rii = new RecursiveIteratorIterator($rdi);

$checkCount = $skipCount = 0;
$exit = 0;

foreach ($rii as $file => $fileinfo) {
    if (!array_key_exists($fileinfo->getExtension(), $extensionsToCheck)) {
        $skipCount++;
        debug("Skipping $file");
        continue;
    }

    debug("Checking $file");
    $command = sprintf('php -l %s 2>&1', escapeshellarg($file));
    $output = []; // Must reset inside each loop; exec appends rather than sets
    $ret = exec($command, $output, $exitCode);

    $checkCount++;

    if ($exitCode === 0) {
        // Lint OK - immediately move to the next file.
        continue;
    }

    $exit = 1;

    // Try to parse the output for more detailed info
    $printed = false;
    foreach ($output as $line) {
        // Something like this:
        // "Parse error: syntax error, unexpected token "private", expecting "{" in src/someFile.php on line 23"
        $matched = preg_match("/Parse error:\s+(?'text'.*) in (?'file'.*) on line (?'line'\d+)$/", $line, $matches);
        if ($matched) {
            $relativePath = mb_substr($file, 2); // Trim leading `./`
            echo ":error file=$relativePath,line={$matches['line']}::{$matches['text']}\n";
            echo "::error file=$relativePath,line={$matches['line']}::{$matches['text']}\n";
            $printed = true;
            break;
        }
    }
    if (!$printed) {
        // Fallback error format
        echo "::error file=$file::Syntax error\n";
    }

}

echo "Checked $checkCount files, skipped $skipCount\n";

exit($exit);
