{{-- resources/views/legal/terms.blade.php --}}
<x-app-layout>
    <x-slot name="title">Terms of Use</x-slot>

    <div class="max-w-3xl mx-auto p-6 space-y-6">
        <h1 class="text-2xl font-bold">Terms of Use</h1>

        <p class="text-gray-700">
            Welcome to BOOKiFY. These Terms of Use (“Terms”) govern your use of our service.
            By creating an account or using the service, you agree to these Terms.
        </p>

        <h2 class="text-xl font-semibold">1. Accounts</h2>
        <p class="text-gray-700">
            You’re responsible for maintaining the confidentiality of your account and password.
            You must provide accurate information and promptly update it as needed.
        </p>

        <h2 class="text-xl font-semibold">2. Event Participation</h2>
        <p class="text-gray-700">
            Booking an event constitutes your acceptance of the event’s rules set by the organizer.
            Organizers are responsible for the accuracy of event details and capacity.
        </p>

        <h2 class="text-xl font-semibold">3. Acceptable Use</h2>
        <p class="text-gray-700">
            You agree not to misuse the service, including but not limited to attempting to
            access data without authorization or disrupting the service.
        </p>

        <h2 class="text-xl font-semibold">4. Data & Privacy</h2>
        <p class="text-gray-700">
            Please review our <a class="text-indigo-600 underline" href="{{ route('privacy.policy') }}">Privacy Policy</a>
            to understand how we collect and use your information.
        </p>

        <h2 class="text-xl font-semibold">5. Changes</h2>
        <p class="text-gray-700">
            We may update these Terms from time to time. Material changes will be announced on this page.
        </p>

        <h2 class="text-xl font-semibold">6. Contact</h2>
        <p class="text-gray-700">
            If you have questions about these Terms, contact us at support@example.com.
        </p>
    </div>
</x-app-layout>
