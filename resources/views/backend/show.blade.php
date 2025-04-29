@extends('layouts.backend')

@section('content')
<div class="container mt-4">
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">幣別詳情</h3>
            <a href="{{ route('backend.index') }}" class="btn btn-light">返回列表</a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>基本資訊</h4>
                    <table class="table">
                        <tr>
                            <th style="width: 150px">ID</th>
                            <td>{{ $currency->id }}</td>
                        </tr>
                        <tr>
                            <th>幣別代碼</th>
                            <td>{{ $currency->code }}</td>
                        </tr>
                        <tr>
                            <th>幣別名稱</th>
                            <td>{{ $currency->name }}</td>
                        </tr>
                        <tr>
                            <th>建立時間</th>
                            <td>{{ $currency->created_at }}</td>
                        </tr>
                        <tr>
                            <th>最後更新</th>
                            <td>{{ $currency->updated_at }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h4>相關匯率資料</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>基準幣別</th>
                            <th>目標幣別</th>
                            <th>匯率</th>
                            <th>最後更新時間</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exchangeRates as $rate)
                        <tr>
                            <td>{{ $rate->base_currency }}</td>
                            <td>{{ $rate->target_currency }}</td>
                            <td>{{ $rate->rate }}</td>
                            <td>{{ $rate->last_updated }}</td>
                        </tr>
                        @endforeach

                        @if(count($exchangeRates) == 0)
                        <tr>
                            <td colspan="4" class="text-center">沒有相關匯率資料</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
                <div class="d-flex mt-3">
                    <a href="{{ route('backend.edit', $currency->id) }}" class="btn btn-warning me-2">編輯此幣別</a>
                    <button type="button" id="deleteBtn" class="btn btn-danger">刪除此幣別</button>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#deleteBtn').click(function() {
            if (confirm('確定要刪除此幣別嗎？')) {
                const currencyId = {
                    {
                        $currency - > id
                    }
                };

                $.ajax({
                    url: `/api/currencies/${currencyId}`,
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        window.location.href = "{{ route('backend.index') }}?success=幣別刪除成功！";
                    },
                    error: function(xhr) {
                        alert(xhr.responseJSON?.error || '刪除失敗');
                    }
                });
            }
        });
    });
</script>
@endsection