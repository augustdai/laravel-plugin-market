<?php
namespace Jyim\LaravelPluginMarket\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Jyim\LaravelPluginMarket\Http\Resources\PluginDownloadResource;
use Jyim\LaravelPluginMarket\Models\MarketPluginDownload;

class PluginDownloadsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return PluginDownloadResource::collection(MarketPluginDownload::query()->with('version', 'user', 'version.plugin')->latest()->paginate());
    }
}