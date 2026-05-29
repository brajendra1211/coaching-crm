<?php

namespace App\Providers;

use App\Models\CoachingSetting;
use App\Models\SeoLocation;
use App\Models\WebsitePage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $siteSetting = null;

        if (Schema::hasTable('coaching_settings')) {
            $siteSetting = CoachingSetting::current();
            $this->configureDynamicMail($siteSetting);
        }

        View::composer('frontend.*', function ($view) {
            $seoLocations = collect();
            $websitePages = collect();

            if (Schema::hasTable('seo_locations')) {
                $seoLocations = SeoLocation::where('status', 'active')
                    ->orderBy('sort_order')
                    ->latest()
                    ->take(12)
                    ->get();
            }

            if (Schema::hasTable('website_pages')) {
                $websitePages = WebsitePage::where('status', 'active')
                    ->orderBy('sort_order')
                    ->latest()
                    ->take(10)
                    ->get();
            }

            $siteSetting = Schema::hasTable('coaching_settings') ? CoachingSetting::current() : null;

            $view->with([
                'seoLocations' => $seoLocations,
                'websitePages' => $websitePages,
                'siteSetting' => $siteSetting,
            ]);
        });

        View::composer('portal.*', function ($view) use ($siteSetting) {
            $view->with('siteSetting', $siteSetting ?: (Schema::hasTable('coaching_settings') ? CoachingSetting::current() : null));
        });
    }

    private function configureDynamicMail(?CoachingSetting $setting): void
    {
        if (!$setting || !$setting->gmail_address || !$setting->gmail_app_password) {
            return;
        }

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.scheme', 'tls');
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.mailers.smtp.username', $setting->gmail_address);
        Config::set('mail.mailers.smtp.password', $setting->gmail_app_password);
        Config::set('mail.from.address', $setting->mail_from_address ?: $setting->gmail_address);
        Config::set('mail.from.name', $setting->mail_from_name ?: $setting->institute_name ?: config('app.name'));
    }
}
