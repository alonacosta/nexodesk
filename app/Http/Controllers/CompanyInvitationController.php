<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptCompanyInvitationRequest;
use App\Http\Requests\StoreCompanyInvitationRequest;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Notifications\CompanyInvitationNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CompanyInvitationController extends Controller
{
    public function store(StoreCompanyInvitationRequest $request, Company $company): RedirectResponse
    {
        $user = $request->user();

        $email = Str::lower($request->validated('email'));

        $alreadyMember = $company->users()
            ->whereRaw('LOWER(users.email) = ?', [$email])
            ->exists();

        if ($alreadyMember) {
            throw ValidationException::withMessages([
                'email' => __('invitations.already_member'),
            ]);
        }

        $token = Str::random(64);

        $company->invitations()->updateOrCreate(
            ['email' => $email],
            [
                'invited_by' => $user->id,
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addDays(7),
            ],
        );

        Notification::route('mail', $email)->notify(
            new CompanyInvitationNotification(
                companyName: $company->name,
                invitationUrl: route('invitations.show', ['token' => $token]),
            ),
        );

        return to_route('companies.members.index', [
            'company' => $company->slug,
        ]);
    }

    public function show(Request $request, string $token): Response
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $invitation = CompanyInvitation::query()
            ->with('company:id,name')
            ->where('token_hash', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->firstOrFail();

        abort_unless(
            Str::lower($user->email) === $invitation->email,
            403,
        );

        return Inertia::render('invitations/Show', [
            'companyName' => $invitation->company->name,
            'token' => $token,
        ]);
    }

    public function accept(AcceptCompanyInvitationRequest $request, string $token): RedirectResponse
    {
        $user = $request->user();

        abort_if($user === null, 401);

        $company = DB::transaction(function () use ($user, $token): Company {
            $invitation = CompanyInvitation::query()
                ->where('token_hash', hash('sha256', $token))
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                Str::lower($user->email) === $invitation->email,
                403,
            );

            $company = $invitation->company;

            $company->users()->syncWithoutDetaching([
                $user->id,
            ]);

            $invitation->delete();

            return $company;
        });

        return to_route('companies.show', [
            'company' => $company->slug,
        ]);
    }
}
