<?php

namespace App\Filament\Resources\SinglePasswordResource\Pages;

use App\Filament\Resources\SinglePasswordResource;
use App\Models\Reminder;
use App\Models\SinglePassword;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Exceptions\Halt;

class CreateSinglePassword extends CreateRecord
{
    protected static string $resource = SinglePasswordResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    // Step 1: Password Type
                    Wizard\Step::make('Password Type')
                        ->schema([
                            Placeholder::make('')->content('Select a template to start with.')
                                ->extraAttributes(['class' => 'justify-self-center font-normal']),
                            Select::make('password_type')
                                ->options([
                                    'Passcode' => 'Passcode',
                                    'Online Password' => 'Online Password',
                                ])
                                ->required()
                                ->reactive()
                                ->label(false),
                        ])
                        ->description('What type of password?'),

                    // Step 2: Nickname
                    Wizard\Step::make('Nickname')
                        ->schema([
                            Placeholder::make('')->content('Nicknames help when browsing or searching.')
                                ->extraAttributes(['class' => 'justify-self-center font-normal']),
                            TextInput::make('title')
                                ->label('Nickname')->required(),
                        ])
                        ->description('Nickname this password.'),

                    // Step 3: Username
                    Wizard\Step::make('Username')
                        ->schema([
                            TextInput::make('username')
                                ->label('Username')
                                ->dehydrateStateUsing(fn($state, $get) => $get('password_type') === 'Passcode' ? null : $state),
                        ])
                        ->hidden(fn ($get) => $get('password_type') === 'Passcode')
                        ->description('Add username.'),

                    // Step 4: Password
                    Wizard\Step::make('Password')
                        ->schema([
                            Placeholder::make('')->content('Safely store this information here so you can access it when you need it.')
                                ->extraAttributes(['class' => 'justify-self-center font-normal']),
                            Section::make()
                                ->schema([
                                    TextInput::make('password')
                                        ->placeholder('Password')
                                        ->password()
                                        ->revealable()
                                        ->inlineLabel(),
                                    TextInput::make('hint')
                                        ->label(false)
                                        ->inlineLabel()
                                        ->placeholder('Hint (Optional)'),
                                ]),
                        ])
                        ->description('Add password.'),

                    // Step 5: Website
                    Wizard\Step::make('Website')
                        ->schema([
                            Placeholder::make('')->content('Quickly access your account by including a link to the website this password is attached to.')
                                ->extraAttributes(['class' => 'justify-self-center font-normal']),
                            TextInput::make('website')
                                ->label(false)
                                ->suffixIcon('heroicon-m-globe-alt')
                                ->prefix('https://')
                                ->inlineLabel()
                                ->dehydrateStateUsing(fn($state, $get) => $get('password_type') === 'Passcode' ? null : $state)
                                ->formatStateUsing(fn($state) => preg_replace('/^https?:\/\//', '', $state))
                                ->dehydrateStateUsing(fn($state) => preg_match('/^https?:\/\//', $state) ? $state : 'https://' . $state),
                        ])
                        ->hidden(fn ($get) => $get('password_type') === 'Passcode')
                        ->description('Add website.'),

                    // Step 6: Security Questions
                    Wizard\Step::make('Security Questions')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Repeater::make('securityQuestions')
                                        ->relationship()
                                        ->schema([
                                            TextInput::make('question')->required()->placeholder('Question')->inlineLabel(),
                                            TextInput::make('answer')->required()->placeholder('Answer')->inlineLabel(),
                                            TextInput::make('hint')->placeholder('Hint (Optional)')->inlineLabel(),
                                        ])
                                        ->reorderable(false)
                                        ->collapsible()
                                        ->addActionLabel('Add Security Question'),
                                ]),
                        ])
                        ->description('Add security questions.'),

                    // Step 7: Additional files
                    Wizard\Step::make('Additional files')
                        ->schema([
                            Section::make()
                                ->schema([
                                    FileUpload::make('files'),
                                ]),
                        ])
                        ->description('Add additional files.'),

                    // Step 8: Notes
                    Wizard\Step::make('Notes')
                        ->schema([
                            Section::make()
                                ->schema([
                                    Textarea::make('notes')
                                        ->rows(10)
                                        ->cols(20),
                                ]),
                        ])
                        ->description('Add notes.'),
                ])
                    ->skippable()
                    ->columnSpanFull()
        ]);
    }
}
