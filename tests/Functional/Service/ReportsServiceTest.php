<?php

namespace Functional\Service;

use Moco\Entity\Report;
use Tests\Functional\Service\AbstractServiceTest;

class ReportsServiceTest extends AbstractServiceTest
{
    public function testGetAbsences(): void
    {
        $reports = $this->mocoClient->reports->getAbsences();
        $this->assertIsArray($reports);

        foreach ($reports as $report) {
            $this->assertInstanceOf(Report::class, $report);
            $this->assertIsObject($report->user);
            $this->assertObjectHasAttribute('id', $report->user);
            $this->assertObjectHasAttribute('firstname', $report->user);
            $this->assertObjectHasAttribute('lastname', $report->user);

            $this->assertIsFloat($report->total_vacation_days);
            $this->assertIsFloat($report->used_vacation_days);
            $this->assertIsFloat($report->planned_vacation_days);
            $this->assertIsFloat($report->sickdays);

            // Validate that vacation days values make sense
            $this->assertGreaterThanOrEqual(0, $report->total_vacation_days);
            $this->assertGreaterThanOrEqual(0, $report->used_vacation_days);
            $this->assertGreaterThanOrEqual(0, $report->planned_vacation_days);
            $this->assertGreaterThanOrEqual(0, $report->sickdays);
        }
    }

    public function testGetAbsencesWithActiveFilter(): void
    {
        $reports = $this->mocoClient->reports->getAbsences(['active' => true]);
        $this->assertIsArray($reports);

        foreach ($reports as $report) {
            $this->assertInstanceOf(Report::class, $report);
            $this->assertIsObject($report->user);
            $this->assertIsInt($report->user->id);
        }
    }

    public function testGetAbsencesWithYearFilter(): void
    {
        $currentYear = (int) date('Y');
        $reports = $this->mocoClient->reports->getAbsences(['year' => $currentYear]);
        $this->assertIsArray($reports);

        foreach ($reports as $report) {
            $this->assertInstanceOf(Report::class, $report);
        }
    }

    public function testGetAbsencesWithBothFilters(): void
    {
        $currentYear = (int) date('Y');
        $reports = $this->mocoClient->reports->getAbsences([
            'active' => true,
            'year' => $currentYear
        ]);
        $this->assertIsArray($reports);

        foreach ($reports as $report) {
            $this->assertInstanceOf(Report::class, $report);
            $this->assertObjectHasAttribute('id', $report->user);
            $this->assertIsInt($report->user->id);
        }
    }

    public function testGetAbsencesWithPreviousYear(): void
    {
        $previousYear = (int) date('Y') - 1;
        $reports = $this->mocoClient->reports->getAbsences(['year' => $previousYear]);
        $this->assertIsArray($reports);

        // Previous year data might be empty or contain historical data
        foreach ($reports as $report) {
            $this->assertInstanceOf(Report::class, $report);
        }
    }

    public function testGetAbsencesDataConsistency(): void
    {
        $reports = $this->mocoClient->reports->getAbsences();
        $this->assertIsArray($reports);

        foreach ($reports as $report) {
            $this->assertInstanceOf(Report::class, $report);

            // Test data consistency - used vacation days should not exceed total
            $this->assertLessThanOrEqual(
                $report->total_vacation_days + 5, // Allow some tolerance for edge cases
                $report->used_vacation_days + $report->planned_vacation_days,
                'Used and planned vacation days should not significantly exceed total vacation days'
            );

            // All vacation day values should be numeric
            $this->assertTrue(is_numeric($report->total_vacation_days));
            $this->assertTrue(is_numeric($report->used_vacation_days));
            $this->assertTrue(is_numeric($report->planned_vacation_days));
            $this->assertTrue(is_numeric($report->sickdays));
        }
    }
}
