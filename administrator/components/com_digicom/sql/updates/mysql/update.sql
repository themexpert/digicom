ALTER TABLE  `#__digicom_products` 
ADD  `price_type` TINYINT NOT NULL DEFAULT  '3' AFTER  `price` ,
ADD  `expiration_length` TINYINT NOT NULL AFTER  `price_type` ,
ADD  `expiration_type` VARCHAR( 15 ) NOT NULL AFTER  `expiration_length` ;