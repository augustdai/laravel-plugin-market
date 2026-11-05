<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Jyim\LaravelPluginMarket\Enums\PluginVersionStatus;
use Jyim\LaravelPluginMarket\ValueObjects\Money;

/**
 * Class MarketPluginVersion
 * @package Jyim\LaravelPluginMarket\Models
 */
class MarketPluginVersion extends Model
{
    use HasFactory;

    protected $table = 'market_plugin_versions';

    /**
     * 逻辑表名：market 域开关打开时解析为 v2_market_plugin_versions。
     *
     * 之前这个模型没挂 trait，写入一直落旧表，而 Admin\MarketPluginController
     * 两张表各自维护自己的行 —— 本项目已无表名映射层。
     * 表现为「删了应用，版本行还在旧表里」。由 v2:status 前置检查（v2 行数 ≥ 旧表）抓到。
     */

    protected $fillable = ['version', 'plugin_id', 'path',  'plugin_metadata', 'price', 'type', 'status'];

    protected $casts = [
        'plugin_metadata' => 'json'
    ];

    /**
     * @return string
     */
    public function getDownloadLinkAttribute(): string
    {
        return Storage::url($this->path);
    }

    /**
     * @param $value
     */
    public function setLogoAttribute($value): void
    {
        if ($value) {
            $pluginMetadata=  $this->plugin_metadata;
            $pluginMetadata['logo'] = $value;
            $this->attributes['plugin_metadata'] = json_encode($pluginMetadata, true);
        }
    }

    /**
     * @param $value
     */
    public function setDescriptionAttribute($value): void
    {
        if ($value) {
            $pluginMetadata=  $this->plugin_metadata;
            $pluginMetadata['description'] = $value;
            $this->attributes['plugin_metadata'] = json_encode($pluginMetadata, true);
        }
    }

    /**
     * @return float
     */
    public function getPriceAmountAttribute(): float
    {
        return Money::withDefaultCurrency($this->price)->amount();
    }

    public function plugin():BelongsTo
    {
        return $this->belongsTo(MarketPlugin::class, 'plugin_id');
    }

    public function getStatusStrAttribute(): string
    {
        return PluginVersionStatus::status($this->status);
    }

    public function getDescriptionAttribute(): string
    {
        return data_get($this->plugin_metadata, 'description', '');
    }

    public function getLogoAttribute(): string
    {
        return data_get($this->plugin_metadata, 'logo', '');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeRelease(Builder $query):Builder
    {
        return $query->where('status', PluginVersionStatus::ACTIVE);
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeNotRelease(Builder $query):Builder
    {
        return $query->where('status', "<",PluginVersionStatus::ACTIVE);
    }
}
