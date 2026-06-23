<?php

namespace App\Filament\Resources\Transfers\Pages;

use App\Filament\Resources\Transfers\TransferResource;
use App\Models\Branch;
use App\Models\Transfer;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\View\PanelsRenderHook;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransferResource::class;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public function mount(): void
    {
        parent::mount();

        $this->createForm->fill();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function createForm(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('New transfer')
                    ->description('Request stock to move from one branch to another.')
                    ->icon(Heroicon::OutlinedArrowsRightLeft)
                    ->schema([
                        Grid::make()
                            ->columns(['default' => 1, 'sm' => 2])
                            ->schema([
                                Select::make('from_branch_id')
                                    ->label('From branch')
                                    ->placeholder('Select origin branch')
                                    ->options(fn (): array => Branch::query()->orderBy('name')->pluck('name', 'id')->all())
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('to_branch_id', null)),
                                Select::make('to_branch_id')
                                    ->label('To branch')
                                    ->placeholder('Select destination branch')
                                    ->options(fn (Get $get): array => Branch::query()
                                        ->when($get('from_branch_id'), fn ($query, $id) => $query->whereKeyNot($id))
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                        Textarea::make('notes')
                            ->label('Notes')
                            ->placeholder('Optional note for the receiving branch…')
                            ->rows(2)
                            ->columnSpanFull(),
                        Actions::make([
                            Action::make('create')
                                ->label('New transfer')
                                ->icon(Heroicon::OutlinedPlus)
                                ->action(fn () => $this->create()),
                        ])->alignEnd(),
                    ]),
            ]);
    }

    public function create(): void
    {
        $data = $this->createForm->getState();

        Transfer::create([
            ...$data,
            'requested_by' => auth()->id(),
            'status' => 'pending',
        ]);

        $this->createForm->fill();

        Notification::make()
            ->title('Transfer requested')
            ->success()
            ->send();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                EmbeddedSchema::make('createForm'),
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
