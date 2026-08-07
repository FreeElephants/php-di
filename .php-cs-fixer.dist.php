<?php
// .php-cs-fixer.dist.php

$finder = PhpCsFixer\Finder::create()
    ->in([
        './config/',
        './src/',
        './tests/',
    ]);

$overridedProjectRules = [

];

return \FreeElephants\PhpCsFixer\build_config($finder, $overridedProjectRules);

