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
                ->addAttributeToSelect( 'id' )
                ->addAttributeToFilter( 'type_id', 'configurable' )
				->addAttributeToFilter( 'status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
                ->load();
Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);

$conn = Mage::getSingleton( 'core/resource' );
$db   = $conn->getConnection( 'core_write' );

foreach ( $products as $product ) {
	try {

		$sql           = "SELECT * FROM catalog_product_entity_media_gallery WHERE entity_id = '" . $product->getId() . "'";
		$res2          = $db->query( $sql );

		$gallery_cnt = 0;
		while ( $mdata = $res2->fetch() ) {
			$file = '../media/catalog/product' . $mdata['value'];
			if ( file_exists( $file ) ) {
				$gallery_cnt ++;
			} // if sonu
		} // while sonu

		if ( $gallery_cnt < 1 ) {
			$simpleProduct = Mage::getModel( 'catalog/product' )->load( $product->getId() );
			$simpleProduct->setStockData(
				array(
					'is_in_stock' => 0,
					'qty' => 0,
					'manage_stock' => 1,
					'use_config_notify_stock_qty' => 1
				)
			);
			$simpleProduct->save();
			print "Resmi yok: ".$simpleProduct->getSku()."\n";

		}

	} catch ( Exception $e ) {
		echo $e->getMessage() . " eklemedi bozuldu\n";
		exit;
	}


}

