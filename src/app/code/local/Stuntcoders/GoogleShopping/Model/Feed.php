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
        if ( ! $this->getPath()) {
            $errors[] = Mage::helper('stuntcoders_googleshopping')->__('Path is mandatory');
        }

        return $errors;
    }

    public function generateXml()
    {
        Mage::app()->setCurrentStore($this->getStores());

        Mage::log('Start generating XML', Zend_Log::INFO, 'Stuntcoders_Googleshopping.log');

        try {
            $productCollection = Mage::getModel('catalog/product')
                                     ->getCollection()
                                     ->joinField(
                                         'category_id', 'catalog/category_product', 'category_id',
                                         'product_id = entity_id', null, 'left'
                                     )
                                     ->addStoreFilter($this->getStores())
                                     ->addAttributeToSelect('*')
                                     ->addAttributeToFilter('category_id', array('in' => explode(',', $this->getCategories())))
                                     ->addAttributeToFilter(
                                        'status',
                                        array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
                                     )
                                     ->groupByAttribute('entity_id');
        } catch (Exception $e) {
            Mage::log('Error creating product collection:', Zend_Log::ERR, 'Stuntcoders_Googleshopping_Error.log');
            Mage::log($e->getTraceAsString(), Zend_Log::ERR, 'Stuntcoders_Googleshopping_Error.log');
        }

        $doc               = new DOMDocument('1.0');
        $doc->formatOutput = true;

        $rss = $doc->appendChild($doc->createElement('rss'));
        $rss->setAttribute('xmlns:g', 'http://base.google.com/ns/1.0');
        $rss->setAttribute('version', '2.0');
        $channel = $rss->appendChild($doc->createElement('channel'));
        $channel->appendChild($doc->createElement('title', $this->getTitle()));
        $channel->appendChild($doc->createElement('link', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB)));
        $channel->appendChild($doc->createElement('description', $this->getDescription()));

        $attributes = json_decode($this->getAttributes(), true);

        Mage::log('Attributes to use:', Zend_Log::DEBUG, 'Stuntcoders_Googleshopping_Debug.log');
        Mage::log($this->getAttributes(), Zend_Log::DEBUG, 'Stuntcoders_Googleshopping_Debug.log');

        foreach ($productCollection as $product) {

            $price = $this->formatPriceForFeed($product->getPrice());

            if ($product->getPrice() != '0.0000') {

                $item = $channel->appendChild($doc->createElement('item'));

                foreach ($attributes as $name => $value) {
                    $tagValue    = isset($value['default']) ? $value['default'] : '';
                    $prefix      = '';
                    $valuePrefix = '';

                    $itemTag = false;
                    foreach ($value as $subName => $subValue) {
                        if (is_array($subValue)) {
                            $subTagValue = isset($subValue['default']) ? $subValue['default'] : '';

                            if ( ! empty($value['prefix'])) {
                                $prefix = $value['prefix'] . ':';
                            }

                            if ( ! $itemTag) {
                                $itemTag = $item->appendChild($doc->createElement($prefix . $name, ''));

                                if ( ! empty($value['type'])) {
                                    $itemTag->setAttribute('type', $value['type']);
                                }
                            }

                            if ( ! empty($value['attribute']) && $product->getData($value['attribute'])) {
                                $subTagValue = $product->getData($subValue['attribute']);
                            }

                            if (empty($subTagValue)) {
                                continue;
                            }

                            if ( ! empty($subValue['prefix'])) {
                                $prefix = $subValue['prefix'] . ':';
                            }

                            if ( ! empty($subValue['value_prefix'])) {
                                $valuePrefix = $subValue['value_prefix'];
                            }

                            $subItemTag = $itemTag->appendChild($doc->createElement($prefix . $subName));
                            $subItemTag->appendChild($doc->createCDATASection($valuePrefix . $subTagValue));

                            if ( ! empty($value['type'])) {
                                $subItemTag->setAttribute('type', $subValue['type']);
                            }
                        }
                    }

                    if ($name === 'product_type') {
                        $tagValue = $this->categorySubcategory($product);
                        $tagValue = str_replace('Default Category > ', '', $tagValue);
                    } else if ($name === 'google_product_category') {
                        $tagValue = $this->getGoogleCategory($product);

                    } else if ($name === 'sale_price') {
                        if ($product->getPrice() > $product->getFinalPrice()) {
                            $tagValue = $this->formatPriceForFeed($product->getFinalPrice());
                        }

                    } else if ( ! empty($value['attribute']) && $product->getData($value['attribute'])) {
                        $tagValue = $product->getData($value['attribute']);
                    }

                    if (empty($tagValue)) {
                        continue;
                    }

                    if ( ! empty($value['prefix'])) {
                        $prefix = $value['prefix'] . ':';
                    }

                    if ( ! empty($value['value_prefix'])) {
                        $valuePrefix = $value['value_prefix'];
                    }

                    $itemTag = $item->appendChild($doc->createElement($prefix . $name));
                    $itemTag->appendChild($doc->createCDATASection($valuePrefix . $tagValue));

                    if ( ! empty($value['type'])) {
                        $itemTag->setAttribute('type', $value['type']);
                    }
                }

                if ($product->getStockItem()->getIsInStock()) {
                    $item->appendChild($doc->createElement('g:availability', 'in stock'));
                } else {
                    $item->appendChild($doc->createElement('g:availability', 'out of stock'));
                }

                $item->appendChild($doc->createElement('g:link', $product->getProductUrl()));
                $item->appendChild($doc->createElement(
                    'g:image_link',
                    Mage::helper('catalog/image')->init($product, 'image')->resize(800)
                ));
                $item->appendChild($doc->createElement(
                    'g:price',
                    $price
                ));
            }
        }

        Mage::log('About creating feed with ' . $productCollection->count() . ' products.', Zend_Log::INFO, 'Stuntcoders_Googleshopping.log');

        return $doc->saveXML();
    }

    public function categorySubcategory($_product)
    {
        $level      = 1;
        $deepestId  = false;
        $categories = array();
        $response   = array();

        foreach ($_product->getCategoryCollection() as $category) {
            $category = Mage::getModel('catalog/category')->load($category->getId());
            if ($category->getIsActive()) {
                $path = $category->getPathIds();
                array_shift($path);
                $categories[$category->getId()] = array(
                    'name' => $category->getName(),
                    'path' => $path
                );
                if ((int)$category->getLevel() > $level) {
                    $deepestId = $category->getId();
                    $level     = (int)$category->getLevel();
                }
            }
        }

        if ( ! $deepestId) {
            return false;
        }

        foreach ($categories[$deepestId]['path'] as $id) {
            if (array_key_exists($id, $categories)) {
                $response[] = $categories[$id]['name'];
                //array_push($response, $categories[$id]['name']);
            }
        }
        $response = implode(" > ", $response);

        return $response;

    }

    public function getGoogleCategory($_product)
    {
        $level      = 1;
        $deepestId  = false;
        $categories = array();

        foreach ($_product->getCategoryCollection() as $category) {
            $category = Mage::getModel('catalog/category')->load($category->getId());
            if ($category->getIsActive()) {
                $categories[$category->getId()] = array(
                    'googleCategoryName' => $category->getGoogleCategory()
                );
                if ((int)$category->getLevel() > $level) {
                    $deepestId = $category->getId();
                    $level     = (int)$category->getLevel();
                }
            }
        }

        if ( ! $deepestId) {
            return false;
        }
        $googleCategory = $categories[$deepestId]['googleCategoryName'];
        if ( ! empty($googleCategory)) {
            return $googleCategory;
        }

        return false;
    }

    public function formatPriceForFeed($price)
    {

        $price = sprintf("%F", $price);
        $price = substr($price, 0, -4) . ' ' . Mage::app()->getStore()->getCurrentCurrencyCode();

        return $price;
    }

}
