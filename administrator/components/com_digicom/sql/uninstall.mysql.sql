DELETE FROM `#__content_types` WHERE `type_alias` IN ('com_digicom.product', 'com_digicom.category');

DROP TABLE IF EXISTS
`#__digicom_products`,
`#__digicom_products_bundle`,
`#__digicom_products_files`,
`#__digicom_products_rating`,
`#__digicom_cart`,
`#__digicom_currencies`,
`#__digicom_customers`,
`#__digicom_licenses`,
`#__digicom_orders`,
`#__digicom_orders_details`,
`#__digicom_promocodes`,
`#__digicom_promocodes_products`,
`#__digicom_session`,
`#__digicom_states`;