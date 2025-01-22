<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\User;

class ShareCommonData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        if ($user) {
            $creatorProjects = Project::whereHas('projectCreators', function ($query) use ($user) {
                $query->where('creator_project', $user->id);
            })->get();

            $creatorProjectsIds = $creatorProjects->flatMap(function ($project) {
                return $project->projectCreators->map(function ($creator) {
                    return $creator->pivot->creator_project;
                });
            });

            $userNames = User::whereIn('id', $creatorProjectsIds)->pluck('profile_pict');
            $creatorPict = $userNames->first();

            // Bagikan creatorPict ke semua view
            view()->share('creatorPict', $creatorPict);
        }

        return $next($request);
    }
}
