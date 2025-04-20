@extends('layouts.backend')

@section('content')
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">編輯幣別</h3>
            </div>
            <div class="card-body">
                <form id="editForm" action="javascript:void(0);">
                    <div class="mb-3">
                        <label for="code" class="form-label">幣別代碼</label>
                        <input type="text" class="form-control" id="code" name="code"
                            value="{{ old('code', $currency->code) }}" required maxlength="3">
                        <div class="form-text">請輸入3個字母的幣別代碼</div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">幣別名稱</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $currency->name) }}" required>
                    </div>

                    @if (count($otherCurrencies) > 0)
                        <!-- 匯率設定部分 -->
                        <div class="card mt-4 mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">匯率設定</h4>
                                <p class="text-muted mb-0 small">管理 {{ $currency->code }} 對其他幣別的匯率關係</p>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">
                                    請輸入從 {{ $currency->code }} 轉換至其他幣別的匯率值。<br>
                                    例如：如果1 {{ $currency->code }} = 31.5 TWD，則輸入31.5
                                </p>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>目標幣別</th>
                                                <th>匯率值</th>
                                                <th>上次更新時間</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($otherCurrencies as $index => $targetCurrency)
                                                <tr>
                                                    <td>
                                                        {{ $targetCurrency->code }} ({{ $targetCurrency->name }})
                                                        <input type="hidden"
                                                            name="rates[{{ $index }}][target_currency]"
                                                            data-index="{{ $index }}" data-field="target_currency"
                                                            value="{{ $targetCurrency->code }}">
                                                    </td>
                                                    <td>
                                                        <div class="input-group">
                                                            <span class="input-group-text">1 {{ $currency->code }} =</span>
                                                            <input type="number" class="form-control"
                                                                id="rates[{{ $index }}][rate]"
                                                                name="rates[{{ $index }}][rate]"
                                                                data-index="{{ $index }}" data-field="rate"
                                                                step="0.000001" min="0.000001" placeholder="請輸入匯率值"
                                                                value="{{ old('rates.' . $index . '.rate', isset($existingRates[$targetCurrency->code]) ? $existingRates[$targetCurrency->code]->rate : '') }}">
                                                            <span
                                                                class="input-group-text">{{ $targetCurrency->code }}</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        @if (isset($existingRates[$targetCurrency->code]))
                                                            {{ \Carbon\Carbon::parse($existingRates[$targetCurrency->code]->last_updated)->format('Y-m-d H:i') }}
                                                        @else
                                                            <span class="text-muted">尚未設定</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="form-text">註：{{ $currency->code }} 對自身的匯率固定為 1.0</div>
                            </div>
                        </div>
                    @endif

                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary me-2">更新幣別</button>
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
            $('#editForm').submit(function(e) {
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

                console.log('數據傳送：', formData);
                // 收集匯率數據
                // 會選取所有 name^= 以 "rates[" 開頭的 input 欄位
                $('input[name^="rates["]').each(function() {
                    const index = $(this).data('index');
                    const field = $(this).data('field');

                    if (!formData.rates[index]) {
                        formData.rates[index] = {};
                    }


                    formData.rates[index][field] = $(this).val();
                });

                const currencyId = {{ $currency->id }};
                $.ajax({
                    url: `/api/currencies/${currencyId}`,
                    method: 'PUT',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log('API 回傳：', response);
                        window.location.href =
                            "{{ route('backend.index') }}?success=幣別及匯率更新成功！";
                    },
                    error: function(xhr) {
                        // 確保 errors 有資訊或沒資訊都將設為物件
                        let errors = xhr.responseJSON?.errors || {};

                        console.log('API 錯誤回傳：', xhr.responseJSON);

                        if (xhr.responseJSON?.error) {
                            $('<div class="alert alert-danger mt-3">' + xhr.responseJSON.error +
                                '</div>').insertBefore('#editForm');
                            return;
                        } else if (Object.keys(errors).length === 0) {
                            // 沒有明確錯誤訊息時顯示預設錯誤
                            $('<div class="alert alert-danger mt-3">提交失敗，請檢查後再試</div>')
                                .insertBefore('#editForm');
                            return;
                        } else {
                            // 顯示錯誤訊息
                            Object.keys(errors).forEach(function(key) {
                                let errorMsg = errors[key][0];

                                // 判斷是否以 "rates." 開頭
                                if (key.startsWith('rates.')) {
                                    // 把錯誤欄位名稱用「.」分割成陣列
                                    const parts = key.split('.');
                                    const index = parts[1];
                                    const field = parts[2];
                                    const inputName = `rates[${index}][${field}]`;
                                    const input = $(`[name="${inputName}"]`);
                                    input.addClass('is-invalid');
                                    input.closest('.input-group').after(
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
                    }
                });
            });
        });
    </script>
@endsection
