<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetResource\Pages;
use App\Filament\Admin\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\TextInput::make('file_path')
                    ->required(),
                Forms\Components\Textarea::make('tags')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('agency_id')
                    ->numeric(),
                Forms\Components\Toggle::make('is_global')
                    ->required(),
                Forms\Components\TextInput::make('price_tier')
                    ->required(),
                Forms\Components\TextInput::make('version_major')
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('version_minor')
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('parent_asset_id')
                    ->relationship('parentAsset', 'title')
                    ->nullable(),
                Forms\Components\Textarea::make('customized_fields_json')
                    ->label('Customized Fields (JSON)')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('expires_at'),
                Forms\Components\TextInput::make('territory_restriction'),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('agency_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_global')
                    ->boolean(),
                Tables\Columns\TextColumn::make('price_tier')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
