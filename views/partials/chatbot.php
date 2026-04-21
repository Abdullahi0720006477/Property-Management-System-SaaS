<!-- Chatbot Toggle Button -->
<button id="chatbotToggle" class="chatbot-toggle" aria-label="Open chat assistant">
    <i class="bi bi-chat-dots-fill"></i>
    <span class="chatbot-pulse"></span>
</button>

<!-- Chatbot Window -->
<div id="chatbotWindow" class="chatbot-window hidden">
    <div class="chatbot-header">
        <div class="chatbot-header-info">
            <div class="chatbot-avatar">
                <i class="bi bi-robot"></i>
            </div>
            <div>
                <h6 class="mb-0">PropMS Assistant</h6>
                <span class="chatbot-status">Online</span>
            </div>
        </div>
        <button id="chatbotClose" class="chatbot-close"><i class="bi bi-x-lg"></i></button>
    </div>
    <div id="chatbotMessages" class="chatbot-messages"></div>
    <div class="chatbot-quick-actions">
        <button class="quick-action" data-message="What can this system do?">Features</button>
        <button class="quick-action" data-message="How do I add a property?">Add Property</button>
        <button class="quick-action" data-message="How do I record a payment?">Payments</button>
        <button class="quick-action" data-message="Help me navigate">Navigation</button>
    </div>
    <div class="chatbot-input">
        <input type="text" id="chatbotInput" placeholder="Ask me anything about the system..." autocomplete="off">
        <button id="chatbotSend"><i class="bi bi-send-fill"></i></button>
    </div>
</div>
