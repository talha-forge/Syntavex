<?php

namespace App\Http\Middleware;

use App\Models\ApprovalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'toast' => fn (): ?array => $request->hasSession() && is_array($toast = $request->session()->get('toast'))
                    ? ['id' => (string) Str::uuid(), 'tone' => 'success', ...$toast]
                    : null,
            ],
            'pendingReviews' => fn (): int => $request->user() === null
                ? 0
                : ApprovalRequest::query()->where('status', 'pending')->count(),
        ];
    }
}
