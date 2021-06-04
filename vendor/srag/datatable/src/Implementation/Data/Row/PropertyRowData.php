<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Implementation\Data\Row;

/**
 * Class PropertyRowData
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Implementation\Data\Row
 */
class PropertyRowData extends AbstractRowData
{

    /**
     * @inheritDoc
     */
    public function __invoke(string $key)
    {
        return $this->getOriginalData()->{$key};
    }
}
