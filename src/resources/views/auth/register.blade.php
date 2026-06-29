<x-guest-layout>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-semibold text-gray-800">Choose Your Role</h2>
        <p class="text-sm text-gray-600 mt-2">Select how you want to register</p>
    </div>

    <div class="space-y-4">
        <!-- Student Registration -->
        <a href="{{ route('register.student') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Register as Student</h3>
            <p class="text-sm text-gray-600">Enroll in courses, track your progress, and learn with our virtual assistant</p>
        </a>

        <!-- Teacher Registration -->
        <a href="{{ route('register.teacher') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Register as Teacher</h3>
            <p class="text-sm text-gray-600">Manage classes, track attendance, and assess student performance</p>
        </a>

        <!-- Admin Registration -->
        <a href="{{ route('register.admin') }}" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Register as Administrator</h3>
            <p class="text-sm text-gray-600">Manage the entire system, courses, teachers, and students</p>
        </a>
    </div>

    <div class="mt-6 text-center">
        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
            {{ __('Already registered?') }}
        </a>
    </div>
</x-guest-layout>
