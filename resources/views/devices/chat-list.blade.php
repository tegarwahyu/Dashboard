<div class="container mt-4">
    <div class="row">
        <!-- Sisi kiri (List Chat) -->
        <div class="col-md-4" style="max-width: 600px;">
            <h4 class="text-dark mb-4">Daftar Chat</h4>

            <div class="list-group list-chat">
                @php
                    // Urutkan berdasarkan timestamp terbaru
                    usort($chatData, function($a, $b) {
                        return $b['conversationTimestamp'] - $a['conversationTimestamp'];
                    });
                @endphp

                @foreach($chatData as $chat)
                <div class="list-group-item p-3 mb-2 chat-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <!-- Nama chat / nomor WhatsApp -->
                            <h6 class="chat-id mb-1">
                                {{ $chat['id'] }}
                            </h6>

                            <!-- Pesan terbaru -->
                            <p class="chat-message text-muted mb-1">
                                {{ $chat['messages'][0]['message']['message']['conversation'] ?? 'Tidak ada pesan terbaru' }}
                            </p>
                        </div>

                        <div class="text-right">
                            <!-- Waktu pesan terakhir -->
                            <small class="text-muted chat-time">{{ date('H:i', $chat['conversationTimestamp']) }}</small>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Sisi kanan (ruang kosong untuk nanti) -->
        <div class="col-md-8">
            <!-- Anda bisa mengisi ini nanti dengan komponen lain -->
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
    body {
        background-color: #f7f7f7;
    }

    .list-chat {
        background-color: #ffffff;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .chat-item {
        background-color: #e9ecef;
        border: none;
        border-radius: 8px;
        color: #333;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        transition: background-color 0.2s;
    }

    .chat-id {
        color: #343a40;
        font-weight: 500;
    }

    .chat-message {
        font-size: 14px;
        color: #6c757d;
    }

    .chat-time {
        font-size: 12px;
    }

    .unread-count {
        font-size: 12px;
        margin-left: 10px;
        padding: 5px;
    }

    /* Hover effect */
    .list-group-item:hover {
        background-color: #dee2e6;
    }

    h4 {
        font-weight: bold;
    }

    .badge {
        background-color: #007bff;
    }
</style>
