<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Báo Cáo Tiến Độ Học Tập') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Certificate Eligibility Alert -->
            @if($isCertificateEligible)
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700 font-medium">
                                🎉 Chúc mừng! Bạn đã đủ điều kiện nhận chứng chỉ hoàn thành khóa học!
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700 font-medium">
                                Bạn chưa đủ điều kiện nhận chứng chỉ. Hãy cố gắng cải thiện điểm danh và điểm số!
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Overall Progress Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Attendance Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Tỷ Lệ Điểm Danh</h3>
                            <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-4xl font-bold {{ $attendanceRate >= 80 ? 'text-green-600' : ($attendanceRate >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($attendanceRate, 1) }}%
                        </div>
                        <div class="mt-4 w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $attendanceRate >= 80 ? 'bg-green-600' : ($attendanceRate >= 60 ? 'bg-yellow-500' : 'bg-red-600') }}" 
                                 style="width: {{ $attendanceRate }}%"></div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600">
                            {{ $presentCount }} / {{ $totalAttendances }} buổi có mặt
                        </div>
                        <div class="mt-2">
                            @if($isAttendanceEligible)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Đạt yêu cầu (>= {{ $attendanceThreshold }}%)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ✗ Chưa đạt (cần >= {{ $attendanceThreshold }}%)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Assessment Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Điểm Trung Bình</h3>
                            <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="text-4xl font-bold {{ $averagePercentage >= 80 ? 'text-green-600' : ($averagePercentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($averagePercentage, 1) }}%
                        </div>
                        <div class="mt-4 w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $averagePercentage >= 80 ? 'bg-green-600' : ($averagePercentage >= 60 ? 'bg-yellow-500' : 'bg-red-600') }}" 
                                 style="width: {{ $averagePercentage }}%"></div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600">
                            {{ $totalAssessments }} bài đánh giá
                        </div>
                        <div class="mt-2">
                            @if($isScoreEligible)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Đạt yêu cầu (>= {{ $scoreThreshold }}%)
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ✗ Chưa đạt (cần >= {{ $scoreThreshold }}%)
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Completion Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Hoàn Thành Khóa Học</h3>
                            <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-4xl font-bold {{ $completionPercentage >= 80 ? 'text-green-600' : ($completionPercentage >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($completionPercentage, 1) }}%
                        </div>
                        <div class="mt-4 w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $completionPercentage >= 80 ? 'bg-green-600' : ($completionPercentage >= 60 ? 'bg-yellow-500' : 'bg-red-600') }}" 
                                 style="width: {{ $completionPercentage }}%"></div>
                        </div>
                        <div class="mt-3 text-sm text-gray-600">
                            {{ $completedSchedules }} / {{ $totalSchedules }} buổi học
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Chi Tiết Điểm Danh</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-700">{{ $totalAttendances }}</div>
                            <div class="text-sm text-gray-600 mt-1">Tổng số buổi</div>
                        </div>
                        <div class="text-center p-4 bg-green-50 rounded-lg">
                            <div class="text-3xl font-bold text-green-600">{{ $presentCount }}</div>
                            <div class="text-sm text-gray-600 mt-1">Có mặt</div>
                        </div>
                        <div class="text-center p-4 bg-yellow-50 rounded-lg">
                            <div class="text-3xl font-bold text-yellow-600">{{ $lateCount }}</div>
                            <div class="text-sm text-gray-600 mt-1">Đi muộn</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 rounded-lg">
                            <div class="text-3xl font-bold text-red-600">{{ $absentCount }}</div>
                            <div class="text-sm text-gray-600 mt-1">Vắng mặt</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Statistics -->
            @if($totalAssessments > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Thống Kê Đánh Giá</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-3xl font-bold text-blue-600">{{ number_format($averageScore, 2) }}</div>
                                <div class="text-sm text-gray-600 mt-1">Điểm trung bình</div>
                            </div>
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-3xl font-bold text-green-600">
                                    @if($highestScore)
                                        {{ number_format(($highestScore->score / $highestScore->max_score) * 100, 1) }}%
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 mt-1">Điểm cao nhất</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-3xl font-bold text-red-600">
                                    @if($lowestScore)
                                        {{ number_format(($lowestScore->score / $lowestScore->max_score) * 100, 1) }}%
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600 mt-1">Điểm thấp nhất</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Progress by Class -->
            @if(count($progressByClass) > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Tiến Độ Theo Lớp</h3>
                        <div class="space-y-4">
                            @foreach($progressByClass as $item)
                                <div class="border-l-4 {{ $item['attendance_rate'] >= 80 && $item['average_score'] >= 70 ? 'border-green-500' : 'border-yellow-500' }} pl-4 py-2">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="font-semibold text-gray-800">{{ $item['class']->course->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $item['class']->name }}</p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-600">Điểm danh:</span>
                                                <span class="font-semibold {{ $item['attendance_rate'] >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ number_format($item['attendance_rate'], 1) }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="h-2 rounded-full {{ $item['attendance_rate'] >= 80 ? 'bg-green-600' : 'bg-yellow-500' }}" 
                                                     style="width: {{ $item['attendance_rate'] }}%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex justify-between text-sm mb-1">
                                                <span class="text-gray-600">Điểm số:</span>
                                                <span class="font-semibold {{ $item['average_score'] >= 70 ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ $item['assessments_count'] > 0 ? number_format($item['average_score'], 1) . '%' : 'Chưa có' }}
                                                </span>
                                            </div>
                                            @if($item['assessments_count'] > 0)
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="h-2 rounded-full {{ $item['average_score'] >= 70 ? 'bg-green-600' : 'bg-yellow-500' }}" 
                                                         style="width: {{ $item['average_score'] }}%"></div>
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-500 italic">Chưa có bài đánh giá</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Quick Links -->
            <div class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Xem Chi Tiết</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <a href="{{ route('student.schedule') }}" class="text-center p-4 bg-white rounded-lg hover:shadow-md transition">
                            <svg class="h-8 w-8 mx-auto text-blue-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div class="text-sm font-medium text-gray-700">Lịch học</div>
                        </a>
                        <a href="{{ route('student.attendance') }}" class="text-center p-4 bg-white rounded-lg hover:shadow-md transition">
                            <svg class="h-8 w-8 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm font-medium text-gray-700">Điểm danh</div>
                        </a>
                        <a href="{{ route('student.assessments') }}" class="text-center p-4 bg-white rounded-lg hover:shadow-md transition">
                            <svg class="h-8 w-8 mx-auto text-purple-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <div class="text-sm font-medium text-gray-700">Kết quả đánh giá</div>
                        </a>
                        <a href="{{ route('enrollments.index') }}" class="text-center p-4 bg-white rounded-lg hover:shadow-md transition">
                            <svg class="h-8 w-8 mx-auto text-yellow-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <div class="text-sm font-medium text-gray-700">Lớp của tôi</div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
