<?php

use wcf\system\database\table\column\DefaultFalseBooleanDatabaseTableColumn;
use wcf\system\database\table\column\NotNullInt10DatabaseTableColumn;
use wcf\system\database\table\column\NotNullVarchar255DatabaseTableColumn;
use wcf\system\database\table\column\ObjectIdDatabaseTableColumn;
use wcf\system\database\table\DatabaseTable;
use wcf\system\database\table\index\DatabaseTableForeignKey;

return [
    DatabaseTable::create('wcf1_user_group_removal')
        ->columns([
            ObjectIdDatabaseTableColumn::create('removalID'),
            NotNullInt10DatabaseTableColumn::create('groupID'),
            NotNullVarchar255DatabaseTableColumn::create('title'),
            DefaultFalseBooleanDatabaseTableColumn::create('isDisabled'),
        ])
        ->foreignKeys([
            DatabaseTableForeignKey::create()
                ->columns(['groupID'])
                ->referencedTable('wcf1_user_group')
                ->referencedColumns(['groupID'])
                ->onDelete('CASCADE'),
        ]),
];
