<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SinglePasswordResource\Pages;
use App\Filament\Resources\SinglePasswordResource\RelationManagers;
use App\Models\Contact;
use App\Models\Reminder;
use App\Models\SinglePassword;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
class SinglePasswordResource extends Resource
{
    protected static ?string $model = SinglePassword::class;


    protected static ?string $navigationGroup = 'Passwords';
    protected static ?string $navigationIcon = 'heroicon-s-key';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Tables\Columns\TextColumn::make('title')->weight(FontWeight::Bold)->size(TextColumnSize::Medium)
                        ->lineClamp(2),
                    SpatieMediaLibraryImageColumn::make('image')
                        ->size(70)
                        ->collection('images')
                        ->circular()
                        ->alignEnd(),
                ])->extraAttributes(['class' => 'mb-4']),
                Stack::make([
                    Tables\Columns\TextColumn::make('password_type')->size('3xl'),
                    Tables\Columns\TextColumn::make('updated_at')->prefix('Last updated' . ' ')
                        ->extraAttributes([
                        'class' => 'text-2xl',
                    ])->since(),
                ]),

            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-horizontal'),
            ])
            ->contentGrid([
                'md' => 3,
                'xl' => 3,
            ])
            ->filters([
                //
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ]);
    }

    public static function rules(): array
    {
        return [
            'password_type' => ['required', 'in:Passcode,Online Password'],
            'username' => ['nullable', 'exclude_if:password_type,Passcode'],
            'website' => ['nullable', 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', 'exclude_if:password_type,Passcode'],
        ];
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
            'index' => Pages\ListSinglePasswords::route('/'),
            'create' => Pages\CreateSinglePassword::route('/create'),
            'edit' => Pages\EditSinglePassword::route('/{record}/edit'),
        ];
    }
}
