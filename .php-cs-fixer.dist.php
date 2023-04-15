<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude(
        [
            'var',
            'tests/_support/_generated'

        ]
    )
    ->notPath('src/Symfony/Component/Translation/Tests/fixtures/resources.php')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
    '@Symfony:risky' => true,
    'declare_strict_types' => true,
    'strict_param' => true,
])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ;