<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kết Quả Đánh Giá') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overall Statistics -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-600 text-sm">Tổng số bài đánh giá</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $totalScores }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-600 text-sm">Điểm trung bình</div>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($averageScore, 2) }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-gray-600 text-sm">Phần trăm hoàn thành</div>
                    <div class="text-3xl font-bold text-purple-600">{{ number_format($averagePercentage, 1) }}%</div>
                </div>
            </div>

            <!-- Assessments by Class -->
            @forelse($classes as $class)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <!-- Class Header -->
                        <div class="mb-4 pb-4 border-b">
                            <h3 class="text-xl font-bold text-gray-800">{{ $class->course->name }}</h3>
                            <p class="text-sm text-gray-600">
                                {{ $class->name }} - Giảng viên: {{ $class->teacher->user->name ?? 'Chưa có' }}
                            </p>
                        </div>

                        <!-- Assessments Table -->
                        @if(isset($scoresByClass[$class->id]) && count($scoresByClass[$class->id]) > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Tên bài đánh giá
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Loại
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Ngày
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Điểm
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Phần trăm
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Đánh giá
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($scoresByClass[$class->id] as $item)
                                            @php
                                                $assessment = $item['assessment'];
                                                $score = $item['score'];
                                                $percentage = $score ? ($score->score / $assessment->max_score) * 100 : null;
                                                $grade = $percentage ? 
                                                    ($percentage >= 90 ? 'A' : 
                                                    ($percentage >= 80 ? 'B' : 
                                                    ($percentage >= 70 ? 'C' : 
                                                    ($percentage >= 60 ? 'D' : 'F')))) : null;
                                                $gradeColor = $percentage ? 
                                                    ($percentage >= 80 ? 'text-green-600' : 
                                                    ($percentage >= 60 ? 'text-yellow-600' : 'text-red-600')) : 'text-gray-400';
                                            @endphp
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $assessment->name }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $assessment->type === 'exam' ? 'bg-red-100 text-red-800' : 
                                                           ($assessment->type === 'test' ? 'bg-yellow-100 text-yellow-800' : 
                                                           'bg-blue-100 text-blue-800') }}">
                                                        {{ ucfirst($assessment->type) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($assessment->date)->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($score)
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ number_format($score->score, 2) }} / {{ number_format($assessment->max_score, 2) }}
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-400 italic">Chưa có điểm</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($percentage !== null)
                                                        <div class="flex items-center">
                                                            <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                                                <div class="h-2.5 rounded-full {{ $percentage >= 80 ? 'bg-green-600' : ($percentage >= 60 ? 'bg-yellow-500' : 'bg-red-600') }}" 
                                                                     style="width: {{ $percentage }}%"></div>
                                                            </div>
                                                            <span class="text-sm font-medium {{ $gradeColor }}">
                                                                {{ number_format($percentage, 1) }}%
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($grade)
                                                        <span class="px-3 py-1 text-sm font-bold {{ $gradeColor }}">
                                                            {{ $grade }}
                                                        </span>
                                                    @else
                                                        <span class="text-sm text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Class Summary -->
                            @php
                                $classScores = collect($scoresByClass[$class->id])
                                    ->filter(fn($item) => $item['score'] !== null)
                                    ->pluck('score');
                                $classAverage = $classScores->count() > 0 ? $classScores->avg('score') : 0;
                                $classCount = $classScores->count();
                            @endphp
                            @if($classCount > 0)
                                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-600">
                                        Trung bình lớp: 
                                    </span>
                                    <span class="text-lg font-bold text-blue-600">
                                        {{ number_format($classAverage, 2) }} ({{ $classCount }} bài)
                                    </span>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2">Chưa có bài đánh giá nào cho lớp này</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có lớp học nào</h3>
                        <p class="mt-1 text-sm text-gray-500">Bạn cần đăng ký vào lớp học để xem kết quả đánh giá</p>
                        <div class="mt-6">
                            <a href="{{ route('courses.browse') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Xem khóa học
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse

            <!-- Grade Scale Reference -->
            <div class="mt-6 bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Thang điểm:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-sm">
                        <div class="flex items-center">
                            <span class="px-2 py-1 bg-green-100 text-green-800 font-bold rounded mr-2">A</span>
                            <span class="text-gray-600">90-100%</span>
                        </div>
                        <div class="flex items-center">
                            <span class="px-2 py-1 bg-green-100 text-green-700 font-bold rounded mr-2">B</span>
                            <span class="text-gray-600">80-89%</span>
                        </div>
                        <div class="flex items-center">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-700 font-bold rounded mr-2">C</span>
                            <span class="text-gray-600">70-79%</span>
                        </div>
                        <div class="flex items-center">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 font-bold rounded mr-2">D</span>
                            <span class="text-gray-600">60-69%</span>
                        </div>
                        <div class="flex items-center">
                            <span class="px-2 py-1 bg-red-100 text-red-800 font-bold rounded mr-2">F</span>
                            <span class="text-gray-600">< 60%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
