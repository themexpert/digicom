# digicom 1.3.0 release sql update
ALTER TABLE  `#__digicom_session` CHANGE  `sid`  `sid` INT( 11 ) NOT NULL ;
ALTER TABLE  `#__digicom_session` DROP PRIMARY KEY ;
ALTER TABLE  `#__digicom_session` ADD  `id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE  `#__digicom_session` CHANGE  `sid`  `sid` VARCHAR( 191 ) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL ;
ALTER TABLE  `#__digicom_session` CHANGE  `shipping_details`  `shipping_details` VARCHAR( 255 ) NOT NULL ;

