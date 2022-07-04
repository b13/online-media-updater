<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->getFinder()->exclude(['var', 'Tests/Acceptance/Support/_generated']);
return $config;
