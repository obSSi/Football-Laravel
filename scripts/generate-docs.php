<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$doxyfile = $projectRoot . DIRECTORY_SEPARATOR . 'Doxyfile';

$candidates = [];

if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
    $candidates[] = 'doxygen';
    $candidates[] = 'C:\\Program Files\\doxygen\\bin\\doxygen.exe';
    $candidates[] = 'C:\\Program Files (x86)\\doxygen\\bin\\doxygen.exe';
} else {
    $candidates[] = 'doxygen';
}

$resolved = null;

$inPath = false;
if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
    $check = @shell_exec('where doxygen 2>NUL');
    $inPath = is_string($check) && trim($check) !== '';
} else {
    $check = @shell_exec('command -v doxygen 2>/dev/null');
    $inPath = is_string($check) && trim($check) !== '';
}

foreach ($candidates as $candidate) {
    if ($candidate === 'doxygen' && $inPath) {
        $resolved = $candidate;
        break;
    }

    if (is_file($candidate)) {
        $resolved = '"' . $candidate . '"';
        break;
    }
}

if ($resolved === null) {
    fwrite(STDERR, "Doxygen introuvable. Installez Doxygen ou ajoutez-le au PATH.\n");
    exit(1);
}

$command = $resolved . ' ' . escapeshellarg($doxyfile);
passthru($command, $exitCode);

if ($exitCode !== 0) {
    fwrite(STDERR, "Echec de la generation Doxygen (code {$exitCode}).\n");
    exit($exitCode);
}

echo "Documentation generee dans docs/doxygen/html/index.html\n";
