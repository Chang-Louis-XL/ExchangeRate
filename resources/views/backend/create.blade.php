@extends('layouts.backend')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">新增幣別</h3>
            </div>
            <div class="card-body">
                {{-- javascript:void(0); 是一個「什麼都不做」的 JavaScript 表達式。 --}}
                <form id="createForm" action="javascript:void(0);">
                    <div class="mb-3">
                        <label for="code" class="form-label">幣別代碼</label>
                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}"
                            required maxlength="3" placeholder="例如：TWD">
                        <div class="form-text">請輸入3個字母的幣別代碼</div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">幣別名稱</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                            required placeholder="例如：新台幣">
                    </div>

                    @if (count($currencies) > 0)
                        <!-- 匯率設定部分 -->
                        <div class="card mt-4 mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">匯率設定</h4>
                                <p class="text-muted mb-0 small">設定新幣別與其他幣別的匯率關係</p>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    請輸入從新幣別轉換至其他幣別的匯率值。<br>
                                    例如：如果1 USD = 31.5 TWD，則輸入31.5
                                </p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>目標幣別</th>
                                                <th>匯率值</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($currencies as $index => $targetCurrency)
                                                <tr>
                                                    <td>
                                                        {{ $targetCurrency->code }} ({{ $targetCurrency->name }})
                                                        <input type="hidden"
                                                            name="rates[{{ $index }}][target_currency]"
                                                            value="{{ $targetCurrency->code }}">
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">1 新幣別 =</span>
                                                            <input type="number" class="form-control"
                                                                name="rates[{{ $index }}][rate]" step="0.000001"
                                                                min="0.000001" placeholder="請輸入匯率值"
                                                                value="{{ old('rates.' . $index . '.rate') }}">
                                                            <span
                                                                class="input-group-text">{{ $targetCurrency->code }}</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-text">註：新幣別對自身的匯率將自動設為 1.0</div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary me-2">新增幣別</button>
                        <a href="{{ route('backend.index') }}" class="btn btn-secondary">返回列表</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#createForm').submit(function(e) {
                // 阻止表單的預設送出行為（例如頁面重新整理或跳轉）
                e.preventDefault();

                // 清除之前的錯誤訊息
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                // 收集表單數據
                let formData = {
                    code: $('#code').val(),
                    name: $('#name').val(),
                    rates: []
                };

                // 收集匯率數據
                $('input[name^="rates["]').each(function() {
                    const match = $(this).attr('name').match(/rates\[(\d+)\]\[([^\]]+)\]/);
                    if (match) {
                        const index = parseInt(match[1]);
                        const field = match[2];

                        if (!formData.rates[index]) {
                            formData.rates[index] = {};
                        }

                        formData.rates[index][field] = $(this).val();
                    }
                });

                $.ajax({
                    url: '/api/currencies',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        window.location.href =
                            "{{ route('backend.index') }}?success=幣別及匯率新增成功！";
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors || {};

                        if (xhr.responseJSON?.error) {
                            $('<div class="alert alert-danger mt-3">' + xhr.responseJSON.error +
                                '</div>').insertBefore('#createForm');
                            return;
                        } else if (Object.keys(errors).length === 0) {
                            // 沒有明確錯誤訊息時顯示預設錯誤
                            $('<div class="alert alert-danger mt-3">提交失敗，請檢查後再試</div>')
                                .insertBefore('#createForm');
                            return;
                        }

                        // 顯示錯誤訊息
                        Object.keys(errors).forEach(function(key) {
                            const errorMsg = errors[key][0];

                            if (key.startsWith('rates.')) {
                                const parts = key.split('.');
                                const index = parts[1];
                                const field = parts[2];
                                const inputName = `rates[${index}][${field}]`;
                                const input = $(`[name="${inputName}"]`);
                                input.addClass('is-invalid');
                                input.after(
                                    `<div class="invalid-feedback d-block">${errorMsg}</div>`
                                    );
                            } else {
                                $(`#${key}`).addClass('is-invalid');
                                $(`#${key}`).after(
                                    `<div class="invalid-feedback">${errorMsg}</div>`
                                    );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
