<?php
/**
 * @copyright  Copyright (c) 2018 OlyThy <olythy@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php  The MIT License
 */

use LukeSnowden\GoogleShoppingFeed\Containers\GoogleShopping;

class Stuntcoders_GoogleShopping_Model_System_Config_Source_Googlecategory extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $lang = 'dk';

    public function __construct($lang = null)
    {
        $this->lang = $lang ?: 'dk';
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    public function toOptionArray()
    {
        $categories = GoogleShopping::categories($this->lang);

        $options = array();

        foreach ($categories as $category) {
            $options[] = array(
                'label' => $category,
                'value' => $category
            );
        }

        return $options;
    }
}
