<?php

	// add to product field "mailchimplistid"
	$this->addField('#__digicom_products', 'mailchimplistid', 'varchar(255)', false, '');

	// delete mailchimplistid
	$this->dropField('#__digicom_settings', 'mailchimplistid');


?>