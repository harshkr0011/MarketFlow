<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Action;

class PromptEngineeringLab extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    protected static string $view = 'filament.pages.prompt-engineering-lab';
    
    protected static ?string $navigationGroup = 'AI Playground';

    public ?array $data = [];

    public function mount(): void
    {
        // In a real app, this would load from a database settings table or config
        $this->form->fill([
            'system_prompt' => "You are an expert copywriter. Write highly converting sales copy tailored for the Indian market.",
            'temperature' => 0.7,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('system_prompt')
                    ->label('AI Copywriter System Prompt')
                    ->rows(6)
                    ->required()
                    ->helperText('This prompt is prepended to all user requests behind the scenes.'),
                
                TextInput::make('temperature')
                    ->numeric()
                    ->step(0.1)
                    ->min(0)
                    ->max(2)
                    ->required()
                    ->helperText('Higher values make the output more random. Lower values make it more deterministic.'),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        // Here we would save to our settings model/table
        // Settings::set('ai_system_prompt', $data['system_prompt']);
        
        $this->getNotificationManager()->success('Prompt settings updated successfully!');
    }
}
