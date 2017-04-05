<?php

$product = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
				if ($product) {
					$productId = $product->getId();
					$stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
					$stockItemId = $stockItem->getId();
					$stock = array();
					if (!$stockItemId) {
						$stockItem->setData('product_id', $product->getId());
						$stockItem->setData('stock_id', 1);
					}
					else {
						$stock = $stockItem->getData();
					}


					$stock['qty'] = $qty;
					$stock['is_in_stock'] = $is_in_stock;
					$stock['manage_stock'] = 1;


					foreach($stock as $field => $value) {
						$stockItem->setData($field, $value ? $value : 0);
					}

					$stockItem->save();
					unset($stockItem);
					unset($product);
				}

				print $sku." Güncellendi!";
