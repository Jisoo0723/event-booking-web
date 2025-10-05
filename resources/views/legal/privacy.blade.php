<x-app-layout>
  <x-slot name="title">Privacy Policy</x-slot>

  <div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Privacy Policy</h1>
    <p class="text-sm text-gray-500 mb-6">Last updated: 30 September 2025</p>

    <p class="mb-4">
      This Privacy Policy explains how <strong>{{ config('app.name') }}</strong> (“we”, “us”, “our”) collects, uses, and protects information when you use our event and booking website (the “Service”). 
      This project is for educational purposes.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Information We Collect</h2>
    <ul class="list-disc list-inside mb-4 space-y-1">
      <li>Account information: name, email address, password (hashed).</li>
      <li>Usage and booking data: events you create or book, dates/times, capacity and booking counts.</li>
      <li>Log/cookie data: basic technical data to keep you signed in and operate the Service.</li>
      <li>Communications: messages you send to us (e.g., support).</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">How We Use Information</h2>
    <ul class="list-disc list-inside mb-4 space-y-1">
      <li>To provide core features (authentication, event listings, bookings, dashboards).</li>
      <li>To maintain security, prevent abuse/over-booking, and troubleshoot.</li>
      <li>To communicate important updates about the Service.</li>
      <li>To comply with applicable policies or law.</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">Sharing</h2>
    <p class="mb-4">
      We do not sell your personal information. We may share minimal data with service providers 
      (e.g., hosting, email) who process it on our behalf under appropriate safeguards, or when required by law.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Data Retention</h2>
    <p class="mb-4">
      We keep account and booking data while your account is active or as needed to operate the Service. 
      You may request deletion of your account; we may retain limited records as required for operational or legal reasons.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Security</h2>
    <p class="mb-4">
      We use reasonable measures to protect information (e.g., hashed passwords, role-based access). 
      No method of transmission or storage is 100% secure.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Your Choices & Rights</h2>
    <ul class="list-disc list-inside mb-4 space-y-1">
      <li>Access, update, or delete your account by contacting us.</li>
      <li>Control cookies via your browser settings (some features may not work without essential cookies).</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">Children’s Privacy</h2>
    <p class="mb-4">
      The Service is not intended for children under 13. We do not knowingly collect data from children under 13.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Changes to This Policy</h2>
    <p class="mb-4">
      We may update this Policy. We will post the new version with a revised “Last updated” date.
    </p>

    <h2 class="text-xl font-semibold mt-6 mb-2">Contact</h2>
    <p class="mb-4">
      Questions about this Policy: <a href="mailto:support@example.com" class="text-indigo-600 hover:underline">support@example.com</a>
    </p>
  </div>
</x-app-layout>
