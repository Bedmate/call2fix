@extends('layouts.app')

@section('title', 'Artisan Withdrawals')

@section('content')
<div class="container-fluid">
    <h1 class="mb-4">Artisan Withdrawals</h1>

    <div class="card">
        <div class="card-body table-responsive">
            <table id="artisanTable" class="table table-bordered table-striped table-hover w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Artisan</th>
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
    $('#artisanTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.payments.artisan.data") }}',
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
