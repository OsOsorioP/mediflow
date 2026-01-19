@component('mail::message')
    # Hello {{ $user->name }},

    You have been invited to join {{ $clinic->name }}.

    Your temporary password is: **{{ $temporaryPassword }}**

    @component('mail::button', ['url' => url('/login')])
        Login Here
    @endcomponent

    Please change your password after logging in.

    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
