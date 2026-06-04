<?php

namespace App\DTOs\Payments;

class GatewayDisplayData
{
    public string $code;
    public string $name;
    public string $description;
    public string $logo;
    public bool $enabled;
    public bool $is_default;

    public function __construct(
        string $code,
        string $name,
        string $description,
        string $logo = '',
        bool $enabled = false,
        bool $is_default = false
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
        $this->logo = $logo;
        $this->enabled = $enabled;
        $this->is_default = $is_default;
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'logo' => $this->logo,
            'enabled' => $this->enabled,
            'is_default' => $this->is_default,
        ];
    }
}
