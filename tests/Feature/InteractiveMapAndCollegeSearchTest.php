<?php

namespace Tests\Feature;

use App\Models\Property;
use Database\Seeders\PropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InteractiveMapAndCollegeSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PropertySeeder::class);
    }

    public function test_search_page_renders_map_view_elements_and_top_colleges(): void
    {
        $response = $this->get(route('properties.index', ['view' => 'map']));
        $response->assertStatus(200);

        // Check map view toggle buttons
        $response->assertSeeText('Grid View');
        $response->assertSeeText('Map View');

        // Check map container element
        $response->assertSee('id="staysMap"', false);

        // Check popular campus proximity chips
        $response->assertSeeText('Near Campus:');
        $response->assertSeeText('Symbiosis International');
        $response->assertSeeText('COEP Technological University');
    }

    public function test_map_properties_data_is_passed_to_view_with_valid_coordinates(): void
    {
        $response = $this->get(route('properties.index'));
        $response->assertStatus(200);

        $response->assertViewHas('mapProperties', function ($mapProps) {
            $this->assertNotEmpty($mapProps);
            $first = $mapProps->first();
            $this->assertArrayHasKey('lat', $first);
            $this->assertArrayHasKey('lng', $first);
            $this->assertArrayHasKey('price', $first);
            $this->assertArrayHasKey('title', $first);
            $this->assertArrayHasKey('url', $first);
            return is_float($first['lat']) && is_float($first['lng']);
        });
    }

    public function test_filtering_by_college_name_returns_relevant_properties(): void
    {
        $response = $this->get(route('properties.index', ['query' => 'Symbiosis']));
        $response->assertStatus(200);

        // Property in Viman Nagar near Symbiosis should be visible
        $response->assertSeeText('Sunrise Student Living');
    }

    public function test_search_endpoint_returns_json_for_ajax_map_requests(): void
    {
        $response = $this->getJson(route('properties.index', ['city' => 'Pune']));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'properties' => [
                '*' => ['id', 'title', 'slug', 'lat', 'lng', 'price', 'rent_display'],
            ],
            'total',
        ]);
    }

    public function test_homepage_renders_popular_campus_quick_chips(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSeeText('Popular Campuses:');
        $response->assertSeeText('Symbiosis Pune');
        $response->assertSeeText('COEP Tech');
        $response->assertSeeText('Delhi University');
    }
}
