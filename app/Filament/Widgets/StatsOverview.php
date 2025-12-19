<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\RSLApp;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // 1. Total Semua Surat
            Stat::make('Archive Mail', RSLApp::count())
                ->description('All Archive Mails')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('primary'),

            // 2. Surat Masuk (Incoming)
            Stat::make('Today Incoming', RSLApp::where('mail_type', 'incoming')->where('date', today())->count())
                ->description('Today`s Incoming Mail')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'), // Hijau

            // 3. Surat Keluar (Outgoing)
            Stat::make('Today Outgoing', RSLApp::where('mail_type', 'outgoing')->where('date', today())->count())
                ->description('Today`s Outgoing Mail')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('warning'), // Kuning/Oranye

            Stat::make('Purchasing', RSLApp::where('subject1', 'purchasing')->count())
                ->description('All Purchasing Mails')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('Non-Purchasing', RSLApp::where('subject1', 'non purchasing')->count())
                ->description('All Non-Purchasing Mails')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('gray'),
        ];
    }
}
