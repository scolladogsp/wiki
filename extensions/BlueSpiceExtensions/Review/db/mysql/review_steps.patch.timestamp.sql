ALTER TABLE /*$wgDBprefix*/bs_review_steps CHANGE `timestamp` `revs_timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;