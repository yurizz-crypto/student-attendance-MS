<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting;
use App\Services\AuditService;
use Livewire\Component;
use Livewire\WithFileUploads;

class SiteSettings extends Component
{
    use WithFileUploads;

    public string $siteName = '';
    public $logo;
    public $favicon;

    public string $primaryColor = '';
    public string $primaryHoverColor = '';
    public string $secondaryColor = '';
    public string $infoColor = '';
    public string $successColor = '';
    public string $warningColor = '';
    public string $errorColor = '';
    public string $backgroundColor = '';
    public string $surfaceColor = '';
    public string $textColor = '';

    public function mount(): void
    {
        $settings = SiteSetting::current();

        $this->siteName = $settings->site_name ?? SiteSetting::defaults()['site_name'];
        $this->primaryColor = $settings->primary_color ?? SiteSetting::defaults()['primary_color'];
        $this->primaryHoverColor = $settings->primary_hover_color ?? SiteSetting::defaults()['primary_hover_color'];
        $this->secondaryColor = $settings->secondary_color ?? SiteSetting::defaults()['secondary_color'];
        $this->infoColor = $settings->info_color ?? SiteSetting::defaults()['info_color'];
        $this->successColor = $settings->success_color ?? SiteSetting::defaults()['success_color'];
        $this->warningColor = $settings->warning_color ?? SiteSetting::defaults()['warning_color'];
        $this->errorColor = $settings->error_color ?? SiteSetting::defaults()['error_color'];
        $this->backgroundColor = $settings->background_color ?? SiteSetting::defaults()['background_color'];
        $this->surfaceColor = $settings->surface_color ?? SiteSetting::defaults()['surface_color'];
        $this->textColor = $settings->text_color ?? SiteSetting::defaults()['text_color'];
    }

    public function saveBranding(): void
    {
        $this->validate([
            'siteName' => ['required', 'string', 'max:120'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:2048'],
            'favicon' => ['nullable', 'mimes:png,jpg,jpeg,svg,ico', 'max:1024'],
            'primaryColor' => ['required', 'string', 'max:20'],
            'primaryHoverColor' => ['required', 'string', 'max:20'],
            'secondaryColor' => ['required', 'string', 'max:20'],
            'infoColor' => ['required', 'string', 'max:20'],
            'successColor' => ['required', 'string', 'max:20'],
            'warningColor' => ['required', 'string', 'max:20'],
            'errorColor' => ['required', 'string', 'max:20'],
            'backgroundColor' => ['required', 'string', 'max:20'],
            'surfaceColor' => ['required', 'string', 'max:20'],
            'textColor' => ['required', 'string', 'max:20'],
        ]);

        $settings = SiteSetting::query()->first() ?? new SiteSetting();
        $settings->site_name = $this->siteName;

        if ($this->logo) {
            $settings->logo_path = $this->logo->store('branding', 'public');
        }

        if ($this->favicon) {
            $settings->favicon_path = $this->favicon->store('branding', 'public');
        }

        $settings->primary_color = $this->primaryColor;
        $settings->primary_hover_color = $this->primaryHoverColor;
        $settings->secondary_color = $this->secondaryColor;
        $settings->info_color = $this->infoColor;
        $settings->success_color = $this->successColor;
        $settings->warning_color = $this->warningColor;
        $settings->error_color = $this->errorColor;
        $settings->background_color = $this->backgroundColor;
        $settings->surface_color = $this->surfaceColor;
        $settings->text_color = $this->textColor;
        $settings->save();

        SiteSetting::clearCache();

        AuditService::log('site_settings_updated', SiteSetting::class, $settings->id, []);

        $this->dispatch('swal:success', title: 'Saved', message: 'Branding settings updated.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings');
    }
}
