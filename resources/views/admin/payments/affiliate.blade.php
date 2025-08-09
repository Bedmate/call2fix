@extends('layouts.app')

@section('title', 'Affiliate Withdrawals')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Affiliate Withdrawals</h1>

    <div class="card">
        <div class="card-body table-responsive">
            <table id="affiliateTable" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Affiliate</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date Requested</th>
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
    $('#affiliateTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.payments.affiliate.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'wallet.user.name', name: 'wallet.user.name' },
            { data: 'amount', name: 'amount' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
        ]
    });
});
</script>
@endpush
