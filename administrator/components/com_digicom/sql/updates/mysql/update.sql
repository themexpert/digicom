ALTER TABLE  `#__digicom_products` 
ADD  `price_type` TINYINT NOT NULL DEFAULT  '3' AFTER  `price` ,
ADD  `expiration_length` TINYINT NOT NULL AFTER  `price_type` ,
ADD  `expiration_type` VARCHAR( 15 ) NOT NULL AFTER  `expiration_length` ;

CREATE TABLE IF NOT EXISTS `#__digicom_licenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licenseid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  `expire_date` date NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `userid` (`userid`),
  KEY `productid` (`productid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `#__digicom_licenses`
ADD CONSTRAINT `#__digicom_licenses_ibfk_2` FOREIGN KEY (`productid`) REFERENCES `#__digicom_products` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `#__digicom_licenses_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `#__digicom_customers` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;


ALTER TABLE `#__digicom_licenses` ADD FOREIGN KEY (`orderid`) REFERENCES `digicom_100beta1`.`#__digicom_orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `#__digicom_licenses` DROP FOREIGN KEY `#__digicom_licenses_ibfk_1`; ALTER TABLE `#__digicom_licenses` ADD CONSTRAINT `#__digicom_licenses_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `digicom_100beta1`.`#__digicom_customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE; ALTER TABLE `#__digicom_licenses` DROP FOREIGN KEY `#__digicom_licenses_ibfk_2`; ALTER TABLE `#__digicom_licenses` ADD CONSTRAINT `#__digicom_licenses_ibfk_2` FOREIGN KEY (`productid`) REFERENCES `digicom_100beta1`.`#__digicom_products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
