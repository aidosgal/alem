<?php

namespace Database\Factories;

use App\Models\Applicant;
use App\Models\Chat;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Models\OrganizationUsers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chat_id' => Chat::factory(),
            'content' => fake()->sentence(),
            'metadata' => [],
            'sender_applicant_id' => null,
            'sender_organization_manager_id' => null,
            'reply_to_message_id' => null,
            'created_at' => now(),
        ];
    }

    public function fromApplicant(?Applicant $applicant = null): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_applicant_id' => $applicant?->id ?? Applicant::factory(),
            'sender_organization_manager_id' => null,
        ]);
    }

    public function fromManager(?OrganizationUsers $manager = null): static
    {
        return $this->state(fn (array $attributes) => [
            'sender_applicant_id' => null,
            'sender_organization_manager_id' => $manager?->id ?? OrganizationUsers::factory(),
        ]);
    }

    public function reply(Message $message): static
    {
        return $this->state(fn (array $attributes) => [
            'reply_to_message_id' => $message->id,
            'chat_id' => $message->chat_id,
        ]);
    }

    public function withAttachment(): static
    {
        return $this->afterCreating(function (Message $message) {
            MessageAttachment::create([
                'message_id' => $message->id,
                'url' => fake()->url() . '/file.pdf',
                'filename' => fake()->word() . '.pdf',
                'filetype' => 'application/pdf',
                'filesize' => fake()->numberBetween(1024, 5242880), // 1KB to 5MB
            ]);
        });
    }
}
