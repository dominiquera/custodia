<?php

namespace Custodia\Services;

use Custodia\UserProfile;

class UserService
{
    /**
     * Get the location data for all users (grouped by [city, state])
     *
     * @return UserProfile[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getUserLocations()
    {
        $locations = UserProfile::all(['city','state','longitude','latitude'])
            ->unique('city', 'state')
            ->where('city', '!=', null)
            ->where('state', '!=', null)
            ->where('longitude', '!=', null)
            ->where('latitude', '!=', null);

        return $locations;
    }
}