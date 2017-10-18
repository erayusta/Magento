<?php

/* Delete old image and add new images */

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

$dir    = '../images/';
$files = scandir($dir);

$MediaDir = Mage::getConfig()->getOptions()->getMediaDir();
$mediaApi = Mage::getModel( "catalog/product_attribute_media_api" );

foreach($files as $key => $file){
if($key > 2){

	$barkod = str_replace("_1.jpg","",$file);
	$barkod = str_replace("_2.jpg","",$barkod);
	$barkod = str_replace("_3.jpg","",$barkod);
	$barkod = str_replace("_4.jpg","",$barkod);
	$barkod = str_replace("_5.jpg","",$barkod);
	$barkod = str_replace("_6.jpg","",$barkod);
	$barkod = str_replace("_7.jpg","",$barkod);
	$barkod = str_replace("_8.jpg","",$barkod);

	$urunler[$barkod] = $barkod;
	}
}

  $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('barkod',array('in'=>$urunler))->setPageSize(30)->setCurPage($_GET['sayfa']);
  
 foreach($collection as $product){

	$parentIds = Mage::getResourceSingleton('catalog/product_type_configurable')->getParentIdsByChild($product->getId());

if(!$parentIds) {
	print "Ürün yok!".$product->getSku()."<br>";
	continue;
}
	$pImage  = "../images/" . $product->getBarkod()."_1.jpg";
	$pImage2  = "../images/" . $product->getBarkod()."_2.jpg";
	$pImage3  = "../images/" . $product->getBarkod()."_3.jpg";
	$pImage4  = "../images/" . $product->getBarkod()."_4.jpg";
	$pImage5  = "../images/" . $product->getBarkod()."_5.jpg";
	$pImage6  = "../images/" . $product->getBarkod()."_6.jpg";
	$pImage7  = "../images/" . $product->getBarkod()."_7.jpg";
	$pImage8  = "../images/" . $product->getBarkod()."_8.jpg";
	
		$dir    = Mage::getBaseDir( 'media' ) . DS;
		$images = null;

		if ( str_replace( "../images/", "", $pImage ) != "" && file_exists( $pImage )) {
		
			$resim1 = pathinfo( $pImage );
			copy( $pImage, $dir . $resim1['filename'] . $resim1['basename'] );
			$images[] = $resim1['filename'] . $resim1['basename'];
		}
		if ( str_replace( "../images/", "", $pImage2 ) != "" && file_exists( $pImage2 )) {
			$resim2 = pathinfo( $pImage2 );
			copy( $pImage2, $dir . $resim2['filename'] . $resim2['basename'] );
			$images[] = $resim2['filename'] . $resim2['basename'];
		}
		if ( str_replace( "../images/", "", $pImage3 ) != "" && file_exists( $pImage3 )) {
			$resim3 = pathinfo( $pImage3 );
			copy( $pImage3, $dir . $resim3['filename'] . $resim3['basename'] );
			$images[] = $resim3['filename'] . $resim3['basename'];
		}
		if ( str_replace( "../images/", "", $pImage4 ) != "" && file_exists( $pImage4 )) {
			$resim4 = pathinfo( $pImage4 );
			copy( $pImage4, $dir . $resim4['filename'] . $resim4['basename'] );
			$images[] = $resim4['filename'] . $resim4['basename'];
		}
		if ( str_replace( "../images/", "", $pImage5 ) != "" && file_exists( $pImage5 )) {
			$resim5 = pathinfo( $pImage5 );
			copy( $pImage5, $dir . $resim5['filename'] . $resim5['basename'] );
			$images[] = $resim5['filename'] . $resim5['basename'];
		}
		if ( str_replace( "../images/", "", $pImage6 ) != "" && file_exists( $pImage6 )) {
			$resim6 = pathinfo( $pImage6 );
			copy( $pImage6, $dir . $resim6['filename'] . $resim6['basename'] );
			$images[] = $resim6['filename'] . $resim6['basename'];
		}
		if ( str_replace( "../images/", "", $pImage7 ) != "" && file_exists( $pImage7 )) {
			$resim7 = pathinfo( $pImage7 );
			copy( $pImage7, $dir . $resim7['filename'] . $resim7['basename'] );
			$images[] = $resim7['filename'] . $resim7['basename'];
		}
		if ( str_replace( "../images/", "", $pImage8 ) != "" && file_exists( $pImage8 )) {
			$resim8 = pathinfo( $pImage8 );
			copy( $pImage8, $dir . $resim8['filename'] . $resim8['basename'] );
			$images[] = $resim8['filename'] . $resim8['basename'];
		}
		

		$configProduct = Mage::getModel( 'catalog/product' )->load( $parentIds[0] );

	$items = $mediaApi->items( $configProduct->getId() );
	try {
	foreach ( $items as $item ) {
		$MediaCatalogDir = $MediaDir . DS . 'catalog' . DS . 'product';
		$DirImagePath    = str_replace( "/", DS, $item['file'] );
		// remove file from Dir
		$io = new Varien_Io_File();
		$io->rm( $MediaCatalogDir . $DirImagePath );
		$mediaApi->remove( $configProduct->getId(), $item['file'] );
	//	$mediaApi->save();

	    }
	} catch (Exception $exception){
    var_dump($exception);
    die('Exception Thrown');
		}
		
		$configProduct->save();

		$configProduct = Mage::getModel( 'catalog/product' )->load( $parentIds[0] );
		$imageType = array( 'thumbnail', 'small_image', 'image' );
		if ( $images != null ) {
			$images = array_reverse( $images );
			foreach ( $images as $imageFileName ) {
				$path = $dir . $imageFileName;
				if ( file_exists( $path ) ) {
					try {
						$configProduct->addImageToMediaGallery( $path, $imageType, false, false );
					} catch ( Exception $e ) {
						echo $e->getMessage();
						exit;
					}
				} else {
					echo "Can not find image by path: {$path}<br/>";
					exit;
				}
			}
		}

		$configProduct->save();
		print "Image Eklendi!: " . $configProduct->getSku() . "<br>";

}
	
