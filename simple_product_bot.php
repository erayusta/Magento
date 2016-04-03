<?php
/*
Excel Formatı:
Conf ID | Beden 36 | Beden 38 ..... | Beden 54 | Renk Conf 1 | Renk Conf 2 | Renk Conf 3
*/
ini_set('display_errors', '1');
error_reporting(E_ALL);

include "app/Mage.php";
header('Content-type: text/plain; charset=utf-8');
Mage::app();


// CSV de yer alan conf product id lerini diziye atar
$csv = array_map('str_getcsv', file('export.csv'));

// Beden attribute unu değer ve value olarak diziye doldurur.
$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 162);
foreach ( $attribute->getSource()->getAllOptions(true, true) as $option){
	$bedenler[$option['label']] = $option['value'];
}

// Ürün id lerini atamak için sayaç
$i = 2225;

foreach($csv as $key => $conf) {
// Csv den okuduğu datada ilk satırı atlar, duruma göre $key<200 gibi değer verip istenen aralığı alırız
	if ($key > 0) {
	
		$conf_id = $conf[0]; // Excel de yer alan ID sütunu
		$simple = array();
		$simple[] = $conf[3]; // Simple ürün oluşturacağımız beden attribute u foreach ilede yapılabilirdi.
		$simple[] = $conf[4];
		$simple[] = $conf[5];
		$simple[] = $conf[6];
		$simple[] = $conf[7];
		$simple[] = $conf[8];
		$simple[] = $conf[9];
		$simple[] = $conf[10];
		$simple[] = $conf[11];
		$simple[] = $conf[12];
		$simple[] = $conf[13];
		$simple[] = $conf[14];
		$simple[] = $conf[15];
		$simple[] = $conf[16];
		$simple[] = $conf[17];
		$urunrenk = array();
		$urunrenk[] = $conf[18]; // Ekstra geliştirdiğimiz ürün - renk ilişkisi için konulan değerleri dizide topluyor
		$urunrenk[] = $conf[19];
		$urunrenk[] = $conf[20];

		$conf_product = Mage::getModel('catalog/product')->load($conf_id); // Configurable ürünü toplar
		if ($conf_product->getYenisezon() == 0) { // Ürün attribute unda sitede yeni sezon value ları toplanır.
			$yenisezon = 70;
		} else {
			$yenisezon = 71;
		}


    // düşük fiyatı indirimli olarak koyar
		if($conf_product->getPrice() < $conf_product->getSpecialPrice()){
			$special_price = $conf_product->getPrice();
			$price = $conf_product->getSpecialPrice();
		}
		else{
			$special_price = $conf_product->getSpecialPrice();
			$price = $conf_product->getPrice();
		}

		$data = array();
		foreach ($simple as $pro) { // Biriktirdiğimiz simple ürün dizisi için ürün oluşturacağız.
			if ($pro != 0) { // Olmayan bedenler için 0 değeri atanmıştı
				try {
					$product = Mage::getModel('catalog/product'); 
					$product
						->setWebsiteIds(array(1))
						->setAttributeSetId(4)
						->setTypeId('simple')//product type
						->setId($i++)
						->setCreatedAt(strtotime('now'))//product creation time
						->setUpdatedAt(strtotime('now'))//product update time
						->setSku($conf_product->getSku() . "-" . $pro) //SKU da magento panelden oluştururken -AttrbiuteCode koyar
						->setName($conf_product->getName()) //product name
						->setStatus(1)//product status (1 - enabled, 2 - disabled)
						->setTaxClassId(4)//tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
						->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)//catalog and search visibility
						->setPrice($price)//price in form 11.22
						->setSpecialPrice($special_price)//special price in form 11.22
						->setSpecialFromDate()//special price from (MM-DD-YYYY)
						->setSpecialToDate()//special price to (MM-DD-YYYY)
						->setMetaTitle($conf_product->getName())
						->setMetaKeyword('')
						->setRenk($conf_product->getRenk())
						->setBeden($bedenler[$pro])
						->setYeni_sezon($yenisezon)
						->setMetaDescription($conf_product->getMetaDescription())
						->setDescription($conf_product->getDescription())
						->setShortDescription($conf_product->getShortDescription())
						->setStockData(array(
								'use_config_manage_stock' => 0, //'Use config settings' checkbox
								'manage_stock' => 1, //manage stock
								'is_in_stock' => 1, //Stock Availability
								'qty' => 5 // Standart stok verdik dilenirse excelde bir sütuna da stok ataması verilebilir.
							)
						)
						->setCategoryIds($conf_product->getCategoryIds()) //assign product to categories
					;

					$product->save();

					echo $product->getId() . ' success    '; // Çıktıyı görmek için koyduk

          // Configurable ürüne bağlayacağımız attribute a göre dolduruyoruz. Beden Attribute ID: 162 
          //$data[Simple ID] = array('0'=>array('attribute_id'=>'162','label'=>Etiket,'value_index'=>Attrbite değer id si,'is_percent'=>0,'pricing_value'=>''));
					$data[$product->getId()] = array('0'=>array('attribute_id'=>'162','label'=>$pro,'value_index'=>$bedenler[$pro],'is_percent'=>0,'pricing_value'=>''));


				} catch (Exception $e) {
					Mage::log($e->getMessage());
					echo $e->getMessage();
					exit;
				}
			}
		}

    // Simple ürünleri oluşturduk şimdi configurable ürüne bağlayacağız
		$conf_product->setConfigurableProductsData($data);
		$conf_product->setCanSaveConfigurableAttributes(1);


    // Kendi geliştirdiğimiz renkleri bağlamak için dataları belirliyoruz
    $param = array();
		if($urunrenk[0] != ""){
			$param[$urunrenk[0]] = array('position' => 0);
		}
		if($urunrenk[1] != ""){
			$param[$urunrenk[1]] = array('position' => 1);
		}
		if($urunrenk[2] != ""){
			$param[$urunrenk[2]] = array('position' => 2);
		}
		
		// Excel sütunda renk bağlanmayacak ürünlerde olduğu için kontrol ediyoruz.
    if(isset($param)){
      $conf_product->setCustomLinkData($param);
    }
    
		try{
			$conf_product->save();

			echo  "  ".$conf_product->getId()." ::: ".$key." -Başarılı!- \n";
		}
		catch (Exception $e){
			echo " ". $key." exception:".$e."  ";
			exit;
		}


	}
}
