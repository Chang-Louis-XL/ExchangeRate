@extends('layouts.backend')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">幣別管理</h3>
            <a href="{{ route('backend.create') }}" class="btn btn-light">新增幣別</a>
        </div>
        <div class="card-body">
            @if (session('success') || request('success'))
                <div class="alert alert-success">
                    {{ session('success') ?? request('success') }}
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>幣別代碼</th>
                            <th>幣別名稱</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($currencies as $currency)
                        <tr>
                            <td>{{ $currency->id }}</td>
                            <td>{{ $currency->code }}</td>
                            <td>{{ $currency->name }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('backend.show', $currency->id) }}" class="btn btn-sm btn-info">查看</a>
                                    <a href="{{ route('backend.edit', $currency->id) }}" class="btn btn-sm btn-warning">編輯</a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="{{ $currency->id }}">刪除</button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if(count($currencies) == 0)
                        <tr>
                            <td colspan="4" class="text-center">沒有幣別資料</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.delete-btn').click(function() {
        const currencyId = $(this).data('id');
        
        if (confirm('確定要刪除此幣別嗎？')) {
            $.ajax({
                url: `/api/currencies/${currencyId}`,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    window.location.reload();
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