@php use Illuminate\Support\Facades\DB; @endphp
@extends('modules.administrator.components.layouts.app')
@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2-bootstrap-5.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <style>
  /* Chat container */
#chat-messages {
    padding: 20px;
    background-color: #f7f7f7;
    border-radius: 10px;
    height: 400px;
    overflow-y: auto;
}

/* Message bubble */
.message-bubble {
    padding: 10px 15px;
    max-width: 70%;
    border-radius: 20px;
    margin-bottom: 10px;
    display: inline-block;
    position: relative;
    word-wrap: break-word;
    line-height: 1.4;
}

/* Sent message (right-aligned) */
.sent-message {
    background-color: #007bff;
    color: white;
    border-radius: 20px 20px 0 20px;
    float: right;
    clear: both;
}

/* Received message (left-aligned) */
.received-message {
    background-color: #e5e5ea;
    color: #333;
    border-radius: 20px 20px 20px 0;
    float: left;
    clear: both;
}

/* Message timestamp */
.message-timestamp {
    font-size: 0.8em;
    color: #888;
    position: absolute;
    bottom: -18px;
    right: 10px;
}

</style>
@endpush 
@section('title', 'Live Chat')

@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Live Chats</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Chats</a></li>
                        <li class="breadcrumb-item active">index</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


 


    <div class="d-lg-flex">
        <div class="chat-leftsidebar me-lg-4">
            <div class="">
                <div class="py-4 border-bottom">
                    <div class="d-flex">
                        <div class="flex-shrink-0 align-self-center me-3">
                            <img src="{{ $authUserPhoto }}" class="avatar-xs rounded-circle" alt="">
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="font-size-15 mb-1">{{ $authUserName }}</h5>
                            <p class="text-muted mb-0"><i class="mdi mdi-circle text-success align-middle me-1"></i> Active</p>
                        </div>

                        <div>
                            <div class="dropdown chat-noti-dropdown active">
                                <button class="btn" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-bell bx-tada"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="search-box chat-search-box py-4">
                    <div class="position-relative">
                        <input type="text" class="form-control" placeholder="Search...">
                        <i class="bx bx-search-alt search-icon"></i>
                    </div>
                </div>

                <div class="chat-leftsidebar-nav">
                    <ul class="nav nav-pills nav-justified">
                        <li class="nav-item">
                            <a href="#chat" data-bs-toggle="tab" aria-expanded="true" class="nav-link active">
                                <i class="bx bx-chat font-size-20 d-sm-none"></i>
                                <span class="d-none d-sm-block">Live Chats</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#groups" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                <i class="bx bx-group font-size-20 d-sm-none"></i>
                                <span class="d-none d-sm-block">Closed Chats</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#contacts" data-bs-toggle="tab" aria-expanded="false" class="nav-link">
                                <i class="bx bx-book-content font-size-20 d-sm-none"></i>
                                <span class="d-none d-sm-block">Contacts</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-4">
                        <div class="tab-pane show active" id="chat">
                            <div>
                                <h5 class="font-size-14 mb-3">Recent</h5>
                                {{-- Active Conversations --}}
                                <ul id="chat-conversation-list" class="list-unstyled chat-list" data-simplebar style="max-height: 410px;">
                                    @foreach ($latestMessages as $message)
                                        @php
                                            $otherUserId = $message->sender_id == auth()->id() ? $message->receiver_id : $message->sender_id;
                                        @endphp
                                        <li id="conversation_{{ $message->conversation_id }}">
                                            <a href="javascript:void(0);" onclick="loadConversation({{ $message->conversation_id }})">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 align-self-center me-3">
                                                        <i class="mdi mdi-circle text-success font-size-10"></i>
                                                    </div>
                                                    <div class="flex-shrink-0 align-self-center me-3">
                                                        <img src="{{ $message->base64_photo }}" class="rounded-circle avatar-xs" alt="">
                                                    </div>
                                                    <div class="flex-grow-1 overflow-hidden">
                                                        <h5 class="text-truncate font-size-14 mb-1">
                                                            {{ $message->sender_firstname }} {{ $message->sender_lastname }}
                                                        </h5>
                                                        <p class="text-truncate mb-0">{{ $message->message }}</p>
                                                    </div>
                                                    <div class="font-size-11">
                                                        {{ \Carbon\Carbon::parse($message->sent_at)->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>





                            </div>
                        </div>

                        <div class="tab-pane" id="groups">
                            <h5 class="font-size-14 mb-3">Groups</h5>
                            <ul class="list-unstyled chat-list" data-simplebar style="max-height: 410px;">
                                @foreach ($closedMessages as $message)
        @php
            $otherUserId = $message->sender_id == auth()->id() ? $message->receiver_id : $message->sender_id;
        @endphp
        <li>
            <a href="javascript:void(0);" onclick="loadConversation({{ $message->conversation_id }})">
                <div class="d-flex">
                    <div class="flex-shrink-0 align-self-center me-3">
                        <i class="mdi mdi-circle text-secondary font-size-10"></i>
                    </div>
                    <div class="flex-shrink-0 align-self-center me-3">
                        <img src="{{ $message->base64_photo }}" class="rounded-circle avatar-xs" alt="">
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <h5 class="text-truncate font-size-14 mb-1">
                            {{ $message->sender_firstname }} {{ $message->sender_lastname }}
                        </h5>
                        <p class="text-truncate mb-0">{{ $message->message }}</p>
                    </div>
                    <div class="font-size-11">
                        {{ \Carbon\Carbon::parse($message->sent_at)->diffForHumans() }}
                    </div>
                </div>
            </a>
        </li>
    @endforeach

                            </ul>
                        </div>

                        <div class="tab-pane" id="contacts">
                            <h5 class="font-size-14 mb-3">Contacts</h5>

                            <div  data-simplebar style="max-height: 410px;">
                                <div>
                                    <div class="avatar-xs mb-3">
                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                            A
                                        </span>
                                    </div>

                                    <ul class="list-unstyled chat-list">
                                        <li>
                                            <a href="javascript: void(0);">
                                                <h5 class="font-size-14 mb-0">Adam Miller</h5>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="javascript: void(0);">
                                                <h5 class="font-size-14 mb-0">Alfonso Fisher</h5>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <div class="mt-4">
                                    <div class="avatar-xs mb-3">
                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary">
                                            B
                                        </span>
                                    </div>

                                    <ul class="list-unstyled chat-list">
                                        <li>
                                            <a href="javascript: void(0);">
                                                <h5 class="font-size-14 mb-0">Bonnie Harney</h5>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 user-chat">
            <div class="card">
                <div class="p-4 border-bottom ">
                    <div class="row">
                            <!-- <h5 class="font-size-15 mb-1">Sender Name Here</h5> -->
                            <div id="chat-user-header">
                                <!-- Name and Active Status will go here -->
                            </div>
                    </div>
                </div>
                <div>
                    





                <div id="chat-header" class="row">
                    <div class="col-12 text-center text-muted py-4" id="default-chat-msg">
                        Click any conversation to preview chats.
                    </div>
                </div>

                <div id="chat-content" style="display: none;">
                    <div class="row" id="chat-user-header">
                        <!-- Name and Active Status will go here -->
                    </div>
                    <div id="chat-messages" class="pt-3 px-3" style="height: 400px; overflow-y: auto;">
                        <!-- Loaded messages go here -->
                    </div>
                </div>




                    <div class="chat-conversation p-3">
                        <ul class="list-unstyled mb-0" id="chatMessages" data-simplebar style="max-height: 486px;"></ul>
                    </div>
                    <div class="p-3 chat-input-section">
                        <div class="row">
                            <div class="col">
                                <div class="position-relative">
                                    <input type="text" id="chat-message" class="form-control chat-input" placeholder="Enter Message...">
                                </div>
                            </div>
                            <div class="col-auto">
                                <button onclick="sendMessage()" type="submit" class="btn btn-primary btn-rounded chat-send w-md waves-effect waves-light"><span class="d-none d-sm-inline-block me-2">Send</span> <i class="mdi mdi-send"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                        <!-- end row -->


    <!-- end row -->
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
    






    <script>
    let lastMessageId = {{ $latestMessages->max('id') ?? 0 }};
    let currentConversationId = null;

    // Notification sound for new messages
    const notificationSound = new Audio('/assets/sounds/message.mp3');

    // Set the current conversation ID when clicked
    function setCurrentConversationId(id) {
        currentConversationId = id;
    }

    // Render the new or updated conversation in the list
    function renderNewConversationItem(message) {
        const list = document.getElementById("chat-conversation-list");
        const existing = document.getElementById("conversation_" + message.conversation_id);

        const html = `
            <a href="javascript:void(0);" onclick="loadConversation(${message.conversation_id})">
                <div class="d-flex">
                    <div class="flex-shrink-0 align-self-center me-3">
                        <i class="mdi mdi-circle text-success font-size-10"></i>
                    </div>
                    <div class="flex-shrink-0 align-self-center me-3">
                        <img src="${message.base64_photo || '/default-avatar.png'}" class="rounded-circle avatar-xs" alt="">
                    </div>
                    <div class="flex-grow-1 overflow-hidden">
                        <h5 class="text-truncate font-size-14 mb-1">
                            ${message.sender_firstname || 'Unknown'} ${message.sender_lastname || ''}
                        </h5>
                        <p class="text-truncate mb-0">${message.message}</p>  <!-- Fix: Ensure the message goes here -->
                    </div>
                    <div class="font-size-11">
                        just now
                    </div>
                </div>
            </a>
        `;

        if (existing) {
            // Only update the message content without modifying the position of the conversation
            existing.querySelector('.text-truncate.mb-0').textContent = message.message; // Corrected to target <p>
            existing.querySelector('.font-size-11').textContent = 'just now'; // Update the timestamp if necessary
        } else {
            // If the conversation is new, create a new list item and add it to the top
            const li = document.createElement("li");
            li.id = "conversation_" + message.conversation_id;
            li.innerHTML = html;
            list.prepend(li);  // Add to the top if it's a new conversation
        }
    }

    // Render the message into the currently opened conversation
    function renderMessageToOpenConversation(message) {
        if (parseInt(currentConversationId) !== parseInt(message.conversation_id)) return;

        const container = document.getElementById("chat-messages");
        if (!container) return;

        // Check if the message already exists in the chat to avoid duplication
        if (document.getElementById(`message_${message.id}`)) return;

        const div = document.createElement("div");
        div.id = `message_${message.id}`;
        const isOwnMessage = message.sender_id == {{ auth()->id() }};
        div.className = isOwnMessage ? "chat-message right" : "chat-message left";
        div.innerHTML = `
            <div class="message ${isOwnMessage ? 'own' : ''}">
                <div class="message-content">${message.message}</div>
                <div class="message-time">just now</div>
            </div>
        `;
        container.appendChild(div);
        container.scrollTop = container.scrollHeight; // Auto scroll to bottom
    }

    // Fetch new messages every 3 seconds
    function fetchNewMessages() {
        fetch(`/api/chat/updates?last_message_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && Array.isArray(data.latestConversations)) {
                    let hasNewMessage = false;

                    // Loop through the fetched messages
                    data.latestConversations.forEach(message => {
                        renderNewConversationItem(message);  // Update or add the conversation
                        renderMessageToOpenConversation(message);  // Add message to the open chat

                        if (message.id > lastMessageId) {
                            lastMessageId = message.id;
                            hasNewMessage = true;
                        }
                    });

                    // If there are new messages, play the notification sound
                    if (hasNewMessage) {
                        notificationSound.pause();
                        notificationSound.currentTime = 0;
                        notificationSound.play().catch(e => {
                            console.warn('Autoplay blocked or error:', e);  // Handle errors like autoplay restrictions
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Failed to fetch messages:', error);
            });
    }

    // Poll every 3 seconds to check for new messages
    setInterval(fetchNewMessages, 3000);
</script>


<script>
// Function to load conversation messages
function loadConversation(conversationId) {
    fetch(`/api/chat/conversations/${conversationId}`)
        .then(res => res.json())
        .then(data => {
            const messages = data.messages;
            const chatMessagesContainer = document.getElementById('chat-messages');
            chatMessagesContainer.innerHTML = '';

            messages.forEach(msg => {
                const isSender = msg.sender_id === userId; // Assuming userId is the ID of the logged-in user
                const messageDiv = document.createElement('div');
                messageDiv.classList.add('message-bubble');
                messageDiv.classList.add(isSender ? 'sent-message' : 'received-message');
                messageDiv.innerHTML = `
                    <p>${msg.message}</p>
                    <span class="message-timestamp">${new Date(msg.sent_at).toLocaleString()}</span>
                `;
                chatMessagesContainer.appendChild(messageDiv);
            });

            chatMessagesContainer.scrollTop = chatMessagesContainer.scrollHeight; // Scroll to the latest message
        })
        .catch(err => console.error('Failed to load conversation', err));
}

// Function to send a message
function sendMessage() {
    const messageInput = document.getElementById('chat-message');
    const messageText = messageInput.value;

    if (messageText.trim() !== '') {
        const conversationId = getConversationId(); // Replace with logic to get the current conversation ID

        // Send message to the server via API
        fetch(`/api/chat/conversations/${conversationId}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                message: messageText,
                userId: userId, // Logged-in user ID
            }),
        })
        .then(res => res.json())
        .then(data => {
            // After sending, load updated conversation
            loadConversation(conversationId);

            // Clear the input field
            messageInput.value = '';
        })
        .catch(err => console.error('Failed to send message', err));
    }
}


</script>


@endpush