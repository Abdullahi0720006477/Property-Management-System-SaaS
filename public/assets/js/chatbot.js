/**
 * PropMS Chatbot - Knowledge-based assistant
 */
var ChatBot = {
    knowledge: [
        {
            keywords: ['hello', 'hi', 'hey', 'greetings', 'good morning', 'good afternoon'],
            response: "Hello! I'm the **PropMS Assistant**. I can help you navigate the property management system, answer questions about features, and guide you through common tasks. What would you like to know?"
        },
        {
            keywords: ['what', 'system', 'features', 'can do', 'overview', 'about'],
            response: "**PropMS** is a complete property management system. Key features include:\n\n- **Property & Unit Management** - Add and manage properties and units\n- **Tenant Management** - Track tenant information and leases\n- **Lease Management** - Create, renew, and terminate leases\n- **Payment Tracking** - Record payments and M-Pesa integration\n- **Maintenance Requests** - Handle repair and maintenance tickets\n- **Expense Tracking** - Monitor property-related expenses\n- **Reports & Analytics** - Generate financial and occupancy reports\n- **Notifications** - Automated alerts for due payments and expiring leases"
        },
        {
            keywords: ['navigate', 'menu', 'sidebar', 'find', 'where', 'go to', 'how to get'],
            response: "Use the **sidebar menu** on the left to navigate:\n\n- **Dashboard** - Overview of key metrics\n- **Properties** - Manage your properties\n- **Units** - View and manage units\n- **Tenants** - Tenant directory\n- **Leases** - Lease agreements\n- **Payments** - Payment records\n- **Maintenance** - Maintenance requests\n- **Expenses** - Expense tracking\n- **Reports** - Analytics and reports\n\nOn mobile, tap the **hamburger icon** at the top to open the sidebar."
        },
        {
            keywords: ['add property', 'new property', 'create property', 'register property'],
            response: "To **add a new property**:\n\n1. Go to **Properties** in the sidebar\n2. Click the **\"Add Property\"** button\n3. Fill in the property details (name, address, type, etc.)\n4. Click **Save**\n\nAfter creating a property, you can add units to it."
        },
        {
            keywords: ['add unit', 'new unit', 'create unit', 'register unit'],
            response: "To **add a new unit**:\n\n1. Go to **Units** in the sidebar\n2. Click **\"Add Unit\"**\n3. Select the **property** it belongs to\n4. Enter the unit number, type, rent amount, and other details\n5. Click **Save**\n\nUnits can be marked as occupied or vacant."
        },
        {
            keywords: ['add tenant', 'new tenant', 'create tenant', 'register tenant'],
            response: "To **add a new tenant**:\n\n1. Go to **Tenants** in the sidebar\n2. Click **\"Add Tenant\"**\n3. Enter the tenant's full name, email, phone, and ID number\n4. Click **Save**\n\nOnce added, you can create a lease to assign them to a unit."
        },
        {
            keywords: ['lease', 'agreement', 'contract', 'rental agreement', 'create lease', 'new lease'],
            response: "To **manage leases**:\n\n1. Go to **Leases** in the sidebar\n2. Click **\"Create Lease\"** to add a new one\n3. Select the **tenant**, **unit**, start/end dates, and rent amount\n4. Click **Save**\n\nYou can also **renew** or **terminate** existing leases from the lease details page. The system automatically tracks expiring leases and sends notifications."
        },
        {
            keywords: ['payment', 'pay', 'mpesa', 'm-pesa', 'record payment', 'rent payment', 'money'],
            response: "To **record a payment**:\n\n1. Go to **Payments** in the sidebar\n2. Click **\"Record Payment\"**\n3. Select the **lease**, enter the amount, date, and payment method\n4. For **M-Pesa** payments, enter the transaction code\n5. Click **Save**\n\nThe system tracks payment status (paid, pending, overdue) and generates receipts."
        },
        {
            keywords: ['maintenance', 'repair', 'fix', 'broken', 'request', 'issue', 'ticket'],
            response: "To **handle maintenance requests**:\n\n1. Go to **Maintenance** in the sidebar\n2. Tenants can submit requests, or you can create one via **\"New Request\"**\n3. Set the **priority** (low, medium, high, urgent)\n4. Assign a status: **Open, In Progress, Completed, or Closed**\n5. Track all requests and their resolution\n\nTenants receive notifications when their request status changes."
        },
        {
            keywords: ['report', 'analytics', 'statistics', 'financial', 'occupancy', 'summary'],
            response: "To **view reports**:\n\n1. Go to **Reports** in the sidebar\n2. Choose a report type:\n   - **Financial Report** - Income, expenses, and profit\n   - **Occupancy Report** - Unit vacancy and occupancy rates\n   - **Payment Report** - Payment collection summary\n3. Filter by date range and property\n4. Reports can be viewed on screen for analysis"
        },
        {
            keywords: ['login', 'password', 'sign in', 'account', 'forgot', 'reset password', 'credentials'],
            response: "For **login and account** help:\n\n- Go to the **login page** and enter your email and password\n- If you forgot your password, click **\"Forgot Password\"** and follow the instructions\n- Contact your **administrator** if you cannot access your account\n- Different users have different roles: **Admin**, **Manager**, or **Tenant**"
        },
        {
            keywords: ['notification', 'alert', 'remind', 'bell', 'message'],
            response: "**Notifications** keep you informed about:\n\n- **Payment due** reminders\n- **Lease expiring** alerts\n- **Maintenance** request updates\n- **New tenant** assignments\n\nCheck the **bell icon** in the header to view your notifications. Unread notifications show a badge count."
        },
        {
            keywords: ['dashboard', 'home', 'main page', 'overview', 'summary'],
            response: "The **Dashboard** shows a quick overview:\n\n- **Total Properties** and units count\n- **Occupancy Rate** percentage\n- **Revenue** collected this month\n- **Pending Payments** amount\n- **Recent Activities** and quick charts\n\nIt's the first page you see after logging in."
        },
        {
            keywords: ['expense', 'cost', 'spending', 'bill', 'track expense'],
            response: "To **track expenses**:\n\n1. Go to **Expenses** in the sidebar\n2. Click **\"Add Expense\"**\n3. Select the **property**, enter the category, amount, date, and description\n4. Click **Save**\n\nExpenses are included in financial reports to calculate net profit."
        },
        {
            keywords: ['role', 'admin', 'manager', 'permission', 'access'],
            response: "The system has **three roles**:\n\n- **Admin** - Full access to everything, can manage users and all properties\n- **Manager** - Can manage assigned properties, units, tenants, and related data\n- **Tenant** - Can view their own lease, payments, and submit maintenance requests\n\nEach role sees only the data relevant to them."
        },
        {
            keywords: ['thank', 'thanks', 'great', 'awesome', 'helpful', 'bye', 'goodbye'],
            response: "You're welcome! If you have more questions, feel free to ask anytime. I'm always here to help you with the property management system. Have a great day!"
        }
    ],

    fallbackResponse: "I'm not sure I understand that question. Here are some things I can help with:\n\n- **System features** and overview\n- **Navigation** guidance\n- **Adding** properties, units, or tenants\n- **Lease** management\n- **Payment** recording\n- **Maintenance** requests\n- **Reports** and analytics\n\nTry asking about any of these topics!",

    findResponse: function (input) {
        var lowerInput = input.toLowerCase();
        var tokens = lowerInput.split(/\s+/);
        var bestMatch = null;
        var bestScore = 0;

        for (var i = 0; i < this.knowledge.length; i++) {
            var entry = this.knowledge[i];
            var score = 0;

            for (var j = 0; j < entry.keywords.length; j++) {
                var keyword = entry.keywords[j];
                // Exact phrase match scores higher
                if (lowerInput.indexOf(keyword) !== -1) {
                    score += keyword.split(/\s+/).length * 2;
                } else {
                    // Check individual keyword words against input tokens
                    var kwTokens = keyword.split(/\s+/);
                    for (var k = 0; k < kwTokens.length; k++) {
                        for (var t = 0; t < tokens.length; t++) {
                            if (tokens[t] === kwTokens[k] || (kwTokens[k].length > 3 && tokens[t].indexOf(kwTokens[k]) !== -1)) {
                                score += 1;
                            }
                        }
                    }
                }
            }

            if (score > bestScore) {
                bestScore = score;
                bestMatch = entry;
            }
        }

        return bestScore >= 1 ? bestMatch.response : this.fallbackResponse;
    },

    formatMessage: function (text) {
        // Convert **bold** to <strong>
        text = text.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        // Convert newlines to <br>
        text = text.replace(/\n/g, '<br>');
        return text;
    },

    addMessage: function (text, sender) {
        var container = document.getElementById('chatbotMessages');
        var div = document.createElement('div');
        div.className = 'chat-message ' + sender;
        div.innerHTML = this.formatMessage(text);
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    },

    showTyping: function () {
        var container = document.getElementById('chatbotMessages');
        var typing = document.createElement('div');
        typing.className = 'chat-typing';
        typing.id = 'chatTypingIndicator';
        typing.innerHTML = '<span></span><span></span><span></span>';
        container.appendChild(typing);
        container.scrollTop = container.scrollHeight;
    },

    removeTyping: function () {
        var indicator = document.getElementById('chatTypingIndicator');
        if (indicator) {
            indicator.remove();
        }
    },

    sendMessage: function () {
        var input = document.getElementById('chatbotInput');
        var text = input.value.trim();
        if (!text) return;

        this.addMessage(text, 'user');
        input.value = '';

        var self = this;
        this.showTyping();

        setTimeout(function () {
            self.removeTyping();
            var response = self.findResponse(text);
            self.addMessage(response, 'bot');
        }, 600 + Math.random() * 400);
    },

    init: function () {
        var self = this;
        var toggle = document.getElementById('chatbotToggle');
        var window_ = document.getElementById('chatbotWindow');
        var closeBtn = document.getElementById('chatbotClose');
        var sendBtn = document.getElementById('chatbotSend');
        var input = document.getElementById('chatbotInput');

        if (!toggle || !window_) return;

        // Toggle open/close
        toggle.addEventListener('click', function () {
            var isHidden = window_.classList.contains('hidden');
            window_.classList.toggle('hidden');
            if (isHidden) {
                input.focus();
                // Show welcome message on first open
                var messages = document.getElementById('chatbotMessages');
                if (messages.children.length === 0) {
                    self.addMessage("Welcome to **PropMS Assistant**! I can help you navigate the system and answer your questions. What would you like to know?", 'bot');
                }
            }
        });

        // Close button
        closeBtn.addEventListener('click', function () {
            window_.classList.add('hidden');
        });

        // Send on button click
        sendBtn.addEventListener('click', function () {
            self.sendMessage();
        });

        // Send on Enter key
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                self.sendMessage();
            }
        });

        // Quick action buttons
        var quickActions = document.querySelectorAll('.quick-action');
        for (var i = 0; i < quickActions.length; i++) {
            quickActions[i].addEventListener('click', function () {
                var message = this.getAttribute('data-message');
                if (message) {
                    input.value = message;
                    self.sendMessage();
                }
            });
        }
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function () {
    ChatBot.init();
});
