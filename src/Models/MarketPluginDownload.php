<?php

namespace Jyim\LaravelPluginMarket\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketPluginDownload extends Model
{

    protected $table = 'market_plugin_downloads';


    use HasFactory;

    protected $fillable = ['plugin_version_id', 'user_id'];

    /**
     * @return BelongsTo
     */
    public function version():BelongsTo
    {
        return $this->belongsTo(MarketPluginVersion::class, 'plugin_version_id');
    }

    /**
     * @return BelongsTo
     */
    public function user():BelongsTo
    {
        return $this->belongsTo(MarketUser::class, 'user_id');
    }
}
