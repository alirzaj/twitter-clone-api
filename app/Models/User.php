<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
        'email_verified_at',
        'phone_verified_at',
        'followers_count',
        'followings_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'following_id',
            'follower_id',
            'id',
            'id'
        );
    }

    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'follower_id',
            'following_id',
            'id',
            'id'
        );
    }

    /**
     * add a column to query indicating that the given user is
     * following the current user in row or not
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeWithFollowingState(Builder $query, User $user): Builder
    {
        return $query->selectRaw(
    '(
                select exists(
                    select * from follows where following_id = users.id AND follower_id = ?
                )
            ) as following',
            [$user->id]
        );
    }

    /**
     * add a column to query indicating that the current user in row
     * has followed the given user or not
     *
     * @param Builder $query
     * @param User $user
     * @return Builder
     */
    public function scopeWithFollowState(Builder $query, User $user): Builder
    {
        return $query->selectRaw(
    '(
                select exists(
                 select * from follows where following_id = ? AND follower_id = users.id
                )
             ) as follows',
            [$user->id]
        );
    }
}
