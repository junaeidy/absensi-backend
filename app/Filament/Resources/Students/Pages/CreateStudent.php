<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected array $parentRelations = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove user account fields from student data
        $createUserAccount = $data['create_user_account'] ?? false;
        $userEmail = $data['user_email'] ?? null;
        $userPassword = $data['user_password'] ?? null;
        
        // Store parent relations temporarily
        $parentRelations = $data['parentRelations'] ?? [];
        unset($data['parentRelations']);
        
        unset($data['create_user_account'], $data['user_email'], $data['user_password'], $data['user_name']);

        // Create User account if requested
        if ($createUserAccount && $userEmail) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $userEmail,
                'password' => Hash::make($userPassword ?? 'password123'),
                'role' => 'siswa',
            ]);

            // Link user_id to student
            $data['user_id'] = $user->id;
        }

        // Store parent relations for later
        $this->parentRelations = $parentRelations;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Attach parent relations with pivot data
        if (!empty($this->parentRelations)) {
            foreach ($this->parentRelations as $relation) {
                $this->record->parents()->attach($relation['parent_id'], [
                    'relation_type' => $relation['relation_type'],
                    'is_primary_contact' => $relation['is_primary_contact'] ?? false,
                ]);
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
