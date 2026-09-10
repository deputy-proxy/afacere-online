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
        return app(CreateBusinessAction::class)->execute(auth()->user(), $data);
    }
}
