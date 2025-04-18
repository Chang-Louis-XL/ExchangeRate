@extends('layouts.frontend')

@section('title', '貨幣轉換器')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">貨幣轉換器</h3>
                </div>
                <div class="card-body">
                    <form id="convertForm" class="needs-validation" novalidate>
                        <div class="mb-3 row">
                            <label for="fromCurrency" class="col-sm-3 col-form-label">從貨幣:</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="fromCurrency" name="from_currency" required>
                                    <option value="">選擇貨幣</option>
                                    @foreach($supportedCurrencies as $currency)
                                        <option value="{{ $currency }}">{{ $currency }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">請選擇來源貨幣</div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="toCurrency" class="col-sm-3 col-form-label">至貨幣:</label>
                            <div class="col-sm-9">
                                <select class="form-select" id="toCurrency" name="to_currency" required>
                                    <option value="">選擇貨幣</option>
                                    @foreach($supportedCurrencies as $currency)
                                        <option value="{{ $currency }}">{{ $currency }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">請選擇目標貨幣</div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label for="amount" class="col-sm-3 col-form-label">金額:</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="amount" name="amount" min="0.01" step="0.01" required>
                                <div class="invalid-feedback">請輸入有效金額</div>
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">轉換</button>
                        </div>
                    </form>
                    
                    <div id="result" class="mt-4 p-3 border rounded bg-light result-box" style="display: none;">
                        <h4 class="mb-3">轉換結果</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>從貨幣:</strong> <span id="resultFromCurrency"></span></p>
                                <p><strong>金額:</strong> <span id="resultAmount"></span></p>
                                <p><strong>使用匯率:</strong> <span id="resultRate"></span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>至貨幣:</strong> <span id="resultToCurrency"></span></p>
                                <p><strong>轉換後金額:</strong> <span id="resultConvertedAmount"></span></p>
                                <p><strong>最新匯率時間:</strong> <span id="lastUpdated"></span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="error" class="mt-4 p-3 border rounded bg-danger text-white result-box" style="display: none;">
                        <p class="mb-0" id="errorMessage"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        //阻止表單的預設提交行為，避免頁面重新加載
        $('#convertForm').submit(function(e) {
            e.preventDefault();
            
            // 表單驗證
            if (!this.checkValidity()) {
                e.stopPropagation();
                $(this).addClass('was-validated');
                return;
            }
            
            // 隱藏之前的結果和錯誤
            $('#result').hide();
            $('#error').hide();
            
            // 獲取表單數據
            const formData = {
                from_currency: $('#fromCurrency').val(),
                to_currency: $('#toCurrency').val(),
                amount: parseFloat($('#amount').val())
            };
            
            // 使用 AJAX 發送 API 請求
            $.ajax({
                url: '/api/currency/convert',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // 顯示結果
                    $('#resultFromCurrency').text(response.from_currency);
                    $('#resultToCurrency').text(response.to_currency);
                    $('#resultAmount').text(response.amount);
                    $('#resultConvertedAmount').text(response.converted_amount);
                    $('#resultRate').text(response.rate);
                    
                    const lastUpdated = new Date(response.last_updated);
                    $('#lastUpdated').text(lastUpdated.toLocaleString());
                    
                    $('#result').fadeIn();
                },
                error: function(xhr) {
                    let errorMessage = '發生錯誤，請稍後再試。';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors).flat().join('<br>');
                    }
                    
                    $('#errorMessage').html(errorMessage);
                    $('#error').fadeIn();
                }
            });
        });
    });
</script>
@endsection