<?php
/**
 * Created by PhpStorm.
 * User: erayusta
 * Date: 19.10.2017
 * Time: 01:26
 */
header( 'Content-Type: text/html; charset=utf-8' );
ini_set( 'display_errors', '1' );
error_reporting( E_ALL );
ini_set( 'max_execution_time', 0 );
set_time_limit( 0 );
ini_set( 'memory_limit', '-1' );
require_once( '../app/Mage.php' );
umask( 0 );
Mage::setIsDeveloperMode( true );
Mage::app();
Mage::register( 'isSecureArea', true );
Mage::app()->setCurrentStore( Mage_Core_Model_App::ADMIN_STORE_ID );
$websiteId = Mage::app()->getWebsite()->getId();
$store     = Mage::app()->getStore();

$products = Mage::getResourceModel( 'catalog/product_collection' )
                ->addAttributeToSelect( '*' )
                ->addAttributeToFilter( 'type_id', 'configurable' )
				->addAttributeToFilter('sku', array('like' => '18KC%'))
	->setPageSize(10)
                ->load();

$api = Mage::getSingleton('catalog/category_api');

$t = 0;
$time_start = microtime(true);
foreach ( $products as $product ) {
	$categoryIds = $product->getResource()->getCategoryIds($product);
	foreach ($categoryIds as $category_id) {
		$api->assignProduct($category_id, $product->getId(), 0);
	}

	print $product->getSku() . " " . $t ++ . "\n";


}

$time_end = microtime(true);

$execution_time = ($time_end - $time_start)/60;
echo '<b>Total Execution Time:</b> '.$execution_time.' Mins';
exit;


