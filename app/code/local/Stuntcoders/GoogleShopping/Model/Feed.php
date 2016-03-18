<?php

class Stuntcoders_GoogleShopping_Model_Feed extends Mage_Core_Model_Abstract
{
	protected function _construct()
	{
		$this->_init('stuntcoders_googleshopping/feed');
	}

	public function validate()
	{
		$errors = array();
		if (!$this->getPath()) {
			$errors[] = Mage::helper('stuntcoders_googleshopping')->__('Path is mandatory');
		}

		return $errors;
	}

	public function generateXml()
	{
		$productCollection = Mage::getModel('catalog/product')
		                         ->getCollection()
		                         ->joinField(
			                         'category_id', 'catalog/category_product', 'category_id',
			                         'product_id = entity_id', null, 'left'
		                         )
		                         ->addAttributeToSelect('*')
		                         ->addAttributeToFilter('category_id', array('in' => explode(',', $this->getCategories())))
		                         ->groupByAttribute('entity_id');

		$doc = new DOMDocument('1.0');
		$doc->formatOutput = true;

		$rss = $doc->appendChild($doc->createElement('rss'));
		$rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
		$rss->setAttribute('version', '2.0');
		$channel = $rss->appendChild($doc->createElement('channel'));
		$channel->appendChild($doc->createElement('title', $this->getTitle()));
		$channel->appendChild($doc->createElement('link', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)));
		$channel->appendChild($doc->createElement('description', $this->getDescription()));

		$attributes = json_decode($this->getAttributes(), true);
		foreach ($productCollection as $product) {
			$item = $channel->appendChild($doc->createElement('item'));

			foreach ($attributes as $name => $value) {
				$tagValue = isset($value['default']) ? $value['default'] : '';
				$prefix = '';
				$valuePrefix = '';

				$itemTag = false;
				foreach($value as $subName => $subValue) {
					if(is_array($subValue)){
						$subTagValue = isset($subValue['default']) ? $subValue['default'] : '';

						if (!empty($value['prefix'])) {
							$prefix = $value['prefix'] . ':';
						}

						if(!$itemTag) {
							$itemTag = $item->appendChild($doc->createElement($prefix . $name, ''));

							if (!empty($value['type'])) {
								$itemTag->setAttribute('type', $value['type']);
							}
						}

						if (!empty($value['attribute']) && $product->getData($value['attribute'])) {
							$subTagValue = $product->getData($subValue['attribute']);
						}

						if (empty($subTagValue)) {
							continue;
						}

						if (!empty($subValue['prefix'])) {
							$prefix = $subValue['prefix'] . ':';
						}

						if (!empty($subValue['value_prefix'])) {
							$valuePrefix = $subValue['value_prefix'];
						}

						$subItemTag = $itemTag->appendChild($doc->createElement($prefix . $subName, '<![CDATA[' . $valuePrefix . $subTagValue . ']]>');

						if (!empty($value['type'])) {
							$subItemTag->setAttribute('type', $subValue['type']);
						}
					}
				}

				if (!empty($value['attribute']) && $product->getData($value['attribute'])) {
					$tagValue = $product->getData($value['attribute']);
				}

				if (empty($tagValue)) {
					continue;
				}

				if (!empty($value['prefix'])) {
					$prefix = $value['prefix'] . ':';
				}

				if (!empty($value['value_prefix'])) {
					$valuePrefix = $value['value_prefix'];
				}

				$itemTag = $item->appendChild($doc->createElement($prefix . $name, '<![CDATA[' . $valuePrefix . $tagValue . ']]>'));

				if (!empty($value['type'])) {
					$itemTag->setAttribute('type', $value['type']);
				}
			}

			if($product->isInStock()) {
				$item->appendChild($doc->createElement('g:availability', 'in stock'));
			}
			else {
				$item->appendChild($doc->createElement('g:availability', 'out of stock'));
			}

			$item->appendChild($doc->createElement('g:link', $product->getUrlInStore()));
			$item->appendChild($doc->createElement(
				'g:image_link',
				Mage::helper('catalog/image')->init($product, 'image')->resize(800)
			));
			$item->appendChild($doc->createElement(
				'g:price',
				Mage::helper('core')->currency($product->getPrice(), true, false)
			));
		}

		return $doc->saveXML();
	}
}
