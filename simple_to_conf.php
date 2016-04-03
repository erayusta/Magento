<?php
/*
Magentoda yer alan simple ürünleri okuyup yerine configurable ürün açan kod parçası
*/
ini_set('display_errors', '1');
error_reporting(E_ALL);

include "app/Mage.php";
header('Content-type: text/plain; charset=utf-8');
Mage::app();

$collection = Mage::getResourceModel('catalog/product_collection');
foreach($collection as $key => $_product) {
	$product = $_product->load($_product->getId());
	if($product->getId()<420 && $product->getId()>330 && $product->getTypeId() == 'simple') {
		$configProduct = Mage::getModel('catalog/product');
		if ($product->getYenisezon() == 0) {
			$yenisezon = 70;
		} else {
			$yenisezon = 71;
		}

		try {
			$configProduct
//    ->setStoreId(1) //you can set data in store scope
				->setWebsiteIds(array(1))//website ID the product is assigned to, as an array
				->setAttributeSetId(4)//ID of a attribute set named 'default'
				->setTypeId('configurable')//product type
				->setCreatedAt(strtotime('now'))//product creation time
				->setUpdatedAt(strtotime('now'))//product update time
				->setSku($product->getSku() . "C")//SKU
				->setName($product->getName())//product name
				->setStatus(1)//product status (1 - enabled, 2 - disabled)
				->setTaxClassId(4)//tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
				->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)//catalog and search visibility
				->setCountryOfManufacture('TR')//country of manufacture (2-letter country code)
				->setPrice($product->getPrice())//price in form 11.22
				->setSpecialPrice($product->getSpecialPrice())//special price in form 11.22
				->setSpecialFromDate()//special price from (MM-DD-YYYY)
				->setSpecialToDate()//special price to (MM-DD-YYYY)
				->setMetaTitle($product->getName())
				->setMetaKeyword('')
				->setRenk($product->getRenk())
				->setYeni_sezon($yenisezon)
				->setMetaDescription($product->getMetaDescription())
				->setDescription($product->getDescription())
				->setShortDescription($product->getShortDescription())
				->setStockData(array(
						'use_config_manage_stock' => 0, //'Use config settings' checkbox
						'manage_stock' => 1, //manage stock
						'is_in_stock' => 1, //Stock Availability
					)
				)
				->setCategoryIds($product->getCategoryIds()) //assign product to categories
			;


			$resim1 = pathinfo(Mage::helper('catalog/image')->init($product, 'thumbnail'));
			$dir = Mage::getBaseDir('media') . DS;
			copy(Mage::helper('catalog/image')->init($product, 'thumbnail'), $dir . $resim1['filename'] . $resim1['basename']);

			$resim2 = pathinfo(Mage::helper('catalog/image')->init($product, 'small_image'));
			$dir = Mage::getBaseDir('media') . DS;
			copy(Mage::helper('catalog/image')->init($product, 'small_image'), $dir . $resim2['filename'] . $resim2['basename']);

			$resim3 = pathinfo(Mage::helper('catalog/image')->init($product, 'image'));
			$dir = Mage::getBaseDir('media') . DS;
			copy(Mage::helper('catalog/image')->init($product, 'image'), $dir . $resim3['filename'] . $resim3['basename']);

			$images = array();
			$images = array(
				'thumbnail' => $resim1['filename'] . $resim1['basename'],
				'small_image' => $resim2['filename'] . $resim2['basename'],
				'image' => $resim3['filename'] . $resim3['basename'],
			);

			foreach ($images as $imageType => $imageFileName) {
				$path = $dir . $imageFileName;
				if (file_exists($path)) {
					try {
						$configProduct->addImageToMediaGallery($path, $imageType, false, false);
					} catch (Exception $e) {
						echo $e->getMessage();
					}
				} else {
					echo "Can not find image by path: {$path}<br/>";
				}
			}


			/**/
			/** assigning associated product to configurable */
			/**/
			$configProduct->getTypeInstance()->setUsedProductAttributeIds(array(162)); //attribute ID of attribute 'color' in my store
			$configurableAttributesData = $configProduct->getTypeInstance()->getConfigurableAttributesAsArray();

			$configProduct->setCanSaveConfigurableAttributes(true);
			$configProduct->setConfigurableAttributesData($configurableAttributesData);

			$configProduct->setConfigurableProductsData($configurableProductsData);
			$configProduct->save();

			echo $product->getId() . ' success    ';
		} catch (Exception $e) {
			Mage::log($e->getMessage());
			echo $e->getMessage();
		}
	}
}
