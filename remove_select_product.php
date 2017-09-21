<?php
/**
 * Created by PhpStorm.
 * User: erayusta
 * Date: 21.09.2017
 * Time: 07:20
 */
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
$websiteId = Mage::app()->getWebsite()->getId();
$store     = Mage::app()->getStore();

Mage::app()->setCurrentStore( Mage_Core_Model_App::ADMIN_STORE_ID );
$MediaDir = Mage::getConfig()->getOptions()->getMediaDir();
$mediaApi = Mage::getModel( "catalog/product_attribute_media_api" );

$products = Mage::getResourceModel( 'catalog/product_collection' )
                ->addAttributeToSelect( 'id' )// <- careful with this
                ->addAttributeToFilter(
		'sku', array( 'like' => '15K%' ) )->load();

$t = 0;
foreach ( $products as $product ) {
	$id    = $product->getId();
	$items = $mediaApi->items( $product->getId() );

	foreach ( $items as $item ) {
		$MediaCatalogDir = $MediaDir . DS . 'catalog' . DS . 'product';
		$DirImagePath    = str_replace( "/", DS, $item['file'] );
		// remove file from Dir

		$io = new Varien_Io_File();
		$io->rm( $MediaCatalogDir . $DirImagePath );
		print $MediaCatalogDir . $DirImagePath . "\n";
		$mediaApi->remove( $product->getId(), $item['file'] );
		//$mediaApi->save();
	}

	$product->delete();


	print $t++." ".$id . " OK!" . "\n";

}
