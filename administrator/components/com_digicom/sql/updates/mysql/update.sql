ALTER TABLE  `#__digicom_products` ADD  `price_type` TINYINT NOT NULL DEFAULT  '3' AFTER  `price` ;
ALTER TABLE  `#__digicom_products` ADD  `expiration` VARCHAR( 10 ) NOT NULL AFTER  `price_type` ;
ALTER TABLE  `#__digicom_products` ADD  `expiration_length` TINYINT NOT NULL AFTER  `expiration` ,
ADD  `expiration_type` VARCHAR( 15 ) NOT NULL AFTER  `expiration_length` ;