<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


class InstalDigicomCest
{
	public function installJoomla(\AcceptanceTester $I)
	{
		$I->am('Administrator');
		// $I->installJoomlaRemovingInstallationFolder();
		$I->doAdministratorLogin();
		// $I->disableStatistics();
		$I->setErrorReportingToDevelopment();
	}

	/**
	 * @depends installJoomla
	 */
	public function installDigicom(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('get Digicom repository folder from acceptance.suite.yml (see _support/AcceptanceHelper.php)');

		// URL where the package file to install is located (mostly the same as joomla-cms)
		$url = $I->getConfiguration('url');
		$I->installExtensionFromUrl($url . "/pkg_digicom_1.3.3-beta1.zip");
		$I->doAdministratorLogout();
	}

	/**
	 * @depends installDigicom
	 */
	public function checkInstallation(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('Now go to Digicom dashboard to see everything ok');

		$I->amGoingTo('Navigate to Digicom page in /administrator/');
		$I->amOnPage('administrator/index.php?option=com_digicom');

		$I->waitForText('Report of the Month', 60, ['xpath' => "//div[@id='j-main-container']//h3"]);
		$I->expectTo('see digicom page');
		$I->doAdministratorLogout();
	}

	/**
	 * @depends checkInstallation
	 */
	public function checkAdminMenu(\AcceptanceTester $I)
	{
		$I->doAdministratorLogin();
		$I->comment('Now check if Digicom admin menu module published');
		$I->amGoingTo('Navigate through quick admin menu module');
		
		$I->click(['xpath' => "//ul[@id='digicom-menu']//li//a[@class='dropdown-toggle']"]);
        $I->waitForElement(['xpath' => "//li[@class='dropdown open']/ul[@class='dropdown-menu']//a//span[text() = 'Dashboard']"], 60);
        $I->click(['xpath' => "//li[@class='dropdown open']/ul[@class='dropdown-menu']//a//span[text() = 'Dashboard']"]);
        $I->waitForElement(['id' => 'adminForm'], 60);
        $I->waitForText('Report of the Month', 60, ['xpath' => "//div[@id='j-main-container']//h3"]);

		$I->doAdministratorLogout();
	}
	
	/**
	 * @depends checkAdminMenu
	 */
	public function checkToolberMenu(\AcceptanceTester $I)
	{
		$I->am('Administrator');
		$I->doAdministratorLogin();
		$I->comment('Now check if Digicom toolber menu has created');
		$I->amGoingTo('Navigate through menu manager');
		
		$I->amOnPage('administrator/index.php?option=com_menus&view=items&menutype=digicom_toolber');

		$I->waitForText('Menus: Items (DigiCom Toolber)', 60, ['css' => "h1"]);
		$I->expectTo('see digicom toolber menu');

		$I->waitForText(['xpath' => "//div[@id='j-main-container']//table//tr//td/a[text() = 'Cart']"]);
		$I->expectTo('Expect to see cart menu');
		
		$I->doAdministratorLogout();
	}

}