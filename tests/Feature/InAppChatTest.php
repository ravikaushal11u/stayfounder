<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\PropertySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InAppChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $provider;
    protected User $otherStudent;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PropertySeeder::class);

        $this->provider = User::where('role', UserRole::PROVIDER)->first();
        $this->property = Property::where('user_id', $this->provider->id)->first();

        $this->student = User::create([
            'name' => 'Siddharth Student',
            'email' => 'siddharth@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
            'phone' => '+91 99887 76655',
        ]);

        $this->otherStudent = User::create([
            'name' => 'Other Student',
            'email' => 'otherstudent@stayfinder.com',
            'password' => bcrypt('password123'),
            'role' => UserRole::STUDENT,
            'status' => UserStatus::ACTIVE,
        ]);
    }

    public function test_guest_cannot_access_chat(): void
    {
        $response = $this->get(route('chat.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_student_can_start_chat_from_property(): void
    {
        $response = $this->actingAs($this->student)->post(route('chat.start', $this->property->id), [
            'message' => 'Hello, I would like to inquire about double room vacancy.',
        ]);

        $this->assertDatabaseHas('conversations', [
            'property_id' => $this->property->id,
            'student_id' => $this->student->id,
            'provider_id' => $this->provider->id,
        ]);

        $conversation = Conversation::where('student_id', $this->student->id)->first();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->student->id,
            'body' => 'Hello, I would like to inquire about double room vacancy.',
        ]);

        $response->assertRedirect(route('chat.index', ['conversation_id' => $conversation->id]));
    }

    public function test_provider_cannot_chat_with_self_on_own_property(): void
    {
        $response = $this->actingAs($this->provider)->post(route('chat.start', $this->property->id));
        $response->assertSessionHas('warning');
        $this->assertDatabaseCount('conversations', 0);
    }

    public function test_student_and_provider_can_view_conversation(): void
    {
        $conversation = Conversation::create([
            'property_id' => $this->property->id,
            'student_id' => $this->student->id,
            'provider_id' => $this->provider->id,
            'last_message_at' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->student->id,
            'body' => 'Test message from student',
        ]);

        // Student can view
        $studentResponse = $this->actingAs($this->student)->get(route('chat.index', ['conversation_id' => $conversation->id]));
        $studentResponse->assertStatus(200);
        $studentResponse->assertSeeText('Test message from student');

        // Provider can view
        $providerResponse = $this->actingAs($this->provider)->get(route('chat.index', ['conversation_id' => $conversation->id]));
        $providerResponse->assertStatus(200);
        $providerResponse->assertSeeText('Test message from student');
    }

    public function test_unauthorized_user_cannot_view_others_conversation(): void
    {
        $conversation = Conversation::create([
            'property_id' => $this->property->id,
            'student_id' => $this->student->id,
            'provider_id' => $this->provider->id,
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($this->otherStudent)->get(route('chat.show', $conversation->id));
        $response->assertStatus(403);
    }

    public function test_user_can_send_message_in_conversation(): void
    {
        $conversation = Conversation::create([
            'property_id' => $this->property->id,
            'student_id' => $this->student->id,
            'provider_id' => $this->provider->id,
            'last_message_at' => now(),
        ]);

        $response = $this->actingAs($this->provider)->post(route('chat.store', $conversation->id), [
            'body' => 'Yes, a room is vacant. You can visit tomorrow at 5 PM.',
        ]);

        $response->assertRedirect(route('chat.index', ['conversation_id' => $conversation->id]));

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->provider->id,
            'body' => 'Yes, a room is vacant. You can visit tomorrow at 5 PM.',
        ]);
    }

    public function test_viewing_conversation_marks_incoming_messages_as_read(): void
    {
        $conversation = Conversation::create([
            'property_id' => $this->property->id,
            'student_id' => $this->student->id,
            'provider_id' => $this->provider->id,
            'last_message_at' => now(),
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->student->id,
            'body' => 'Unread message from student',
            'read_at' => null,
        ]);

        $this->assertNull($message->read_at);

        // When provider views conversation, message is marked as read
        $this->actingAs($this->provider)->get(route('chat.index', ['conversation_id' => $conversation->id]));

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_api_can_fetch_messages_json(): void
    {
        $conversation = Conversation::create([
            'property_id' => $this->property->id,
            'student_id' => $this->student->id,
            'provider_id' => $this->provider->id,
            'last_message_at' => now(),
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->student->id,
            'body' => 'JSON polling test message',
        ]);

        $response = $this->actingAs($this->student)->getJson(route('chat.show', $conversation->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'conversation_id',
            'messages' => [
                '*' => ['id', 'body', 'sender_id', 'created_at'],
            ],
        ]);
    }
}
