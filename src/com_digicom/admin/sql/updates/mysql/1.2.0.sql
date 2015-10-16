# digicom 1.2.0 release sql update
ALTER TABLE  `#__digicom_orders`
ADD  `tax` DECIMAL( 13, 3 ) NOT NULL AFTER  `amount`,
CHANGE `price` `price` DECIMAL( 13, 3 ) NOT NULL DEFAULT '0.000' COMMENT 'original price without discount and tax';
