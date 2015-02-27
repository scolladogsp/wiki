-- Database definition for PageTemplates
--
-- Part of BlueSpice for MediaWiki
--
-- @author     Markus Glaser <glaser@hallowelt.biz>

-- @package    BlueSpice_Extensions
-- @subpackage PageTemplates
-- @copyright  Copyright (C) 2011 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
-- @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
-- @filesource

CREATE TABLE IF NOT EXISTS /*$wgDBprefix*/bs_pagetemplate (
  pt_id                 int(10) unsigned    NOT NULL AUTO_INCREMENT,
  pt_label              varchar(255)        NOT NULL DEFAULT '',
  pt_desc               varchar(255)        NOT NULL DEFAULT '',
  pt_target_namespace   int(11)             NOT NULL DEFAULT -99,
  pt_template_title     varbinary(255)      NOT NULL DEFAULT '', /* foreign key to page_title */
  pt_template_namespace int(11)             NOT NULL DEFAULT 0,  /* foreign key to page_namespace */
  pt_sid                int(10) unsigned    NOT NULL DEFAULT 0,
  UNIQUE KEY pt_id (pt_id)
)/*$wgDBTableOptions*/;
