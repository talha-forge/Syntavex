<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ApprovalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => session('status'),
            'queue' => $this->queue(),
        ]);
    }

    private function queue(): array
    {
        $pending = ApprovalRequest::query()->where('status', 'pending')->with('runStep')->get();

        return [
            'pending' => $pending->count(),
            'frozen' => $pending
                ->filter(fn (ApprovalRequest $approval): bool => (bool) ($approval->runStep?->output_payload['irreversible'] ?? false))
                ->count(),
        ];
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/', 303)->with('toast', [
            'tone' => 'success',
            'message' => 'Signed out. Enter the demo again anytime.',
        ]);
    }
}
