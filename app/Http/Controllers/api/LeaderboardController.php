<?php

namespace App\Http\Controllers\api;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class LeaderboardController extends Controller
{

public function topUsers(): JsonResponse
{
 $topUsers = Cache::remember('top_users_leaderboard', 120, function () {
        return User::select('users.id', 'users.name', 'users.image', 'wallets.points')
            ->join('wallets', 'users.id', '=', 'wallets.user_id')
            ->orderByDesc('wallets.points')
            ->limit(10)
            ->get();
    });

    if ($topUsers->isEmpty()) {
        return response()->json([
            'message' => 'لا يوجد مستخدمون في قائمة المتصدرين حاليًا.',
            'users' => [],
        ], 200);
    }

    return response()->json([
        'message' => 'Top 10 users with highest points',
        'users' => $topUsers->map(fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'profile_picture' => $user->image ? url('storage/' . $user->image) : url('images/default.webp'),
            'points' => $user->points ?? 0,
        ]),
    ]);
}

}
