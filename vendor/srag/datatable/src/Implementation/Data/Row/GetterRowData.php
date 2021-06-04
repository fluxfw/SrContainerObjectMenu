<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Implementation\Data\Row;

use srag\CustomInputGUIs\SrContainerObjectMenu\PropertyFormGUI\Items\Items;

/**
 * Class GetterRowData
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Implementation\Data\Row
 */
class GetterRowData extends AbstractRowData
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $key)
    {
        return Items::getter($this->getOriginalData(), $key);
    }
}
