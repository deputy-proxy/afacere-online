<?php

namespace App\Filament\Resources\BusinessResource\Pages;

use App\Actions\CreateBusiness as CreateBusinessAction;
use App\Filament\Resources\BusinessResource;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;

class CreateBusiness extends CreateRecord
{
    protected static string $resource = BusinessResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateBusinessAction::class)->execute(auth()->user(), $data);
    }
}
