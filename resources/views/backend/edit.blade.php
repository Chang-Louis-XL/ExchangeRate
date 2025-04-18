@extends('layouts.backend')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">編輯幣別</h3>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <form id="editForm" action="javascript:void(0);">
                @csrf
                
                <div class="mb-3">
                    <label for="code" class="form-label">幣別代碼</label>
                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $currency->code) }}" required maxlength="3">
                    <div class="form-text">請輸入3個字母的幣別代碼</div>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="name" class="form-label">幣別名稱</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $currency->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                @if(count($otherCurrencies) > 0)
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
                                    @foreach($otherCurrencies as $index => $targetCurrency)
                                        <tr>
                                            <td>
                                                {{ $targetCurrency->code }} ({{ $targetCurrency->name }})
                                                <input type="hidden" name="rates[{{ $index }}][target_currency]" value="{{ $targetCurrency->code }}">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-text">1 {{ $currency->code }} =</span>
                                                    <input type="number" class="form-control @error('rates.'.$index.'.rate') is-invalid @enderror" 
                                                        name="rates[{{ $index }}][rate]" 
                                                        step="0.000001" min="0.000001" 
                                                        placeholder="請輸入匯率值"
                                                        value="{{ old('rates.'.$index.'.rate', isset($existingRates[$targetCurrency->code]) ? $existingRates[$targetCurrency->code]->rate : '') }}">
                                                    <span class="input-group-text">{{ $targetCurrency->code }}</span>
                                                </div>
                                                @error('rates.'.$index.'.rate')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                @if(isset($existingRates[$targetCurrency->code]))
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
        
        const currencyId = {{ $currency->id }};
        
        $.ajax({
            url: `/api/currencies/${currencyId}`,
            method: 'PUT',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // console.log('API 回傳：', response);
                window.location.href = "{{ route('backend.index') }}?success=幣別及匯率更新成功！";
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors || {};
                
                if (xhr.responseJSON?.error) {
                    $('<div class="alert alert-danger mt-3">' + xhr.responseJSON.error + '</div>').insertBefore('#editForm');
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
                        input.after(`<div class="invalid-feedback d-block">${errorMsg}</div>`);
                    } else {
                        $(`#${key}`).addClass('is-invalid');
                        $(`#${key}`).after(`<div class="invalid-feedback">${errorMsg}</div>`);
                    }
                });
            }
        });
    });
});
</script>
@endsection