<x-mail::message>
# Enrollment Confirmation

Dear {{ $studentName }},

Thank you for enrolling in our course! We're excited to have you join us.

## Course Details

- **Course:** {{ $courseName }}
- **Class:** {{ $className }}
- **Teacher:** {{ $teacherName }}
- **Start Date:** {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}
- **End Date:** {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
- **Enrollment Date:** {{ \Carbon\Carbon::parse($enrollmentDate)->format('F d, Y') }}

## Next Steps

To confirm your enrollment, please complete your payment within 7 days. You can view your enrollment details and payment information by clicking the button below.

<x-mail::button :url="url('/enrollments')">
View My Enrollments
</x-mail::button>

If you have any questions, please don't hesitate to contact us.

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
