@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Transactions</h1>

    <div class="card">
        <div class="card-body table-responsive">
            <div class="mb-3 d-flex">
                <label class="me-2 fw-bold">Filter by User Type:</label>
                <select id="userTypeFilter" class="form-select w-auto">
                    <option value="">All</option>
                    <option value="customer">Customer</option>
                    <option value="merchant">Merchant</option>
                    <option value="artisan">Artisan</option>
                    <option value="affiliate">Affiliate</option>
                </select>
            </div>

            <table id="transactionsTable" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>User Type</th>
                        <th>Amount</th>
                        <th>Type</th>
                        <th>Description</th>
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
    let table = $('#transactionsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.transactions.data") }}',
            data: function (d) {
                d.user_type = $('#userTypeFilter').val(); // Pass filter to server
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'wallet.user.name', name: 'wallet.user.name' },
            { data: 'wallet.user.user_type', name: 'wallet.user.user_type' },
            { data: 'amount', name: 'amount' },
            { data: 'type', name: 'type' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
        ]
    });

    // Reload table when filter changes
    $('#userTypeFilter').change(function () {
        table.ajax.reload();
    });
});
</script>
@endpush
