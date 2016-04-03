<?php
/*
Başka bir eticaret script inden export edilmiş CSV yi düzenleyip magentoya ürün olarak ekleyen kod parçası.
*/

//print_r($urunler);
/*
 *
 * [0] => StokNo
    [1] => urunADI
    [2] => SEOLink
    [3] => StokKodu
    [4] => Barkod
    [5] => EntegrasyonKodu
    [6] => kategoriID
    [7] => EkstraKategoriID
    [8] => markaADI
    [9] => urunModel
    [10] => urunGrubu
    [11] => RenkGrupKodu
    [12] => RenkADI
    [13] => KisaAciklama
    [14] => DetayAciklama
    [15] => Aktif-1-Pasif-0
    [16] => GoruntulenmeSira
    [17] => MinSatisMiktar
    [18] => MaxSatisMiktar
    [19] => AksesuarUrunID
    [20] => SiteiciAramaKelimeleri
    [21] => SEOanahtarKelimeler
    [22] => MaliyetFiyat
    [23] => PiyasaFiyat
    [24] => Fiyat
    [25] => BayiFiyat
    [26] => FiyatKurID
    [27] => KDVoran
    [28] => indirim%
    [29] => KargoDesi
    [30] => StokAdedi
    [31] => StokDurumID
    [32] => SecenekTipi(0-Liste;1-Secenek;2-Matris)
    [33] => Secenek/MatrisKodu(Renk-veya-Beden)
    [34] => SecenekBarkodu
    [35] => SecenekAd(Renk-veya-Beden)
    [36] => MatrisSecenekAd
    [37] => SecenekFiyat
    [38] => SecenekFiyatDavranis(0-ilaveEt;1-YazilanFiyatGecerli)
    [39] => SecenekStokAdet(Renk-veya-Beden)
    [40] => SecenekGrup
    [41] => SecenekSeciliGelsin
    [42] => SecenekZorunlu
    [43] => SecenekEntegrasyonKod
    [44] => SecenekStokKontrol
    [45] => Resim1
    [46] => Resim2
    [47] => Resim3
    [48] => Resim4
    [49] => Resim5
    [50] => Resim6
    [51] => Resim7
    [52] => Resim8
)
 */

include "app/Mage.php";
header('Content-type: text/plain; charset=utf-8');
Mage::app('admin');

$csv = array_map('str_getcsv', file('urunler.csv'));

// Renk Seçeneği
$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 161);
foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
	$renkler[$option['value']] = $option['label'];
}

// Beden Seçeneği
$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 162);
foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
	$bedenler[$option['value']] = $option['label'];
}

foreach ($csv as $key => $uruns) {
	if ($key > 0) {
		if ($uruns[15] == 1) {
    	$urunler[$uruns[0]]['sku'] = $uruns[0];
			$urunler[$uruns[0]]['name'] = $uruns[1];
			$urunler[$uruns[0]]['link'] = $uruns[2];
			$urunler[$uruns[0]]['renk'] = $uruns[12];
			$urunler[$uruns[0]]['kategori_id'] = $uruns[6];
			$urunler[$uruns[0]]['description'] = $uruns[14];
			$urunler[$uruns[0]]['special_price'] = $uruns[23];
			$urunler[$uruns[0]]['price'] = $uruns[24];
			$urunler[$uruns[0]]['stok'] = $uruns[30];
			$urunler[$uruns[0]]['beden'] = $uruns[35];
			$urunler[$uruns[0]]['resimler'][0] = $uruns[45];
			$urunler[$uruns[0]]['resimler'][1] = $uruns[46];
			$urunler[$uruns[0]]['resimler'][2] = $uruns[47];

		}
	}
}

foreach ($urunler as $urun) {
  foreach($bedenler as $key => $beden){
		if($beden == $urun['beden']){
			$urun['beden_attr'] = $key;
		}
		elseif($urun['beden'] == "2XL"){
			$urun['beden_attr'] = 49;
		}
	}

	if($urun['renk'] == "" && stripos($urun['name'],'Kahve')){
		$urun['renk'] = "Kahve";
	}
	elseif($urun['renk'] == "" && stripos($urun['name'],'Antrasit')){
		$urun['renk'] = "Antrasit";
	}
	elseif($urun['renk'] == "" && stripos($urun['name'],'Siyah')){
		$urun['renk'] = "Siyah";
	}

	foreach($renkler as $keyr => $renk){
		if($renk == $urun['renk']){
			$urun['renk_attr'] = $keyr;
		}
	}

	if(!isset($urun['beden_attr'])){
		$urun['beden_attr'] = 0;
	}

	if(!isset($urun['renk_attr'])){
		$urun['renk_attr'] = 0;
	}

	switch ($urun['kategori_id']) {
		case 7:
			$kategori = array(3, 5);
			break;
		case 6:
			$kategori = array(3, 6);
			break;
		case 36:
			$kategori = array(3, 6);
			break;
		case 62:
			$kategori = array(3, 6);
			break;
		case 64:
			$kategori = array(3, 6);
			break;
		case 8:
			$kategori = array(3, 7);
			break;
		case 10:
			$kategori = array(3, 8);
			break;
		case 9:
			$kategori = array(3, 9);
			break;
		case 22:
			$kategori = array(3, 15);
			break;
		case 23:
			$kategori = array(3, 16);
			break;
		case 34:
			$kategori = array(3, 17);
			break;
		case 56:
			$kategori = array(3, 17);
			break;
		case 38:
			$kategori = array(3, 18);
			break;
		case 5:
			$kategori = array(4, 10);
			break;
		case 65:
			$kategori = array(4, 10);
			break;
		case 4:
			$kategori = array(4, 11);
			break;
		case 3:
			$kategori = array(4, 12);
			break;
		case 12:
			$kategori = array(4, 13);
			break;
		case 25:
			$kategori = array(14);
			break;
		case 28:
			$kategori = array(14);
			break;
		case 29:
			$kategori = array(14);
			break;
		case 59:
			$kategori = array(14);
			break;

		default:
	}


	try {
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
		$product = Mage::getModel('catalog/product');
		$product
			->setWebsiteIds(array(1))
			->setAttributeSetId(4)
			->setTypeId('simple')
			->setCreatedAt(strtotime('now'))
			->setSku($urun['sku'])
			->setName($urun['name'])
			->setStatus(1)
			->setTaxClassId(4)
			->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)//catalog and search visibility
			->setBeden($urun['beden_attr'])
			->setRenk($urun['renk_attr'])
			->setPrice($urun['price'])
			->setSpecialPrice($urun['special_price'])
			->setMetaTitle($urun['name'])
			->setMetaDescription(strip_tags($urun['description']))
			->setDescription($urun['description'])
			->setShortDescription($urun['name'])
			->setMediaGallery(array('images' => array(), 'values' => array()))//media gallery initialization
			->setStockData(array(
					'use_config_manage_stock' => 0, //'Use config settings' checkbox
					'manage_stock' => 1, //manage stock
					'min_sale_qty' => 1, //Minimum Qty Allowed in Shopping Cart
					'max_sale_qty' => 2, //Maximum Qty Allowed in Shopping Cart
					'is_in_stock' => 1, //Stock Availability
					'qty' => $urun['stok'] //qty
				)
			)
			->setCategoryIds($kategori); //assign product to categories


		$resim1 = pathinfo($urun['resimler'][0]);
		$dir = Mage::getBaseDir('media') . DS;
		copy($urun['resimler'][0], $dir . $resim1['filename'] . $resim1['basename']);

		$resim2 = pathinfo($urun['resimler'][1]);
		$dir = Mage::getBaseDir('media') . DS;
		copy($urun['resimler'][1], $dir . $resim2['filename'] . $resim2['basename']);

		$resim3 = pathinfo($urun['resimler'][2]);
		$dir = Mage::getBaseDir('media') . DS;
		copy($urun['resimler'][2], $dir . $resim3['filename'] . $resim3['basename']);

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
					$product->addImageToMediaGallery($path, $imageType, false, false);
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			} else {
				echo "Can not find image by path: {$path}<br/>";
			}
		}


		$product->save();
		print $urun['sku'] . " Sorunsuz Eklendi!\n";

	} catch
	(Exception $e) {
		Mage::log($e->getMessage());
	}

}

