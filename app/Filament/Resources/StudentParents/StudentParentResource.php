<?php

namespace App\Filament\Resources\StudentParents;

use App\Filament\Resources\StudentParents\Pages\CreateStudentParent;
use App\Filament\Resources\StudentParents\Pages\EditStudentParent;
use App\Filament\Resources\StudentParents\Pages\ListStudentParents;
use App\Filament\Resources\StudentParents\Pages\ViewStudentParent;
use App\Filament\Resources\StudentParents\Schemas\StudentParentForm;
use App\Filament\Resources\StudentParents\Schemas\StudentParentInfolist;
use App\Filament\Resources\StudentParents\Tables\StudentParentsTable;
use App\Models\StudentParent;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StudentParentResource extends Resource
{
    protected static ?string $model = StudentParent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static UnitEnum|string|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Orang Tua';

    protected static ?string $modelLabel = 'Orang Tua';

    protected static ?string $pluralModelLabel = 'Orang Tua';

    public static function form(Schema $schema): Schema
    {
        return StudentParentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentParentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentParentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentParents::route('/'),
            'create' => CreateStudentParent::route('/create'),
            'view' => ViewStudentParent::route('/{record}'),
            'edit' => EditStudentParent::route('/{record}/edit'),
        ];
    }
}
