@extends('layouts.admin')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-0"><i class="fa-solid fa-headset text-primary me-2"></i>Trung Tâm Hỗ Trợ Trực Tuyến (LiveChat Admin)</h3>
        <p class="text-muted small mb-0">Trò chuyện 2 chiều theo thời gian thực với Khách hàng & Khách vãng lai trên website.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold">
            <i class="fa-solid fa-gauge-high me-1"></i> Bảng Điều Khiển Admin
        </a>
        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#selectCustomerModal">
            <i class="fa-solid fa-user-plus me-1"></i> Chọn Khách Hàng Để Nhắn Tin
        </button>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fw-bold">
            <i class="fa-solid fa-circle text-success me-1" style="font-size: 8px;"></i> CSKH Trực Tuyến
        </span>
    </div>
</div>

<div class="row g-3" style="min-height: 580px;">
    <!-- COL 1: DANH SÁCH CUỘC HỘI THOẠI KHÁCH HÀNG & KHÁCH VÃNG LAI -->
    <div class="col-lg-4">
        <div class="bg-white rounded-4 border shadow-sm h-100 d-flex flex-column overflow-hidden">
            <div class="p-3 bg-light border-bottom">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <strong class="text-dark small text-uppercase"><i class="fa-solid fa-comments text-primary me-2"></i>Danh Sách Hội Thoại</strong>
                    <span id="conversationCountBadge" class="badge bg-primary rounded-pill">0</span>
                </div>
                <!-- Ô tìm kiếm cuộc hội thoại -->
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="conversationSearchInput" class="form-control border-start-0" placeholder="Lọc hội thoại..." autocomplete="off">
                </div>
            </div>

            <!-- List Conversations Container -->
            <div id="conversationList" class="overflow-y-auto flex-grow-1 p-2" style="max-height: 500px;">
                <div class="text-center py-5 text-muted small">
                    <i class="fa-solid fa-spinner fa-spin fa-2x mb-2 text-primary"></i><br>
                    Đang đồng bộ cuộc hội thoại...
                </div>
            </div>
        </div>
    </div>

    <!-- COL 2: KHUNG CHÁT TRAO ĐỔI TRỰC TIẾP -->
    <div class="col-lg-8">
        <div class="bg-white rounded-4 border shadow-sm h-100 d-flex flex-column overflow-hidden">
            <!-- Header Chat Window -->
            <div id="chatHeader" class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div id="activeAvatar" class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <strong id="activeUserName" class="d-block text-dark leading-tight" style="font-size: 15px;">Đang tải cuộc hội thoại...</strong>
                        <small id="activeUserEmail" class="text-muted" style="font-size: 11px;">Hệ thống đang tự động chọn khách hàng mới nhất</small>
                    </div>
                </div>
            </div>

            <!-- Body Messages Container -->
            <div id="chatMessagesBox" class="p-3 overflow-y-auto flex-grow-1 d-flex flex-column gap-2" style="max-height: 440px; background-color: #f8fafc;">
                <div class="text-center my-auto py-5 text-muted small">
                    <i class="fa-solid fa-comments fa-3x mb-3 text-secondary opacity-50"></i><br>
                    Đang đồng bộ dữ liệu tin nhắn...
                </div>
            </div>

            <!-- Footer Input Form -->
            <div class="p-3 bg-white border-top">
                <form id="adminSendForm" class="d-flex align-items-center gap-2">
                    <input type="text" id="adminMessageInput" class="form-control rounded-pill px-3" placeholder="Nhập câu trả lời tới khách hàng..." autocomplete="off" disabled>
                    <button type="submit" id="adminSendBtn" class="btn btn-primary rounded-pill px-4 fw-bold flex-shrink-0" disabled>
                        <i class="fa-solid fa-paper-plane me-1"></i> Gửi Tin Nhắn
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CHỌN TÀI KHOẢN KHÁCH HÀNG ĐỂ NHẮN TIN -->
<div class="modal fade" id="selectCustomerModal" tabindex="-1" aria-labelledby="selectCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-dark text-white border-0">
                <h6 class="modal-title fw-bold" id="selectCustomerModalLabel"><i class="fa-solid fa-user-plus text-warning me-2"></i>Chọn Tài Khoản Khách Hàng Để Nhắn Tin</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted small mb-3">Tìm kiếm khách hàng trong hệ thống để mở cuộc trò chuyện trực tiếp:</p>
                <div class="input-group mb-3">
                    <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" id="modalSearchCustomerInput" class="form-control" placeholder="Nhập tên hoặc email khách hàng..." autocomplete="off">
                </div>

                <div id="modalCustomerList" class="overflow-y-auto" style="max-height: 280px;">
                    <div class="text-center py-4 text-muted small">Đang tải danh sách khách hàng...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let activeChatKey = null;
    let lastMessageId = 0;
    let isAutoSelected = false;

    const conversationListEl = document.getElementById('conversationList');
    const conversationCountBadge = document.getElementById('conversationCountBadge');
    const activeUserNameEl = document.getElementById('activeUserName');
    const activeUserEmailEl = document.getElementById('activeUserEmail');
    const chatMessagesBox = document.getElementById('chatMessagesBox');
    const adminSendForm = document.getElementById('adminSendForm');
    const adminMessageInput = document.getElementById('adminMessageInput');
    const adminSendBtn = document.getElementById('adminSendBtn');

    // 1. TẢI DANH SÁCH HỘI THOẠI (POLLING 2.5s)
    function loadConversations() {
        fetch("{{ route('admin.livechat.conversations') }}")
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                const conversations = data.conversations;
                conversationCountBadge.innerText = conversations.length;

                if (conversations.length === 0) {
                    conversationListEl.innerHTML = '<div class="text-center py-5 text-muted small">Chưa có cuộc hội thoại nào. Khi khách hàng bấm nhắn tin, tin nhắn sẽ xuất hiện tại đây ngay lập tức!</div>';
                    activeUserNameEl.innerText = 'Chưa có cuộc hội thoại nào';
                    activeUserEmailEl.innerText = 'Chờ tin nhắn mới từ khách hàng';
                    chatMessagesBox.innerHTML = '<div class="text-center my-auto py-5 text-muted small">Chưa có tin nhắn nào.</div>';
                    return;
                }

                // Tự động chọn cuộc hội thoại đầu tiên nếu chưa chọn
                if (!activeChatKey && conversations.length > 0) {
                    const first = conversations[0];
                    selectConversation(first.chat_key, first.user_name, first.user_email);
                }

                let html = '';
                conversations.forEach(c => {
                    const isActive = c.chat_key === activeChatKey;
                    const activeClass = isActive ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark border';
                    const nameColor = isActive ? 'text-white' : 'text-dark';
                    const msgColor = isActive ? 'text-white-50' : 'text-muted';

                    html += `
                        <div class="p-3 rounded-4 mb-2 cursor-pointer transition-smooth ${activeClass} conversation-item" data-chat-key="${c.chat_key}" data-name="${c.user_name}" data-email="${c.user_email}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <strong class="${nameColor} small">${c.user_name}</strong>
                                <small class="${msgColor}" style="font-size: 10px;">${c.latest_time}</small>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="${msgColor} small text-truncate" style="max-width: 210px;">
                                    ${c.latest_message}
                                </div>
                                ${c.unread_count > 0 ? `<span class="badge bg-danger rounded-pill">${c.unread_count}</span>` : ''}
                            </div>
                        </div>
                    `;
                });

                conversationListEl.innerHTML = html;

                // Thêm event click item
                document.querySelectorAll('.conversation-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const key = this.getAttribute('data-chat-key');
                        const name = this.getAttribute('data-name');
                        const email = this.getAttribute('data-email');
                        selectConversation(key, name, email);
                    });
                });
            })
            .catch(err => console.error('Lỗi tải cuộc hội thoại:', err));
    }

    // 2. CHỌN HỘI THOẠI
    function selectConversation(chatKey, userName, userEmail) {
        if (activeChatKey === chatKey && isAutoSelected) {
            return;
        }

        activeChatKey = chatKey;
        isAutoSelected = true;
        lastMessageId = 0;

        activeUserNameEl.innerText = userName;
        activeUserEmailEl.innerText = userEmail;

        adminMessageInput.disabled = false;
        adminSendBtn.disabled = false;

        chatMessagesBox.innerHTML = '<div class="text-center py-5 text-muted small"><i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải tin nhắn...</div>';

        loadMessages(true);
    }

    // 3. TẢI TIN NHẮN TỪ CHI TIẾT (POLLING 2.5s)
    function loadMessages(isInitial = false) {
        if (!activeChatKey) return;

        const url = `{{ url('admin/livechat/messages') }}/${activeChatKey}?after_id=${lastMessageId}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                if (isInitial) {
                    chatMessagesBox.innerHTML = '';
                    lastMessageId = 0;
                    if (data.chat_info) {
                        activeUserNameEl.innerText = data.chat_info.name;
                        activeUserEmailEl.innerText = data.chat_info.email;
                    }
                }

                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        appendMessageToBox(msg);
                        if (msg.id > lastMessageId) {
                            lastMessageId = msg.id;
                        }
                    });
                    scrollChatToBottom();
                }
            })
            .catch(err => console.error('Lỗi tải tin nhắn:', err));
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function appendMessageToBox(msg) {
        const isAdmin = msg.sender_type === 'admin';
        const msgDiv = document.createElement('div');
        msgDiv.className = `d-flex flex-column ${isAdmin ? 'align-items-end' : 'align-items-start'} mb-3 w-100`;

        const senderBadge = isAdmin 
            ? '<span class="badge bg-primary px-2 py-1 mb-1 shadow-sm"><i class="fa-solid fa-user-shield me-1"></i>Bạn (Admin)</span>' 
            : '<span class="badge bg-secondary px-2 py-1 mb-1 shadow-sm"><i class="fa-solid fa-user me-1"></i>Khách hàng</span>';

        const bubbleClass = isAdmin 
            ? 'bg-primary text-white shadow-sm' 
            : 'bg-white text-dark border shadow-sm';

        msgDiv.innerHTML = `
            <div class="d-flex flex-column ${isAdmin ? 'align-items-end' : 'align-items-start'}" style="max-width: 80%;">
                <div class="d-flex align-items-center mb-1">
                    ${senderBadge}
                </div>
                <div class="px-3 py-2 rounded-3 ${bubbleClass} small" style="word-break: break-word; font-size: 13.5px; line-height: 1.5;">
                    ${escapeHtml(msg.message)}
                </div>
                <small class="text-muted mt-1 px-1" style="font-size: 10px;">${msg.time}</small>
            </div>
        `;

        chatMessagesBox.appendChild(msgDiv);
    }

    function scrollChatToBottom() {
        chatMessagesBox.scrollTop = chatMessagesBox.scrollHeight;
    }

    // 4. ADMIN GỬI TIN NHẮN TRẢ LỜI
    adminSendForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const text = adminMessageInput.value.trim();
        if (!text || !activeChatKey) return;

        adminMessageInput.value = '';
        const currentCsrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch(`{{ url('admin/livechat/send') }}/${activeChatKey}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': currentCsrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                appendMessageToBox(data.data);
                if (data.data.id > lastMessageId) {
                    lastMessageId = data.data.id;
                }
                scrollChatToBottom();
                loadConversations();
            }
        })
        .catch(err => console.error('Lỗi gửi tin nhắn:', err));
    });

    // 5. TÌM KIẾM CẢNH BÁO / LỌC DANH SÁCH CUỘC HỘI THOẠI REALTIME
    const conversationSearchInput = document.getElementById('conversationSearchInput');
    if (conversationSearchInput) {
        conversationSearchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.conversation-item').forEach(item => {
                const name = item.getAttribute('data-name')?.toLowerCase() || '';
                const email = item.getAttribute('data-email')?.toLowerCase() || '';
                if (name.includes(query) || email.includes(query)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // 6. MODAL TÌM KIẾM TÀI KHOẢN KHÁCH HÀNG ĐỂ CHỦ ĐỘNG NHẮN TIN
    const modalSearchCustomerInput = document.getElementById('modalSearchCustomerInput');
    const modalCustomerList = document.getElementById('modalCustomerList');
    const selectCustomerModalEl = document.getElementById('selectCustomerModal');

    function searchCustomersInModal(query = '') {
        fetch(`{{ route('admin.livechat.searchUsers') }}?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;
                const users = data.users;
                if (users.length === 0) {
                    modalCustomerList.innerHTML = '<div class="text-center py-4 text-muted small">Không tìm thấy khách hàng nào.</div>';
                    return;
                }

                let html = '';
                users.forEach(u => {
                    html += `
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-3 mb-2 bg-light border border-hover customer-modal-item cursor-pointer" data-id="${u.id}" data-name="${escapeHtml(u.name)}" data-email="${escapeHtml(u.email)}">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                    ${escapeHtml(u.name.substring(0, 1).toUpperCase())}
                                </div>
                                <div>
                                    <strong class="d-block text-dark small">${escapeHtml(u.name)}</strong>
                                    <small class="text-muted" style="font-size: 11px;">${escapeHtml(u.email)}</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                <i class="fa-solid fa-paper-plane me-1"></i> Nhắn Tin
                            </button>
                        </div>
                    `;
                });
                modalCustomerList.innerHTML = html;

                document.querySelectorAll('.customer-modal-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const id = this.getAttribute('data-id');
                        const name = this.getAttribute('data-name');
                        const email = this.getAttribute('data-email');
                        
                        selectConversation('user_' + id, name, email);

                        // Đóng modal bootstrap
                        const modalInstance = bootstrap.Modal.getInstance(selectCustomerModalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                    });
                });
            })
            .catch(err => console.error('Lỗi tìm kiếm khách hàng:', err));
    }

    if (selectCustomerModalEl) {
        selectCustomerModalEl.addEventListener('shown.bs.modal', function () {
            searchCustomersInModal('');
        });
    }

    if (modalSearchCustomerInput) {
        modalSearchCustomerInput.addEventListener('input', function () {
            searchCustomersInModal(this.value);
        });
    }

    // Khoi chay Polling tự động liên tục
    loadConversations();
    setInterval(function () {
        loadConversations();
        if (activeChatKey) {
            loadMessages(false);
        }
    }, 2500);
});
</script>
@endsection
