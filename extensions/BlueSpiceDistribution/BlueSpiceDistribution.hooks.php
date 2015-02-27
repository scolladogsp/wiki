<?php

$wgHooks['BeforePageDisplay'][] = 'BlueSpiceDistributionHooks::onBeforePageDisplay';
$wgHooks['MinervaPreRender'][] = 'BlueSpiceDistributionHooks::onMinervaPreRender';
$wgHooks['ResourceLoaderRegisterModules'][] = 'BlueSpiceDistributionHooks::onResourceLoaderRegisterModules';
