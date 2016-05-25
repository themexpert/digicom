# digicom 1.2.4 release sql update
ALTER TABLE `#__digicom_customers` CHANGE `taxnum` `taxnum` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL
