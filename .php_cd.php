<?php

use Akeneo\CouplingDetector\Domain\Rule;
use Akeneo\CouplingDetector\Domain\RuleInterface;
use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/src');

$rules = [
    new Rule('Khatovar\Component\User\Domain', [], RuleInterface::TYPE_ONLY),
    new Rule('Khatovar\Component\User\Application', ['Khatovar\Component\User\Domain'], RuleInterface::TYPE_ONLY),
];

return new \Akeneo\CouplingDetector\Configuration\Configuration($rules, $finder);
