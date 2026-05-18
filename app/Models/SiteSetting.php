<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'logo_path',
        'favicon_path',
        'primary_color',
        'primary_hover_color',
        'secondary_color',
        'info_color',
        'success_color',
        'warning_color',
        'error_color',
        'background_color',
        'surface_color',
        'text_color',
    ];

    public static function defaults(): array
    {
        return [
            'site_name' => config('app.name', 'StudentAMS'),
            'primary_color' => '#084924',
            'primary_hover_color' => '#053319',
            'secondary_color' => '#FDC601',
            'info_color' => '#2F80ED',
            'success_color' => '#27AE60',
            'warning_color' => '#E2B93B',
            'error_color' => '#EB5757',
            'background_color' => '#F8FAFC',
            'surface_color' => '#FFFFFF',
            'text_color' => '#0F172A',
        ];
    }

    public static function current(): self
    {
        $data = Cache::remember('site_settings', 600, function () {
            $record = static::query()->first();

            return $record ? $record->toArray() : static::defaults();
        });

        if (!is_array($data)) {
            Cache::forget('site_settings');
            $data = static::defaults();
        }

        $model = new static($data);
        if (isset($data['id'])) {
            $model->exists = true;
            $model->setAttribute('id', $data['id']);
        }

        return $model;
    }

    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        return asset('storage/' . $this->logo_path);
    }

    public function getFaviconUrlAttribute(): ?string
    {
        if (!$this->favicon_path) {
            return null;
        }

        return asset('storage/' . $this->favicon_path);
    }
}
