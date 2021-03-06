<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * Return an array of the most recent locations of a user
     *
     * @return App\UserLocation User Locations
     */
    public function locations()
    {
        return $this
            ->hasMany('App\UserLocation')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Return the trip where the user is a passenger
     *
     * @return App\Trip Trip
     */
    public function trip()
    {
        return $this->hasOne('App\Trip', 'passenger_id', 'id');
    }

    /**
     * Checks if a user's last recorded location was less than 60 minutes ago,
     * if the user location is frozen, then they will be forcefully shown
     */
    public function scopeRecentlyLogged($query)
    {
        return $query
            ->where('freeze_location', true)
            ->orWhereHas('locations', function ($location) {
                $tenMinsAgo = Carbon::now()->subMinutes(60)->toDateTimeString();
                $location->where('created_at', '>', $tenMinsAgo);
            });
    }
}
