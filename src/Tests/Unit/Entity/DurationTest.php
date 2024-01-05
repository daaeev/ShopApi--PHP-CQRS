<?php

namespace Project\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Project\Common\Entity\Duration;

class DurationTest extends TestCase
{
    public function testCreateDuration()
    {
        $duration = new Duration($startDate = new \DateTimeImmutable());
        $this->assertSame($startDate, $duration->getStartDate());
        $this->assertNull($duration->getEndDate());

        $duration = new Duration(endDate: $endDate = new \DateTimeImmutable());
        $this->assertSame($endDate, $duration->getEndDate());
        $this->assertNull($duration->getStartDate());

        $duration = new Duration(
            startDate: $startDate = new \DateTimeImmutable('-1 day'),
            endDate: $endDate = new \DateTimeImmutable('+1 day')
        );
        $this->assertSame($startDate, $duration->getStartDate());
        $this->assertSame($endDate, $duration->getEndDate());
    }

    public function testCreateDurationWithoutDates()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Duration();
    }

    public function testCreateDurationWithEndDateThatLessThanStartDate()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Duration(
            new \DateTimeImmutable('+1 day'),
            new \DateTimeImmutable('-1 day'),
        );
    }

    public function testDurationNotStarted()
    {
        $duration = new Duration(new \DateTimeImmutable('+1 day'));
        $this->assertTrue($duration->notStarted());
        $this->assertFalse($duration->started());
        $this->assertFalse($duration->ended());
    }

    public function testDurationStarted()
    {
        $this->assertDurationStarted(new Duration(
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        ));

        $this->assertDurationStarted(new Duration(
            endDate: new \DateTimeImmutable('+1 day'),
        ));
    }

    private function assertDurationStarted(Duration $duration): void
    {
        $this->assertFalse($duration->notStarted());
        $this->assertTrue($duration->started());
        $this->assertFalse($duration->ended());
    }

    public function testDurationEnded()
    {
        $duration = new Duration(endDate: new \DateTimeImmutable('-1 day'));
        $this->assertFalse($duration->notStarted());
        $this->assertFalse($duration->started());
        $this->assertTrue($duration->ended());
    }

    public function testEqualsTo()
    {
        $duration = new Duration(
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );

        $this->assertTrue($duration->equalsTo($duration));
    }

    /**
     * @dataProvider doesNotEqualsDurations
     */
    public function testDoesNotEqualsTo(Duration $notEqualsDuration)
    {
        $duration = new Duration(
            new \DateTimeImmutable('-1 day'),
            new \DateTimeImmutable('+1 day'),
        );

        $this->assertFalse($duration->equalsTo($notEqualsDuration));
    }

    public static function doesNotEqualsDurations()
    {
        return [
            [new Duration(new \DateTimeImmutable('-1 day'))],
            [new Duration(endDate: new \DateTimeImmutable('+1 day'))],
            [
                new Duration(
                    new \DateTimeImmutable('-2 days'),
                    new \DateTimeImmutable('+1 day'),
                )
            ],
            [
                new Duration(
                    new \DateTimeImmutable('-1 day'),
                    new \DateTimeImmutable('+2 days'),
                )
            ],
        ];
    }
}