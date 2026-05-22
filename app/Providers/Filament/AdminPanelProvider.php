<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Navigation\NavigationItem;
use Filament\Enums\ThemeMode;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => Color::Indigo,
                'gray' => Color::Slate,
            ])
            ->font('Outfit')
            ->defaultThemeMode(ThemeMode::Dark)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationItems([
                NavigationItem::make('Manage Subscriptions')
                    ->url(fn (): string => route('admin.users.index'))
                    ->icon('heroicon-o-credit-card')
                    ->isActiveWhen(fn () => request()->routeIs('admin.users.index'))
                    ->sort(1),
                NavigationItem::make('Return to App')
                    ->url(fn (): string => route('dashboard'))
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->sort(2),
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('
                    <style>
                        /* Custom scrollbar matching dark navy theme */
                        ::-webkit-scrollbar {
                            width: 8px;
                            height: 8px;
                        }
                        ::-webkit-scrollbar-track {
                            background: #090d16;
                        }
                        ::-webkit-scrollbar-thumb {
                            background: #1e293b;
                            border-radius: 4px;
                        }
                        ::-webkit-scrollbar-thumb:hover {
                            background: #334155;
                        }

                        /* Core Page Layout Styling */
                        .fi-layout, .fi-main {
                            background-color: #090d16 !important;
                            background-image: radial-gradient(ellipse 80% 80% at 50% -20%, rgba(120, 119, 198, 0.25), rgba(255, 255, 255, 0)) !important;
                            color: #f1f5f9 !important;
                        }
                        
                        /* Sidebar Styling */
                        .fi-sidebar {
                            background-color: rgba(9, 13, 22, 0.8) !important;
                            backdrop-filter: blur(16px) !important;
                            border-right: 1px solid rgba(51, 65, 85, 0.4) !important;
                        }
                        
                        /* Topbar Header Styling */
                        .fi-topbar {
                            background-color: rgba(9, 13, 22, 0.8) !important;
                            backdrop-filter: blur(16px) !important;
                            border-bottom: 1px solid rgba(51, 65, 85, 0.4) !important;
                        }
                        
                        /* Card containers, sections, widgets, tables container */
                        .fi-ta-ctn, 
                        .fi-section, 
                        .fi-gpts, 
                        .fi-widget, 
                        .fi-modal-window, 
                        .fi-wi-stats-overview-card-ctn {
                            background-color: rgba(30, 41, 59, 0.3) !important;
                            backdrop-filter: blur(16px) !important;
                            border: 1px solid rgba(51, 65, 85, 0.4) !important;
                            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -4px rgba(0, 0, 0, 0.3) !important;
                            border-radius: 1rem !important;
                        }

                        /* Table body styling */
                        .fi-ta-content {
                            background-color: transparent !important;
                        }
                        
                        /* Headers inside sections */
                        .fi-section-header, .fi-ta-header {
                            border-bottom: 1px solid rgba(51, 65, 85, 0.4) !important;
                            background-color: rgba(15, 23, 42, 0.3) !important;
                            padding-top: 1rem !important;
                            padding-bottom: 1rem !important;
                        }

                        /* Table row hover and styling */
                        .fi-ta-record:hover {
                            background-color: rgba(51, 65, 85, 0.2) !important;
                        }
                        
                        /* Active sidebar navigation items */
                        .fi-sidebar-item-active > a {
                            background-image: linear-gradient(to right, rgba(34, 211, 238, 0.15), rgba(59, 130, 246, 0.05)) !important;
                            border-left: 3px solid #22d3ee !important;
                            color: #ffffff !important;
                        }
                    </style>
                ')
            );
    }
}
