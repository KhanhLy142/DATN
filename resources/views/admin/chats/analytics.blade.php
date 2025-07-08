@extends('admin.layouts.master')

@section('title', 'Phân tích dữ liệu Chat')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-primary">Phân tích dữ liệu Chat cho Training AI</h2>
            <div>
                <a href="{{ route('admin.chats.export-training-data') }}" class="btn btn-success me-2">
                    <i class="fas fa-download"></i> Xuất dữ liệu Training
                </a>
                <a href="{{ route('admin.chats.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalChats) }}</h3>
                        <p class="mb-0">Tổng Chat Sessions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ number_format($totalMessages) }}</h3>
                        <p class="mb-0">Tổng tin nhắn</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h3>{{ $totalChats > 0 ? round($totalMessages / $totalChats, 1) : 0 }}</h3>
                        <p class="mb-0">TB tin nhắn/chat</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-tags"></i> Phân loại chủ đề Chat</h5>
                    </div>
                    <div class="card-body">
                        @if($chatsByTopic->count() > 0)
                            @foreach($chatsByTopic as $topic => $count)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary">{{ ucfirst($topic) }}</span>
                                    <div class="flex-grow-1 mx-3">
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ ($count / $totalChats) * 100 }}%"></div>
                                        </div>
                                    </div>
                                    <span><strong>{{ $count }}</strong> chat</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Chưa có dữ liệu</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-key"></i> Từ khóa phổ biến</h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @if(count($topKeywords) > 0)
                            <div class="row">
                                @foreach($topKeywords as $word => $count)
                                    @if(strlen($word) > 2 && $count > 2) {{-- Lọc từ ngắn và ít xuất hiện --}}
                                    <div class="col-md-6 mb-1">
                                        <span class="badge bg-light text-dark me-1">{{ $word }}</span>
                                        <small class="text-muted">({{ $count }})</small>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Chưa có dữ liệu</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-question-circle"></i> Câu hỏi được lặp lại nhiều lần</h5>
                <small class="text-muted">Những câu hỏi này nên được training kỹ cho AI</small>
            </div>
            <div class="card-body">
                @if($popularQuestions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Câu hỏi</th>
                                <th width="100">Số lần</th>
                                <th width="120">Mức độ ưu tiên</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($popularQuestions as $question)
                                <tr>
                                    <td>{{ $question->message }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $question->count }}</span>
                                    </td>
                                    <td>
                                        @if($question->count >= 10)
                                            <span class="badge bg-danger">Cao</span>
                                        @elseif($question->count >= 5)
                                            <span class="badge bg-warning">Trung bình</span>
                                        @else
                                            <span class="badge bg-secondary">Thấp</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Chưa có câu hỏi lặp lại</p>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Chat kéo dài - Cần cải thiện AI response</h5>
                <small class="text-muted">Những chat này cho thấy AI chưa trả lời hiệu quả</small>
            </div>
            <div class="card-body">
                @if($slowResponseChats->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Chat ID</th>
                                <th>Khách hàng</th>
                                <th>Thời gian</th>
                                <th>Số tin nhắn</th>
                                <th>Câu hỏi đầu</th>
                                <th>Hành động</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($slowResponseChats as $chat)
                                <tr>
                                    <td><strong>#{{ $chat->id }}</strong></td>
                                    <td>{{ $chat->customer->name ?? 'Khách #' . $chat->customer_id }}</td>
                                    <td>
                                        <span class="badge bg-warning">{{ $chat->getChatDurationInMinutes() }} phút</span>
                                    </td>
                                    <td>{{ $chat->getMessageCount() }}</td>
                                    <td style="max-width: 200px;">
                                        @if($chat->firstCustomerMessage)
                                            {{ Str::limit($chat->firstCustomerMessage->message, 50) }}
                                        @else
                                            <em>Không có</em>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.chats.show', $chat->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Không có chat kéo dài bất thường</p>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line"></i> Xu hướng Chat theo ngày</h5>
            </div>
            <div class="card-body">
                @if($chatsByDate->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Số chat</th>
                                <th>Biểu đồ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $maxCount = $chatsByDate->max('count'); @endphp
                            @foreach($chatsByDate as $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($data->date)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-info">{{ $data->count }}</span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ ($data->count / $maxCount) * 100 }}%">
                                                {{ $data->count }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Chưa có dữ liệu theo thời gian</p>
                @endif
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5><i class="fas fa-info-circle"></i> Hướng dẫn sử dụng dữ liệu để Training AI</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>📊 Dữ liệu ưu tiên:</h6>
                        <ul class="list-unstyled">
                            <li>✅ Câu hỏi lặp lại nhiều lần (training chính xác)</li>
                            <li>✅ Chat kéo dài (cải thiện response)</li>
                            <li>✅ Từ khóa phổ biến (mở rộng vocabulary)</li>
                            <li>✅ Chủ đề chính (phân loại tốt hơn)</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>🎯 Cách sử dụng:</h6>
                        <ul class="list-unstyled">
                            <li>1️⃣ Xuất dữ liệu định dạng JSON</li>
                            <li>2️⃣ Làm sạch và gán nhãn</li>
                            <li>3️⃣ Tạo training dataset</li>
                            <li>4️⃣ Fine-tune AI model</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
