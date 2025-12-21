<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected array $parentRelations = [];

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load existing user data if exists
        if ($this->record->user_id) {
            $user = User::find($this->record->user_id);
            if ($user) {
                $data['create_user_account'] = true;
                $data['user_email'] = $user->email;
                $data['user_name'] = $user->name;
                // Don't show password for security
            }
        }

        // Load parent relations
        $data['parentRelations'] = $this->record->parents->map(function ($parent) {
            return [
                'parent_id' => $parent->id,
                'relation_type' => $parent->pivot->relation_type,
                'is_primary_contact' => $parent->pivot->is_primary_contact,
            ];
        })->toArray();

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $createUserAccount = $data['create_user_account'] ?? false;
        $userEmail = $data['user_email'] ?? null;
        $userPassword = $data['user_password'] ?? null;
        
        // Store parent relations temporarily
        $parentRelations = $data['parentRelations'] ?? [];
        unset($data['parentRelations']);
        
        unset($data['create_user_account'], $data['user_email'], $data['user_password'], $data['user_name']);

        // If user account should be created/updated
        if ($createUserAccount && $userEmail) {
            if ($this->record->user_id) {
                // Update existing user
                $user = User::find($this->record->user_id);
                if ($user) {
                    $user->email = $userEmail;
                    $user->name = $data['name'];
                    if ($userPassword) {
                        $user->password = Hash::make($userPassword);
                    }
                    $user->save();
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $userEmail,
                    'password' => Hash::make($userPassword ?? 'password123'),
                    'role' => 'siswa',
                ]);
                $data['user_id'] = $user->id;
            }
        } elseif (!$createUserAccount && $this->record->user_id) {
            // If toggle is off, remove user account
            $user = User::find($this->record->user_id);
            if ($user) {
                $user->delete();
            }
            $data['user_id'] = null;
        }

        // Store parent relations for later
        $this->parentRelations = $parentRelations;

        return $data;
    }

    protected function afterSave(): void
    {
        // Sync parent relations with pivot data
        $syncData = [];
        foreach ($this->parentRelations as $relation) {
            $syncData[$relation['parent_id']] = [
                'relation_type' => $relation['relation_type'],
                'is_primary_contact' => $relation['is_primary_contact'] ?? false,
            ];
        }
        $this->record->parents()->sync($syncData);
    }
}
