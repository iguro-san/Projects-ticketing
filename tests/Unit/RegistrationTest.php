<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Registration;
use App\Models\TicketType;
use PHPUnit\Framework\Attributes\Test;

class RegistrationTest extends TestCase
{
    #[Test]
    public function it_generates_correct_registration_number_format()
    {
        $number = Registration::generateRegistrationNumber();
        
        $this->assertMatchesRegularExpression(
            '/^REG-\d{8}-[A-Z0-9]{6}$/',
            $number
        );
    }

    #[Test]
    public function payment_deadline_is_5_minutes_from_now()
    {
        $deadline = Registration::getDefaultDeadline();
        $expected = now()->addMinutes(5);
        
        $this->assertEqualsWithDelta(
            $expected->timestamp,
            $deadline->timestamp,
            1
        );
    }

    #[Test]
    public function remaining_quota_is_calculated_correctly()
    {
        $ticket = new TicketType([
            'quota' => 100,
            'registered' => 30,
        ]);
        
        $this->assertEquals(70, $ticket->remaining_quota);
    }

    #[Test]
    public function ticket_available_when_quota_remaining()
    {
        $ticket = new TicketType([
            'quota' => 100,
            'registered' => 50,
            'is_active' => true,
        ]);
        
        $this->assertTrue($ticket->isAvailable());
    }

    #[Test]
    public function ticket_sold_out_when_quota_full()
    {
        $ticket = new TicketType([
            'quota' => 100,
            'registered' => 100,
            'is_active' => true,
        ]);
        
        $this->assertTrue($ticket->isSoldOut());
        $this->assertFalse($ticket->isAvailable());
    }

    #[Test]
    public function payment_status_detection()
    {
        $reg = new Registration(['payment_status' => 'pending']);
        $this->assertTrue($reg->isPending());
        $this->assertFalse($reg->isPaid());
        
        $reg->payment_status = 'paid';
        $this->assertTrue($reg->isPaid());
        
        $reg->payment_status = 'cancelled';
        $this->assertTrue($reg->isCancelled());
    }

    #[Test]
    public function detects_expired_deadline()
    {
        $expired = new Registration(['payment_deadline' => now()->subMinutes(10)]);
        $this->assertTrue($expired->isDeadlinePassed());
        
        $active = new Registration(['payment_deadline' => now()->addMinutes(10)]);
        $this->assertFalse($active->isDeadlinePassed());
        
        $none = new Registration(['payment_deadline' => null]);
        $this->assertFalse($none->isDeadlinePassed());
    }
}