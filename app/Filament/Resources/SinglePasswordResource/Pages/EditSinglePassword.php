<?php

namespace App\Filament\Resources\SinglePasswordResource\Pages;

use App\Filament\Resources\SinglePasswordResource;
use App\Models\Reminder;
use App\Models\SinglePassword;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\VerticalAlignment;

class EditSinglePassword extends EditRecord
{
    protected static string $resource = SinglePasswordResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
        ];
    }
    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save Changes')
                ->requiresConfirmation(fn (SinglePassword $record): bool =>
                    ($record->getOriginal('password_type') !== 'Passcode' && $record->password_type === 'Passcode') &&
                    ($record->username || $record->website)
                )
                ->modalHeading('Confirm Change')
                ->modalSubheading('Switching to "Passcode" will remove the username and website values. Do you want to proceed?')
                ->modalButton('Yes, proceed')
                ->action(function (SinglePassword $record) {
                    // Nullify the fields if the password type is now 'Passcode'
                    if ($record->password_type === 'Passcode') {
                        $record->username = null;
                        $record->website = null;
                    }

                    // Save the record after modification
                    $record->save();
                })
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Fieldset::make()->schema([
                            SpatieMediaLibraryFileUpload::make('image')
                                ->avatar()
                                ->extraAttributes(['class' => 'cursor-pointer'])
                                ->image()
                                ->collection('images')
                                ->acceptedFileTypes(['image/*'])
                                ->label(false)
                                ->maxFiles(1)
                                ->responsiveImages()
                                ->deletable()
                                ->openable(),
                            Grid::make()->schema([
                                TextInput::make('title'),
//                            Forms\Components\Placeholder::make('password_type')
                            ])->columnSpan(1)->columns(1),
                        ])->columns(3)->columnSpan(1),
                    ]),
                Section::make()->schema([
                    Section::make()
                        ->schema([
                            Select::make('password_type')
                                ->options([
                                    'Passcode' => 'Passcode',
                                    'Online Password' => 'Online Password',
                                ])
                                ->label('Password Type')
                                ->inlineLabel()
                                ->reactive(),
                        ]),
                    Section::make()
                        ->schema([
                            TextInput::make('username')
                                ->inlineLabel()
                                ->dehydrateStateUsing(fn ($state, $get) => $get('password_type') === 'Passcode' ? null : $state),
                        ])
                        ->visible(fn ($get) => $get('password_type') === 'Online Password'), // Visible only if 'Online Password' is selected,
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
                    Section::make()
                        ->schema([
                            TextInput::make('website')
                                ->suffixIcon('heroicon-m-globe-alt')
                                ->prefix('https://')
                                ->inlineLabel()
                                ->dehydrateStateUsing(fn ($state, $get) => $get('password_type') === 'Passcode' ? null : $state)
                                // Remove prefix when displaying the input
                                ->formatStateUsing(fn($state) => preg_replace('/^https?:\/\//', '', $state))
                                // Add prefix if missing when saving the input
                                ->dehydrateStateUsing(fn($state) => preg_match('/^https?:\/\//', $state) ? $state : 'https://' . $state),
                        ])
                        ->visible(fn ($get) => $get('password_type') === 'Online Password'), // Visible only if 'Online Password' is selected,
                    Section::make()
                        ->schema([
                            Repeater::make('securityQuestions')
                                ->relationship()
                                ->schema([
                                    TextInput::make('question')->required(),
                                    TextInput::make('answer')->required(),
                                    TextInput::make('hint')->required(),
                                ])
                                ->default(fn($get) => $get('members'))
                                ->reorderable(false)
                                ->collapsible()
                                ->addActionLabel('Add Security Question')
                                ->columns(1)
                        ])
                        ->inlineLabel()->visible(fn ($get) => $get('password_type') === 'Online Password')
                        ->dehydrateStateUsing(fn ($state, $get) => $get('password_type') === 'Passcode' ? null : $state),
                    Section::make()
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('files')
                                ->collection('files')
                                ->multiple()
                                ->openable()
                                ->panelLayout('grid')
                                ->downloadable()
                                ->previewable()
                                ->deletable()
//                                    ->reorderable()
                                ->extraAttributes(['class' => 'cursor-pointer'])
                        ]),
                    Section::make()
                        ->schema([
                            Textarea::make('notes')
                                ->rows(5)
                                ->placeholder('Type a new note...'),
                        ]),
                ])->columnSpan(1),
                Section::make()->schema([
                    Section::make()
                        ->schema([
                            Placeholder::make('Reminders'),
                            \Filament\Forms\Components\Actions::make([
                                Action::make('create_reminder')
                                    ->label(false)
                                    ->icon('heroicon-m-plus')
                                    ->modal()
                                    ->form([
                                        TextInput::make('name')->label('Reminder Name')->required(),
                                        Select::make('reminder_type')->label('Reminder Type')->options([
                                            'one_time' => 'One Time',
                                            'recurring' => 'Recurring',
                                        ])->required(),
                                        Select::make('due_date')->label('Due Date')->options([
                                            'next_week' => 'Next Week',
                                            'in_1_month' => 'In 1 Month',
                                            'in_3_months' => 'In 3 Months',
                                            'in_1_year' => 'In 1 Year',
                                        ])->required(),
                                        Select::make('repeat')->label('Repeat Frequency')->options([
                                            'every_week' => 'Every Week',
                                            'every_month' => 'Every Month',
                                            'every_3_months' => 'Every 3 Months',
                                            'every_year' => 'Every Year',
                                        ])->visible(fn($get) => $get('reminder_type') === 'recurring'), // Visible only for recurring reminders
                                    ])->action(function (array $data, SinglePassword $record) {
                                        // Create the new Reminder
                                        $reminder = Reminder::create([
                                            'name' => $data['name'],
                                            'reminder_type' => $data['reminder_type'],
                                            'remind_at' => now()->addWeek(), // Example due date calculation
                                            'status' => 1,
                                            'expires_on' => now()->addMonths(3), // Example expiry calculation
                                        ]);

                                        // Attach the Reminder to the current SinglePassword
                                        $record->reminders()->attach($reminder);

                                        \Filament\Notifications\Notification::make()
                                            ->title('Reminder Created')
                                            ->body('The reminder has been successfully created')
                                            ->success()
                                            ->send();
                                    }),

                            ])->verticalAlignment(VerticalAlignment::End),

                            View::make('components.reminders-list')->columnSpanFull(),
                        ])->columns(2),
                    Section::make()
                        ->schema([
                            Placeholder::make('Contacts'),
                            Actions::make([
                                Action::make('add_contact')
                                    ->label(false)
                                    ->icon('heroicon-m-plus')
                                    ->modal()
                                    ->form([
                                        Select::make('contact_id')
                                            ->relationship('contacts', 'name', function ($query, $record) {
                                                if ($record) {
                                                    // Exclude contacts already attached to the SinglePassword object
                                                    $query->whereDoesntHave('singlePasswords', function ($subQuery) use ($record) {
                                                        $subQuery->where('single_passwords.id', $record->id);
                                                    });
                                                }
                                            })
                                            ->createOptionForm([
                                                TextInput::make('name')->required(),
                                                TextInput::make('email')->required(),
                                            ]),
                                    ])->action(function (array $data, SinglePassword $record) {
                                        if (isset($data['contact_id'])) {
                                            // Check if the contact_id is already attached
                                            $existingContact = $record->contacts()->where('contacts.id', $data['contact_id'])->exists();

                                            // Only attach if the contact_id isn't already linked to this SinglePassword
                                            if (!$existingContact) {
                                                $record->contacts()->attach($data['contact_id']);
                                            }
                                        }

                                        \Filament\Notifications\Notification::make()
                                            ->title('Contact Attached')
                                            ->body('This contact has been successfully attached')
                                            ->success()
                                            ->send();
                                    })
                            ]),
                            View::make('components.contacts-list')
                                ->columnSpanFull()
                        ])->columns(2),
                ])->columnSpan(1)->columns(2),
            ])->columns(2);
    }

    public function detachContact($contactId)
    {
        $record = $this->record; // Access the current SinglePassword record
        if ($record) {
            $record->contacts()->detach($contactId); // Detach the contact
        }

        // Optionally send a success notification
        \Filament\Notifications\Notification::make()
            ->title('Contact Detached')
            ->body('The contact has been successfully detached.')
            ->success()
            ->send();

    }

    public function detachReminder($reminderId)
    {
        $record = $this->record; // Access the current SinglePassword record

        if ($record) {
            // Detach the reminder
            $record->reminders()->detach($reminderId);

            // Delete the reminder after detachment
            $reminder = \App\Models\Reminder::find($reminderId); // Replace with your actual Reminder model's namespace
            if ($reminder) {
                $reminder->delete();
            }
        }

        // Send a success notification
        \Filament\Notifications\Notification::make()
            ->title('Reminder Detached and Deleted')
            ->body('The reminder has been successfully detached and deleted.')
            ->success()
            ->send();
    }
}
