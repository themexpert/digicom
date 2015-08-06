# this is a short databse update for better log or stats report

ALTER TABLE  `#__digicom_orders_details` CHANGE `expires`  `update` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ;

ALTER TABLE  `#__digicom_log` ADD `callbackid` INT NULL DEFAULT NULL COMMENT  'orderid | fileid ; quickid to find items' AFTER  `callback` ;

ALTER TABLE  `#__digicom_orders` ADD `update_date` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER  `comment` ;
