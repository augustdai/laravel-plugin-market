<?php

namespace Jyim\LaravelPluginMarket\DTOs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Jyim\LaravelPluginMarket\Exceptions\ApiRequestException;
use Jyim\LaravelPluginMarket\ValueObjects\Money;

/**
 * 原实现继承 `Spatie\DataTransferObject\DataTransferObject`（v2 的数组构造）。
 * 该依赖只支持到 Laravel 9，且 v3 改了构造方式。这里改为零依赖实现，
 * 保留 `new self([...])` 的调用形态。
 */
class UpdatePluginVersionData
{
    public int $status;

    public ?string $version;

    public Money $price;

    public ?string $description;

    public ?string $logo;

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
        $validator = Validator::make($request->input(), [
            'status' => 'integer',
            'price' => 'numeric',
        ]);

        if ($validator->fails()) {
            throw new ApiRequestException($validator->errors()->all()[0]);
        }

        return new self([
            'status' => (int) $request->input('status', 0),
            'version' => $request->input('version'),
            'price' => Money::withDefaultCurrency((int) bcmul((string) $request->input('price', 0), '100')),
            'description' => $request->input('description'),
            'logo' => $request->input('logo'),
        ]);
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}