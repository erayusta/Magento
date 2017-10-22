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
                ->load();

$mediaApi = Mage::getModel("catalog/product_attribute_media_api");
$MediaDir = Mage::getConfig()->getOptions()->getMediaDir();
// usage
$t = 0;
foreach ( $products as $product ) {

	$gallery_images = Mage::getModel( 'catalog/product' )->load( $product->getId() )->getMediaGalleryImages();

	$items          = array();
	if(count($gallery_images)>0) {
		foreach ( $gallery_images as $g_image ) {
			if ( file_exists( $g_image['path'] ) ) {
				if(filesize($g_image['path']) == 47373) {
					print_r($g_image);
					print $product->getSku() ." boş image! ".$g_image['url']. " image var!" . filesize( $g_image['path'] ) . "\n";
					$MediaCatalogDir = $MediaDir . DS . 'catalog' . DS . 'product';
					$DirImagePath    = str_replace( "/", DS, $g_image['file'] );
					// remove file from Dir

					$io = new Varien_Io_File();
					$io->rm( $MediaCatalogDir . $DirImagePath );
					$mediaApi->remove($product->getId(), $g_image['file']);

				}
			} else {
			//	print $product->getSku() . " image yok!\n";
				exit;
			}
		}
	}
	else{
		//print $product->getSku()." galerisi yok!\n";

	}

	print $product->getSku() . " " . $t ++ . "\n";


}
