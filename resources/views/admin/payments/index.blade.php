@extends('layouts.app')

@section('title', 'Wallet Deposits')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Wallet Deposits</h1>

    <div class="card">
        <div class="card-body table-responsive">
            <table id="depositsTable" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#depositsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.payments.index.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'wallet.user.name', name: 'wallet.user.name' },
            { data: 'amount', name: 'amount' },
            { data: 'method', name: 'method' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
        ]
    });
});
</script>
@endpush
