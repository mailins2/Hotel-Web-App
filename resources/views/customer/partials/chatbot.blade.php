<div class="customer-chatbot" data-customer-chatbot>
  <section class="customer-chatbot-panel" data-chatbot-panel aria-label="Trợ lý ảo Peach Valley" hidden>
    <header class="customer-chatbot-header">
      <div>
        <p class="customer-chatbot-kicker">Peach Valley</p>
        <h2>Trợ lý đặt phòng</h2>
      </div>
      <button class="customer-chatbot-close" type="button" data-chatbot-close aria-label="Đóng chat">×</button>
    </header>

    <div class="customer-chatbot-messages" data-chatbot-messages aria-live="polite">
      <div class="customer-chatbot-message is-bot">
        Xin chào, mình có thể hỗ trợ bạn về đặt phòng, thanh toán, khuyến mãi và thông tin khách sạn.
      </div>
    </div>

    <div class="customer-chatbot-suggestions" aria-label="Cau hoi goi y">
      <button type="button" data-chatbot-suggestion="Tôi muốn đặt phòng">Đặt phòng</button>
      <button type="button" data-chatbot-suggestion="Giờ nhận phòng và trả phòng là mấy giờ?">Nhận/trả phòng</button>
      <button type="button" data-chatbot-suggestion="Khách sạn có dịch vụ gì?">Dịch vụ</button>
      <button type="button" data-chatbot-suggestion="Tôi cần liên hệ khách sạn">Liên hệ</button>
    </div>

    <form class="customer-chatbot-form" data-chatbot-form>
      <input
        type="text"
        data-chatbot-input
        placeholder="Nhập câu hỏi của bạn..."
        autocomplete="off"
        aria-label="Nhập câu hỏi cho chatbot"
      >
      <button type="submit">Gửi</button>
    </form>
  </section>

  <button class="customer-chatbot-toggle" type="button" data-chatbot-toggle aria-label="Mo chat ho tro">
    <span class="icon-comment" aria-hidden="true"></span>
  </button>
</div>
