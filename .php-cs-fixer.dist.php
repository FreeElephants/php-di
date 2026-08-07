<?php
// .php-cs-fixer.dist.php

$finder = PhpCsFixer\Finder::create()
    ->in([
        './src/',
        './tests/',
    ]);

$overridedProjectRules = [

];

return \FreeElephants\PhpCsFixer\build_config($finder, $overridedProjectRules);

