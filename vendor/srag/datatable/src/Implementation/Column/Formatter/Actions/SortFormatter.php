<?php

namespace srag\DataTableUI\SrContainerObjectMenu\Implementation\Column\Formatter\Actions;

use srag\CustomInputGUIs\SrContainerObjectMenu\Waiter\Waiter;
use srag\DataTableUI\SrContainerObjectMenu\Component\Column\Column;
use srag\DataTableUI\SrContainerObjectMenu\Component\Column\Formatter\Actions\ActionsFormatter;
use srag\DataTableUI\SrContainerObjectMenu\Component\Data\Row\RowData;
use srag\DataTableUI\SrContainerObjectMenu\Component\Format\Format;
use srag\DataTableUI\SrContainerObjectMenu\Component\Table;
use srag\DataTableUI\SrContainerObjectMenu\Implementation\Column\Formatter\DefaultFormatter;

/**
 * Class SortFormatter
 *
 * @package srag\DataTableUI\SrContainerObjectMenu\Implementation\Column\Formatter\Actions
 */
class SortFormatter extends DefaultFormatter implements ActionsFormatter
{

    /**
     * @inheritDoc
     */
    public function formatRowCell(Format $format, $actions, Column $column, RowData $row, string $table_id) : string
    {
        if (self::version()->is6()) {
            $glyph_factory = self::dic()->ui()->factory()->symbol()->glyph();
        } else {
            $glyph_factory = self::dic()->ui()->factory()->glyph();
        }

        return self::output()->getHTML([
            $glyph_factory->sortAscending()->withAdditionalOnLoadCode(function (string $id) use ($format, $row, $column, $table_id) : string {
                Waiter::init(Waiter::TYPE_WAITER/*, null, $component->getPlugin()*/); // TODO: Pass $component

                return '
            $("#' . $id . '").click(function () {
                il.waiter.show();
                var row = $(this).parent().parent();
                $.ajax({
                    url: ' . json_encode($format->getActionUrlWithParams($row($column->getKey() . "_up_action_url"), [Table::ACTION_GET_VAR => $row->getRowId()], $table_id)) . ',
                    type: "GET"
                 }).always(function () {
                    il.waiter.hide();
               }).success(function() {
                    row.insertBefore(row.prev());
                });
            });';
            }),
            $glyph_factory->sortDescending()->withAdditionalOnLoadCode(function (string $id) use ($format, $row, $column, $table_id) : string {
                return '
            $("#' . $id . '").click(function () {
                il.waiter.show();
                var row = $(this).parent().parent();
                $.ajax({
                     url: ' . json_encode($format->getActionUrlWithParams($row($column->getKey() . "_down_action_url"), [Table::ACTION_GET_VAR => $row->getRowId()], $table_id)) . ',
                    type: "GET"
                }).always(function () {
                    il.waiter.hide();
                }).success(function() {
                    row.insertAfter(row.next());
                });
        });';
            })
        ]);
    }
}
