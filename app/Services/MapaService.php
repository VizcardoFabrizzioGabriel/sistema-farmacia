<?php

namespace App\Services;

use App\Models\Proveedor;

class MapaService
{
    // Obtener coordenadas de la farmacia (configuradas en .env)
    public function getCoordenadasFarmacia(): array
    {
        return [
            'lat' => config('services.map.default_lat', -12.046374),
            'lng' => config('services.map.default_lng', -77.042793),
            'nombre' => 'EDDUFARMA - Sede Principal',
        ];
    }

    // Obtener todos los proveedores con coordenadas
    public function getProveedoresConCoordenadas(): array
    {
        $proveedores = Proveedor::whereNotNull('latitud')
            ->whereNotNull('longitud')
            ->get()
            ->map(function ($proveedor) {
                return [
                    'id' => $proveedor->id_proveedor,
                    'nombre' => $proveedor->razon_social,
                    'contacto' => $proveedor->contacto,
                    'telefono' => $proveedor->telefono,
                    'lat' => $proveedor->latitud,
                    'lng' => $proveedor->longitud,
                ];
            });

        return $proveedores->toArray();
    }

    // Calcular distancia entre farmacia y proveedor (fórmula Haversine)
    public function calcularDistancia(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $radioTierra = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($radioTierra * $c, 2);
    }

    // Obtener proveedores ordenados por distancia
    public function getProveedoresPorDistancia(): array
    {
        $farmacia = $this->getCoordenadasFarmacia();
        $proveedores = $this->getProveedoresConCoordenadas();

        foreach ($proveedores as &$proveedor) {
            $proveedor['distancia_km'] = $this->calcularDistancia(
                $farmacia['lat'],
                $farmacia['lng'],
                $proveedor['lat'],
                $proveedor['lng']
            );
        }

        usort($proveedores, function ($a, $b) {
            return $a['distancia_km'] <=> $b['distancia_km'];
        });

        return $proveedores;
    }
}