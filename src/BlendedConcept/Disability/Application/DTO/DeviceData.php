<?php

namespace Src\BlendedConcept\Disability\Application\DTO;

use Illuminate\Http\Request;
use Src\BlendedConcept\Disability\Domain\Model\Entities\Device;

class DeviceData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $descripton,
        public readonly ?string $status,
        public readonly ?array $disability_types
    ) {
    }

    public static function fromRequest(Request $request, $device): DeviceData
    {

        return new self(
            id: $device->id,
            name: $request->name,
            descripton: $request->description,
            status: $request->status,
            disability_types: $request->disability_types
        );
    }

    public static function fromEloquent(Device $device): self
    {
        return new self(
            id: $device->id,
            name: $device->name,
            descripton: $device->description,
            status: $device->status,
            disability_types: null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->descripton,
            'disability_types' => $this->disability_types,
        ];
    }
}
