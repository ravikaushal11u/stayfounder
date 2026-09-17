<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LocationService
{
    /**
     * Common Indian student hubs, cities, and coaching centres offline dictionary.
     */
    protected static array $cityCoordinates = [
        'hazaribagh' => [23.9925, 85.3637, 'Jharkhand'],
        'hazaribag' => [23.9925, 85.3637, 'Jharkhand'],
        'ranchi' => [23.3441, 85.3096, 'Jharkhand'],
        'dhanbad' => [23.7957, 86.4304, 'Jharkhand'],
        'jamshedpur' => [22.8046, 86.2029, 'Jharkhand'],
        'bokaro' => [23.6693, 86.1511, 'Jharkhand'],
        'patna' => [25.5941, 85.1376, 'Bihar'],
        'gaya' => [24.7914, 85.0002, 'Bihar'],
        'kota' => [25.2138, 75.8648, 'Rajasthan'],
        'jaipur' => [26.9124, 75.7873, 'Rajasthan'],
        'pune' => [18.5204, 73.8567, 'Maharashtra'],
        'mumbai' => [19.0760, 72.8777, 'Maharashtra'],
        'delhi' => [28.6139, 77.2090, 'Delhi'],
        'new delhi' => [28.6139, 77.2090, 'Delhi'],
        'noida' => [28.5355, 77.3910, 'Uttar Pradesh'],
        'greater noida' => [28.4744, 77.5040, 'Uttar Pradesh'],
        'lucknow' => [26.8467, 80.9462, 'Uttar Pradesh'],
        'kanpur' => [26.4499, 80.3319, 'Uttar Pradesh'],
        'prayagraj' => [25.4358, 81.8463, 'Uttar Pradesh'],
        'allahabad' => [25.4358, 81.8463, 'Uttar Pradesh'],
        'varanasi' => [25.3176, 82.9739, 'Uttar Pradesh'],
        'bengaluru' => [12.9716, 77.5946, 'Karnataka'],
        'bangalore' => [12.9716, 77.5946, 'Karnataka'],
        'hyderabad' => [17.3850, 78.4867, 'Telangana'],
        'chennai' => [13.0827, 80.2707, 'Tamil Nadu'],
        'kolkata' => [22.5726, 88.3639, 'West Bengal'],
        'chandigarh' => [30.7333, 76.7794, 'Chandigarh'],
        'indore' => [22.7196, 75.8577, 'Madhya Pradesh'],
        'bhopal' => [23.2599, 77.4126, 'Madhya Pradesh'],
        'dehradun' => [30.3165, 78.0322, 'Uttarakhand'],
    ];

    /**
     * Resolve latitude and longitude from City, Locality, or Pincode.
     *
     * @return array{0: float, 1: float}|null
     */
    public static function resolveCoordinates(?string $city, ?string $locality = null, ?string $pincode = null): ?array
    {
        $cityKey = strtolower(trim((string) $city));

        // 1. Direct match in Indian city dictionary
        if (isset(self::$cityCoordinates[$cityKey])) {
            return [self::$cityCoordinates[$cityKey][0], self::$cityCoordinates[$cityKey][1]];
        }

        // Check if city contains known key (e.g. "Hazaribagh District")
        foreach (self::$cityCoordinates as $key => $coords) {
            if (str_contains($cityKey, $key) || ($locality && str_contains(strtolower($locality), $key))) {
                return [$coords[0], $coords[1]];
            }
        }

        // 2. Lookup via Pincode if provided
        if ($pincode && preg_match('/^[1-9][0-9]{5}$/', trim($pincode))) {
            try {
                $response = Http::timeout(3)->get('https://api.postalpincode.in/pincode/' . trim($pincode));
                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json[0]['PostOffice'][0]['District'])) {
                        $district = strtolower($json[0]['PostOffice'][0]['District']);
                        if (isset(self::$cityCoordinates[$district])) {
                            return [self::$cityCoordinates[$district][0], self::$cityCoordinates[$district][1]];
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::debug('Postal Pincode API lookup error: ' . $e->getMessage());
            }

            // Fallback via OpenStreetMap Nominatim for Pincode
            try {
                $geo = Http::timeout(3)
                    ->withHeaders(['User-Agent' => 'StayFinder-App/1.0'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'postalcode' => trim($pincode),
                        'country' => 'india',
                        'format' => 'json',
                        'limit' => 1,
                    ]);

                if ($geo->successful() && !empty($geo->json()[0]['lat'])) {
                    return [
                        (float) $geo->json()[0]['lat'],
                        (float) $geo->json()[0]['lon'],
                    ];
                }
            } catch (\Throwable $e) {
                Log::debug('Nominatim Pincode lookup error: ' . $e->getMessage());
            }
        }

        // 3. Fallback via OpenStreetMap Nominatim for City + Locality
        if ($city) {
            try {
                $query = trim(($locality ? $locality . ', ' : '') . $city . ', India');
                $geo = Http::timeout(3)
                    ->withHeaders(['User-Agent' => 'StayFinder-App/1.0'])
                    ->get('https://nominatim.openstreetmap.org/search', [
                        'q' => $query,
                        'format' => 'json',
                        'limit' => 1,
                    ]);

                if ($geo->successful() && !empty($geo->json()[0]['lat'])) {
                    return [
                        (float) $geo->json()[0]['lat'],
                        (float) $geo->json()[0]['lon'],
                    ];
                }
            } catch (\Throwable $e) {
                Log::debug('Nominatim City lookup error: ' . $e->getMessage());
            }
        }

        return null;
    }
}
