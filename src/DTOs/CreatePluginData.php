<?php

namespace Jyim\LaravelPluginMarket\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Jyim\LaravelPluginMarket\Exceptions\ApiRequestException;

/**
 * 原实现继承 `Spatie\DataTransferObject\DataTransferObject`（v2 的数组构造）。
 * 该依赖只支持到 Laravel 10，且 v3 改了构造方式。这里只是 5 个字段的传输对象，
 * 改为零依赖实现，保留 `new self([...])` 的调用形态。
 */
class CreatePluginData
{
    public string $pluginName;

    public int $authorId;

    public array $pluginMetadata;

    public string $content;

    public string $contentType;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public static function fromRequest(Request $request): self
    {
        $pluginInfo = json_decode($request->header('plugin-info'), true) ?? [];

        $validator = Validator::make($pluginInfo, [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new ApiRequestException($validator->errors()->all()[0]);
        }

        return new self([
            'pluginName' => data_get($pluginInfo, 'name'),
            'authorId' => Auth::user()->id,
            'content' => $request->getContent(),
            'pluginMetadata' => $pluginInfo,
            'contentType' => $request->header('content-type'),
        ]);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}