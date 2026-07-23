<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PwdProfile;
use App\Models\JobVacancy;
use App\Http\Controllers\PWDController;
use Illuminate\Support\Facades\Cache;
use App\Models\AlgorithmWeight;

class MatchAlgorithmTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the Cache remember method to bypass database calls for algorithm weights
        Cache::shouldReceive('remember')
            ->zeroOrMoreTimes()
            ->with('algorithm_weights', 60, \Closure::class)
            ->andReturn(new AlgorithmWeight([
                's_weight' => 30, 
                'p_weight' => 25, 
                't_weight' => 20, 
                'w_weight' => 15, 
                'c_weight' => 10
            ]));
    }

    /**
     * Test case 1: Perfect Match (100% score)
     * All job requirements and facilities match the PWD profile.
     */
    public function test_perfect_match(): void
    {
        $pwdProfile = new PwdProfile([
            'skills' => ['PHP', 'Vue', 'Laravel'],
            'physical_needs' => ['Ramp', 'Elevator'],
            'tech_needs' => ['Screen Reader'],
            'work_arrangement' => ['Remote'],
            'sensory_needs' => ['Quiet Space'],
        ]);

        $job = new JobVacancy([
            'requirements' => ['PHP', 'Vue'],
            'physical_facilities' => ['Ramp', 'Elevator', 'Braille Signage'],
            'tech_support' => ['Screen Reader', 'Braille Keyboard'],
            'work_arrangement' => ['Remote', 'On-site'],
            'sensory_support' => ['Quiet Space'],
        ]);

        $controller = new PWDController();
        $result = $controller->calculateMatchScore($pwdProfile, $job);

        // Assert all individual rules score 100%
        $this->assertEquals(100, $result['s_score'], 'S-Rule (Skills) should be 100%');
        $this->assertEquals(100, $result['p_score'], 'P-Rule (Physical) should be 100%');
        $this->assertEquals(100, $result['t_score'], 'T-Rule (Tech) should be 100%');
        $this->assertEquals(100, $result['w_score'], 'W-Rule (Work) should be 100%');
        $this->assertEquals(100, $result['c_score'], 'C-Rule (Sensory) should be 100%');

        // Assert final score is 100%
        $this->assertEquals(100, $result['match_score'], 'Final match score should be 100%');
    }

    /**
     * Test case 2: Zero Match (0% score)
     * None of the job requirements or facilities match the PWD profile.
     */
    public function test_zero_match(): void
    {
        $pwdProfile = new PwdProfile([
            'skills' => ['Python', 'Django'],
            'physical_needs' => ['Ramp'],
            'tech_needs' => ['Screen Reader'],
            'work_arrangement' => ['Remote'],
            'sensory_needs' => ['Quiet Space'],
        ]);

        $job = new JobVacancy([
            'requirements' => ['PHP', 'Laravel'], // 0% skills match
            'physical_facilities' => ['Elevator'], // 0% physical match
            'tech_support' => ['Braille Keyboard'], // 0% tech match
            'work_arrangement' => ['On-site'], // 0% work match
            'sensory_support' => ['Dim Light'], // 0% sensory match
        ]);

        $controller = new PWDController();
        $result = $controller->calculateMatchScore($pwdProfile, $job);

        // Assert all individual rules score 0%
        $this->assertEquals(0, $result['s_score'], 'S-Rule should be 0%');
        $this->assertEquals(0, $result['p_score'], 'P-Rule should be 0%');
        $this->assertEquals(0, $result['t_score'], 'T-Rule should be 0%');
        $this->assertEquals(0, $result['w_score'], 'W-Rule should be 0%');
        $this->assertEquals(0, $result['c_score'], 'C-Rule should be 0%');

        // Assert final score is 0%
        $this->assertEquals(0, $result['match_score'], 'Final match score should be 0%');
    }

    /**
     * Test case 3: Partial Match (45% score under default weights)
     * - Skills: 2 of 4 requirements matched = 50% (S weight: 30% -> 15.0 pts)
     * - Physical: 1 of 2 needs matched = 50% (P weight: 25% -> 12.5 pts)
     * - Tech: 0 of 1 needs matched = 0% (T weight: 20% -> 0 pts)
     * - Work: 1 of 2 arrangements matched = 50% (W weight: 15% -> 7.5 pts)
     * - Sensory: 1 of 1 needs matched = 100% (C weight: 10% -> 10.0 pts)
     * Total: 15 + 12.5 + 0 + 7.5 + 10 = 45%
     */
    public function test_partial_match(): void
    {
        $pwdProfile = new PwdProfile([
            'skills' => ['PHP', 'Vue'],
            'physical_needs' => ['Ramp', 'Elevator'],
            'tech_needs' => ['Screen Reader'],
            'work_arrangement' => ['Remote', 'Hybrid'],
            'sensory_needs' => ['Quiet Space'],
        ]);

        $job = new JobVacancy([
            'requirements' => ['PHP', 'Laravel', 'Vue', 'Docker'],
            'physical_facilities' => ['Ramp'],
            'tech_support' => [],
            'work_arrangement' => ['Remote'],
            'sensory_support' => ['Quiet Space'],
        ]);

        $controller = new PWDController();
        $result = $controller->calculateMatchScore($pwdProfile, $job);

        // Assert individual scores
        $this->assertEquals(50, $result['s_score'], 'S-Rule (Skills) should be 50%');
        $this->assertEquals(50, $result['p_score'], 'P-Rule (Physical) should be 50%');
        $this->assertEquals(0, $result['t_score'], 'T-Rule (Tech) should be 0%');
        $this->assertEquals(50, $result['w_score'], 'W-Rule (Work) should be 50%');
        $this->assertEquals(100, $result['c_score'], 'C-Rule (Sensory) should be 100%');

        // Assert final score matches expectation of 45%
        $this->assertEquals(45, $result['match_score'], 'Final match score should be 45%');
    }
}
