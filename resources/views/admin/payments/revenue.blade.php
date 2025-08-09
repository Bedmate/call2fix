@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Revenue Transactions</h2>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped" id="revenue-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Wallet</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Transaction Type</th>
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
$(function() {
    $('#revenue-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.payments.revenue.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'wallet.user.name', name: 'wallet.user.name' },
            { data: 'wallet.name', name: 'wallet.name' },
            { data: 'amount', name: 'amount' },
            { data: 'type', name: 'type' },
            { data: 'meta.transaction_type', name: 'meta.transaction_type' },
            { data: 'created_at', name: 'created_at' }
        ]
    });
});
</script>
@endpush
