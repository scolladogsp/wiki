<?php
$wgGroupPermissions["*"]["searchfiles"] = false;
$wgGroupPermissions["*"]["wikiadmin"] = false;
$wgGroupPermissions["*"]["readshoutbox"] = false;
$wgGroupPermissions["*"]["writeshoutbox"] = false;
$wgGroupPermissions["bureaucrat"]["wikiadmin"] = false;
$wgGroupPermissions["sysop"]["wikiadmin"] = true;
$wgGroupPermissions["sysop"]["workflowedit"] = true;
$wgGroupPermissions["sysop"]["workflowview"] = true;
$wgGroupPermissions["sysop"]["statistics"] = true;
$wgGroupPermissions["sysop"]["searchfiles"] = true;
$wgGroupPermissions["user"]["wikiadmin"] = false;
$wgGroupPermissions["user"]["workflowedit"] = false;
$wgGroupPermissions["user"]["workflowview"] = false;
$wgGroupPermissions["user"]["readshoutbox"] = true;
$wgGroupPermissions["user"]["writeshoutbox"] = true;
$wgGroupPermissions["user"]["wantedarticle-suggest"] = true;
