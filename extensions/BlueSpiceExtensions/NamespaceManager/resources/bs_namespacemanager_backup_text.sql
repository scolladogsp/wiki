-- Database definition for table bs_namespacemanager_backup_text in NamespaceManager
--
-- Part of BlueSpice for MediaWiki
--
-- @author     Sebastian Ulbricht <sebastian.ulbricht@gmx.de>

-- @package    BlueSpice_Extensions
-- @subpackage NamespaceManager
-- @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
-- @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
-- @filesource

CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/bs_namespacemanager_backup_text (
  old_id    int(10)     unsigned NOT NULL,
  old_text  mediumblob           NOT NULL,
  old_flags tinyblob             NOT NULL
) /*$wgDBTableOptions*/;
