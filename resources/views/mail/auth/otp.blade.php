<x-mail::message>
# Hello {{ $user->name }}!

We received a request to access your **{{ config('app.name') }}** account.

@if ($otp->type == 'password-reset')
## Reset Your Password
@else
## Verify Your Account
@endif

Your one-time password (OTP) is:

<x-mail::panel>
<div style="font-size: 32px; font-weight: bold; letter-spacing: 8px; text-align: center; color: #2d3748;">
{{ $otp->code }}
</div>
</x-mail::panel>

---

**Important Security Information:**
- This OTP is valid for **10 minutes only**
- Do not share this code with anyone
- Our team will never ask for your OTP

If you didn't request this code, please ignore this email or contact our support team immediately.

Thanks,<br>
**{{ config('app.name') }} Team**

<x-mail::subcopy>
This is an automated message, please do not reply to this email.
</x-mail::subcopy>
</x-mail::message>
