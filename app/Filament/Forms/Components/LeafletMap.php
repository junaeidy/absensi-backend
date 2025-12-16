<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class LeafletMap extends Field
{
    protected string $view = 'filament.forms.components.leaflet-map';

    protected float $defaultLatitude = -6.200000;
    protected float $defaultLongitude = 106.816666;
    protected int $defaultZoom = 15;
    protected int $mapHeight = 400;

    public function defaultLocation(float $latitude, float $longitude): static
    {
        $this->defaultLatitude = $latitude;
        $this->defaultLongitude = $longitude;

        return $this;
    }

    public function defaultZoom(int $zoom): static
    {
        $this->defaultZoom = $zoom;

        return $this;
    }

    public function mapHeight(int $height): static
    {
        $this->mapHeight = $height;

        return $this;
    }

    public function getDefaultLatitude(): float
    {
        return $this->defaultLatitude;
    }

    public function getDefaultLongitude(): float
    {
        return $this->defaultLongitude;
    }

    public function getDefaultZoom(): int
    {
        return $this->defaultZoom;
    }

    public function getMapHeight(): int
    {
        return $this->mapHeight;
    }
}
