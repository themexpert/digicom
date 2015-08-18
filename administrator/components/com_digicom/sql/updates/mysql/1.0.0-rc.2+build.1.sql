ALTER TABLE  `#__digicom_log`
CHANGE  `type`  `type` VARCHAR( 255 ) NOT NULL COMMENT 'download|email|purchase|status|payment';
