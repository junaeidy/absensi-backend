<?php

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use App\Models\School;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSchool extends ViewRecord
{
    protected static string $resource = SchoolResource::class;

    public function mount(int|string|null $record = null): void
    {
        // Jika tidak ada record yang diberikan, ambil school pertama
        if (! $record) {
            $school = School::first();
            if ($school) {
                $record = $school->getKey();
            }
        }

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
