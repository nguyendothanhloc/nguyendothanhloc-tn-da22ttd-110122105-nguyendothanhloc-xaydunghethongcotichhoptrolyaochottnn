# Tóm tắt vấn đề Chatbot và Giải pháp

## Vấn đề hiện tại

Chatbot không thể trả lời câu hỏi phức tạp vì Google Gemini API liên tục báo lỗi 404:
- Model `gemini-pro` không tồn tại với API v1beta
- Model `gemini-1.5-flash` cũng không tồn tại với API v1beta  
- Model `gemini-1.5-flash-latest` không tồn tại với API v1
- API key có thể không hợp lệ hoặc Google đang thay đổi API

## Giải pháp 1: Sử dụng OpenAI thay vì Gemini (KHUYẾN NGHỊ)

OpenAI API ổn định hơn nhiều và dễ sử dụng hơn:

1. Đăng ký tài khoản tại: https://platform.openai.com/
2. Tạo API key tại: https://platform.openai.com/api-keys
3. Nạp $5-10 vào tài khoản (rất rẻ, ~0.002$ mỗi lần chat)
4. Thay đổi code để dùng OpenAI

## Giải pháp 2: Tạo API key mới từ Google

1. Truy cập: https://aistudio.google.com/app/apikey
2. Tạo API key mới
3. Copy key và paste vào `.env`: `GEMINI_API_KEY=your_new_key_here`

## Giải pháp 3: Vô hiệu hóa AI, chỉ dùng Rule-based + FAQ

Nếu không muốn dùng API bên ngoài, chatbot vẫn hoạt động được với:
- Rule-based patterns (35+ câu hỏi cơ bản)
- FAQ database (12 entries, admin có thể thêm nhiều hơn)

Nhưng sẽ KHÔNG trả lời được câu hỏi phức tạp như:
- "Tôi học không tốt, phải làm sao?"
- "Làm thế nào để học tiếng Anh hiệu quả?"

## Khuyến nghị

**Dùng OpenAI** vì:
✅ API ổn định nhất
✅ Documentation rõ ràng
✅ Giá rẻ (~0.002$ mỗi lần chat)
✅ Không cần lo API thay đổi
✅ Chất lượng trả lời tốt hơn Gemini

Tôi có thể giúp bạn chuyển sang OpenAI trong vòng 5 phút nếu bạn có API key.
