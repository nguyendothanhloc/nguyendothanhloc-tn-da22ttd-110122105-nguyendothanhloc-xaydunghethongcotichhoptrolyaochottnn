<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatbotKnowledge;

class ChatbotKnowledgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            // Category: Chính sách hoàn tiền
            [
                'category' => 'Chính sách hoàn tiền',
                'question' => 'Học viên có thể được hoàn tiền học phí trong trường hợp nào?',
                'answer' => 'Học viên được hoàn lại 100% học phí nếu hủy đăng ký trước khi lớp khai giảng ít nhất 7 ngày. Hoàn 50% nếu hủy từ 3-6 ngày trước khai giảng. Không hoàn tiền nếu hủy trong vòng 2 ngày trước khai giảng hoặc sau khi lớp đã bắt đầu.',
                'keywords' => 'hoàn tiền, hủy khóa học, học phí, hoàn lại, đăng ký',
                'priority' => 90,
                'is_active' => true,
            ],
            [
                'category' => 'Chính sách hoàn tiền',
                'question' => 'Thời gian xử lý hoàn tiền mất bao lâu?',
                'answer' => 'Sau khi yêu cầu hoàn tiền được chấp thuận, trung tâm sẽ xử lý hoàn tiền trong vòng 7-10 ngày làm việc. Tiền sẽ được chuyển về tài khoản ngân hàng mà học viên đã đăng ký.',
                'keywords' => 'thời gian hoàn tiền, xử lý hoàn tiền, ngày làm việc',
                'priority' => 70,
                'is_active' => true,
            ],

            // Category: Quy định chuyển lớp
            [
                'category' => 'Quy định chuyển lớp',
                'question' => 'Học viên có thể chuyển sang lớp khác không?',
                'answer' => 'Có, học viên được phép chuyển lớp 1 lần miễn phí nếu thông báo trước ít nhất 3 ngày. Điều kiện: lớp mới phải cùng khóa học, cùng trình độ và còn chỗ trống. Học viên cần liên hệ với quản trị viên để được hỗ trợ.',
                'keywords' => 'chuyển lớp, đổi lớp, thay đổi lịch học, lớp khác',
                'priority' => 90,
                'is_active' => true,
            ],
            [
                'category' => 'Quy định chuyển lớp',
                'question' => 'Có mất phí khi chuyển lớp không?',
                'answer' => 'Lần chuyển lớp đầu tiên hoàn toàn miễn phí. Từ lần thứ 2 trở đi, học viên sẽ phải đóng phí hành chính 100.000 VNĐ cho mỗi lần chuyển.',
                'keywords' => 'phí chuyển lớp, chi phí, miễn phí, lần đầu',
                'priority' => 70,
                'is_active' => true,
            ],

            // Category: Thủ tục nghỉ học / bảo lưu
            [
                'category' => 'Thủ tục nghỉ học / bảo lưu',
                'question' => 'Làm thế nào để xin bảo lưu khóa học?',
                'answer' => 'Học viên cần gửi đơn xin bảo lưu qua email hoặc nộp trực tiếp tại văn phòng trung tâm. Thời gian bảo lưu tối đa là 6 tháng. Học viên có thể quay lại học với lớp tương đương trong thời gian bảo lưu mà không mất thêm chi phí.',
                'keywords' => 'bảo lưu, nghỉ học, tạm dừng, đơn xin nghỉ',
                'priority' => 90,
                'is_active' => true,
            ],
            [
                'category' => 'Thủ tục nghỉ học / bảo lưu',
                'question' => 'Có thể bảo lưu nhiều lần không?',
                'answer' => 'Mỗi khóa học chỉ được phép bảo lưu 1 lần duy nhất với thời hạn tối đa 6 tháng. Nếu hết thời hạn bảo lưu mà học viên không quay lại, sẽ được xem là tự động hủy khóa học và không được hoàn tiền.',
                'keywords' => 'số lần bảo lưu, bảo lưu lại, thời hạn',
                'priority' => 50,
                'is_active' => true,
            ],

            // Category: Điều kiện nhận ưu đãi / giảm giá
            [
                'category' => 'Điều kiện nhận ưu đãi / giảm giá',
                'question' => 'Học viên cũ có được giảm giá khi đăng ký khóa mới không?',
                'answer' => 'Có, học viên đã hoàn thành ít nhất 1 khóa học tại trung tâm được giảm 10% học phí cho khóa học tiếp theo. Học viên giới thiệu bạn bè đăng ký thành công sẽ nhận thêm 5% giảm giá hoặc 1 tháng học miễn phí.',
                'keywords' => 'giảm giá, ưu đãi, học viên cũ, giới thiệu bạn bè, khuyến mãi',
                'priority' => 90,
                'is_active' => true,
            ],
            [
                'category' => 'Điều kiện nhận ưu đãi / giảm giá',
                'question' => 'Học sinh, sinh viên có ưu đãi gì không?',
                'answer' => 'Học sinh THPT và sinh viên đại học được giảm 15% học phí khi xuất trình thẻ học sinh/sinh viên còn hiệu lực. Ưu đãi này không áp dụng đồng thời với các chương trình giảm giá khác.',
                'keywords' => 'học sinh, sinh viên, giảm giá, thẻ học sinh, ưu đãi',
                'priority' => 70,
                'is_active' => true,
            ],

            // Category: Khác
            [
                'category' => 'Khác',
                'question' => 'Trung tâm có cấp chứng chỉ sau khi hoàn thành khóa học không?',
                'answer' => 'Có, học viên hoàn thành khóa học với điểm danh đạt ít nhất 80% buổi học và đạt điểm thi cuối khóa từ 6.0 trở lên sẽ được cấp chứng chỉ hoàn thành khóa học của trung tâm.',
                'keywords' => 'chứng chỉ, hoàn thành khóa học, cấp chứng chỉ, điểm danh',
                'priority' => 70,
                'is_active' => true,
            ],
            [
                'category' => 'Khác',
                'question' => 'Làm sao để liên hệ với giáo viên của lớp?',
                'answer' => 'Học viên có thể xem thông tin liên hệ của giáo viên trong phần chi tiết lớp học trên hệ thống. Ngoài ra, học viên có thể gửi tin nhắn qua chatbot này và hệ thống sẽ tự động cung cấp thông tin liên hệ của giáo viên phụ trách.',
                'keywords' => 'liên hệ giáo viên, thông tin giáo viên, email giáo viên, số điện thoại',
                'priority' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            ChatbotKnowledge::firstOrCreate(
                [
                    'category' => $faq['category'],
                    'question' => $faq['question']
                ],
                $faq
            );
        }
    }
}
