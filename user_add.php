<?php
/*
CSV ye export edilmiş müşteri datasının magento içerisine alınmasını sağlayan kod parçası.
 */
 
ini_set('display_errors', '1');
error_reporting(E_ALL);

include "app/Mage.php";
header('Content-type: text/plain; charset=utf-8');
Mage::app();

$csv = array_map('str_getcsv', file('users.csv'));


/*
 *        [0] => Ad Soyad
            [1] => Mail
            [2] => Firma
            [3] => Adres
            [4] => İlçe
            [5] => İl
            [6] => Tel
            [7] => Mobil
            [8] => Doğum Tarihi
            [9] => % İndirim
 */

$websiteId = Mage::app()->getWebsite()->getId();
$store = Mage::app()->getStore();

foreach($csv as $key => $user) {
	if ($key > 0) {
		$adsoyad = explode(" ", $user[0]);
		$isim = $adsoyad[0];
		$soyisim = $adsoyad[1];

		if($soyisim == ''){
			$isim = $user[0];
		}

		$currentTime = strtotime('now');

		$customer = Mage::getModel("customer/customer");
		$customer->setWebsiteId($websiteId)
			->setStore($store)
			->setFirstname($isim)
			->setLastname($soyisim)
			->setEmail($user[1])
			->setDob($user[8])
			->setPassword('123234dd');

		try {
			$customer->save();
		} catch (Exception $e) {
			Zend_Debug::dump($e->getMessage());
		}

			$address = Mage::getModel("customer/address");
			$address->setCustomerId($customer->getId())
				->setFirstname($customer->getFirstname())
				->setMiddleName($customer->getMiddlename())
				->setLastname($customer->getLastname())
				->setCountryId('TR')
				->setPostcode()
				->setCity($user[4])
				->setRegion($user[5])
				->setTelephone($user[6])
				->setFax($user[7])
				->setCompany('')
				->setStreet($user[3])
				->setCreatedAt($currentTime)
				->setUpdatedAt($currentTime)
				->setIsDefaultBilling('1')
				->setIsDefaultShipping('1')
				->setSaveInAddressBook('1');

			try {
				$address->save();
				print $user[0]." OK!<br>";
			} catch (Exception $e) {
				Zend_Debug::dump($e->getMessage());
			}
	}
}
