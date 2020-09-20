<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace JMC\DynamicShipping\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Vendor\Module\Block\Adminhtml\Form\Field\TaxColumn;

/**
 * Dynamic Methods Row
 * @author Juan Manuel Cinto <juanmanuelcinto@gmail.com>
 */
class Methods extends AbstractFieldArray
{

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('name', ['label' => __('Name'), 'class' => 'required-entry']);
        $this->addColumn('price', ['label' => __('Price'), 'class' => 'required-entry', 'size' => 3]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
