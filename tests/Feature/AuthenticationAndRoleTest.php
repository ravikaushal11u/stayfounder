<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ProviderProfile;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationAndRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('StayFinder');
        $response->assertSeeText('Find a place that');
        $response->assertSeeText('feels like home');
    }

    public function test_login_page_renders_with_demo_buttons(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSeeText('Sign in to StayFinder');
        $response->assertSeeText('One-Click Quick Demo Sign In');
    }

    public function test_register_page_renders_with_role_options(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
        $response->assertSeeText("Student (100% Free)");
        $response->assertSeeText("Provider (PG/Hostel Owner)");
    }

    public function test_student_can_register_and_access_student_dashboard(): void
    {
        $response = $this->post('/register/student', [
            'name' => 'Kavya Sen',
            'email' => 'kavya@student.du.ac.in',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'phone' => '+91 9988776655',
            'gender' => 'female',
            'college_name' => 'Miranda House, Delhi University',
            'preferred_city' => 'Delhi',
            'budget_min' => 5000,
            'budget_max' => 12000,
            'preferred_room_type' => 'double',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'kavya@student.du.ac.in')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::STUDENT, $user->role);
        $this->assertNotNull($user->studentProfile);
        $this->assertEquals('Miranda House, Delhi University', $user->studentProfile->college_name);

        $dashboardResponse = $this->get('/student/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Welcome back, Kavya Sen!');
    }

    public function test_provider_can_register_and_access_provider_dashboard(): void
    {
        $response = $this->post('/register/provider', [
            'name' => 'Anil Deshmukh',
            'email' => 'anil@punehostels.com',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
            'business_name' => 'Royal Heritage PG & Hostel',
            'phone' => '+91 98220 12345',
            'whatsapp_number' => '+91 98220 12345',
            'city' => 'Pune',
            'address' => 'Near FC Road, Shivajinagar, Pune',
        ]);

        $response->assertRedirect(route('provider.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'anil@punehostels.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::PROVIDER, $user->role);
        $this->assertNotNull($user->providerProfile);
        $this->assertEquals('Royal Heritage PG & Hostel', $user->providerProfile->business_name);

        $dashboardResponse = $this->get('/provider/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Royal Heritage PG & Hostel');
    }

    public function test_user_can_login_and_redirects_to_correct_dashboard(): void
    {
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student.test@stayfinder.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $response = $this->post('/login', [
            'email' => 'student.test@stayfinder.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_student_cannot_access_provider_or_admin_dashboard(): void
    {
        $student = User::create([
            'name' => 'Student User',
            'email' => 'student.test2@stayfinder.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($student);

        // Attempt to access provider dashboard
        $response = $this->get('/provider/dashboard');
        $response->assertRedirect(route('student.dashboard'));
        $response->assertSessionHas('error');

        // Attempt to access admin dashboard
        $adminResponse = $this->get('/admin/dashboard');
        $adminResponse->assertRedirect(route('student.dashboard'));
        $adminResponse->assertSessionHas('error');
    }

    public function test_suspended_user_cannot_access_dashboard(): void
    {
        $suspendedUser = User::create([
            'name' => 'Suspended Student',
            'email' => 'suspended@stayfinder.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::SUSPENDED,
        ]);

        $this->actingAs($suspendedUser);

        $response = $this->get('/student/dashboard');
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Your account is suspended. Please contact StayFinder support.');
        $this->assertGuest();
    }

    public function test_user_can_logout_cleanly(): void
    {
        $user = User::create([
            'name' => 'Active Student',
            'email' => 'active@stayfinder.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post('/logout');
        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }
}
