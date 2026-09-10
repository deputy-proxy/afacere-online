<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Actions\CreateBusiness as CreateBusinessAction;
use App\Filament\Resources\BusinessResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBusiness extends CreateRecord
{
    protected static string $resource = BusinessResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        /** @var array{name:string, description?:string|null, website?:string|null} $attributes */
        $attributes = [
            'name' => (string) $data['name'],
            'description' => isset($data['description']) ? (string) $data['description'] : null,
        ];

        return app(CreateBusinessAction::class)->execute(auth()->user(), $attributes);
    }
}
