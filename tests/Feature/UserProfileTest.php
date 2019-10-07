<?php

namespace Tests\Feature;

use Custodia\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{
    /**
     * Test user profile data attributes are persisted:
     *
     * Test User: Test Case User One
     *
     * Role: User
     * Home Type: Small Bungalow
     * Outdoor Spaces: Small Yard, Hedges
     * Driveway: No Driveway
     * Home Features: Porch, Walkway
     * Mobility Issues: No Mobility Issues
     **/
    public function testUserProfile()
    {
        $user = User::where('name', '=', 'Test Case User One')->firstOrFail();
        $this->assertNotNull($user);

        //Check home type
        $home_type = $user->userProfile->homeType;
        $this->assertNotNull($home_type);
        $this->assertEquals('Small Bungalow', $home_type->name);

        //Check outdoor spaces
        $outdoor_spaces = $user->userProfile->outdoorSpaces;
        $small_yard = false;
        $hedges = false;
        foreach ($outdoor_spaces as $outdoor_space){
            if ($outdoor_space->name == "Small Yard"){
                $small_yard = true;
            }
            if ($outdoor_space->name == "Hedges"){
                $hedges = true;
            }
        }
        $this->assertTrue($small_yard);
        $this->assertTrue($hedges);

        //Check driveway
        $driveways = $user->userProfile->drivewayTypes;
        $no_driveway = false;
        foreach ($driveways as $driveway){
            if ($driveway->name == "No Driveway"){
                $no_driveway = true;
            }
        }
        $this->assertTrue($no_driveway);

        //check home features
        $home_features = $user->userProfile->homeFeatures;
        $porch = false;
        $walkway = false;
        foreach ($home_features as $home_feature){
            if ($home_feature->name == "Porch"){
                $porch = true;
            }
            if ($home_feature->name == "Walkway"){
                $walkway = true;
            }
        }
        $this->assertTrue($porch);
        $this->assertTrue($walkway);

        //Check mobility issues
        $issues = $user->userProfile->mobilityIssues;
        $no_issues = false;
        foreach ($issues as $issue){
            if ($issue->name == "No Mobility Issues"){
                $no_issues = true;
            }
        }
        $this->assertTrue($no_issues);
    }
}
