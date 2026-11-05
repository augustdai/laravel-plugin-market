<?php
namespace Jyim\LaravelPluginMarket\Services\Plugins;

use Illuminate\Support\Facades\Storage;
use Jyim\LaravelPluginMarket\Models\MarketPluginDownload;
use Jyim\LaravelPluginMarket\Models\MarketPluginVersion;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Download
{
    /**
     * @param  int  $versionId
     * @param  int  $userId
     * @return StreamedResponse
     */
    public function execute(int $versionId, int $userId): StreamedResponse
    {
        $mpv = MarketPluginVersion::query()->with('plugin')->release()->findOrFail($versionId);
        MarketPluginDownload::query()->create([
            'plugin_version_id' => $mpv->id,
            'user_id' => $userId,
        ]);
        $mpv->download_times += 1;
        $mpv->plugin->download_times += 1;
        $mpv->push();
        return Storage::download($mpv->path);
    }
}