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
            Stat::make('Total Arsip Surat', RSLApp::count())
                ->description('Semua surat tercatat')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('primary'),

            // 2. Surat Masuk (Incoming)
            Stat::make('Surat Masuk Hari ini', RSLApp::where('mail_type', 'incoming')->where('date', today())->count())
                ->description('Total Incoming Today')
                ->descriptionIcon('heroicon-m-arrow-down-tray')
                ->color('success'), // Hijau

            // 3. Surat Keluar (Outgoing)
            Stat::make('Surat Keluar Hari ini', RSLApp::where('mail_type', 'outgoing')->where('date', today())->count())
                ->description('Total Outgoing Today')
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('warning'), // Kuning/Oranye

            Stat::make('Purchasing Mails', RSLApp::where('subject1', 'purchasing')->count())
                ->description('Total Purchasing')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),

            Stat::make('Non-Purchasing Mails', RSLApp::where('subject1', 'non purchasing')->count())
                ->description('Total Non-Purchasing')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('gray'),
        ];
    }
}
