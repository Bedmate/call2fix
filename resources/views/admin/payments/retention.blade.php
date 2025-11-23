@extends('layouts.app')

@section('title', 'Retention Payments')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4">Retention Payments</h4>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped" id="retention-table" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Service Request</th>
                        <th>Call2Fix Earnings</th>
                        <th>Retention Amount</th>
                        <th>Date Created</th>
                        <th>Release Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="retentionDetailsModal" tabindex="-1" aria-labelledby="retentionDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="retentionDetailsLabel">Retention Payment Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold">Service Request</h6>
                <ul class="list-group mb-3" id="serviceRequestDetails">
                    <!-- Populated by JS -->
                </ul>

                <h6 class="fw-bold">Payment Apportionment</h6>
                <ul class="list-group" id="apportionmentDetails">
                    <!-- Populated by JS -->
                </ul>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    let table = $('#retention-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.payments.retention") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'service_request.problem_title', name: 'service_request.problem_title', defaultContent: 'N/A' },
            { data: 'call2fix_earnings', name: 'call2fix_earnings' },
            { data: 'warranty_retention', name: 'warranty_retention' },
            { data: 'created_at', name: 'created_at' },
            { data: 'release_date', name: 'release_date', defaultContent: 'N/A' },
            { 
                data: null, 
                orderable: false, 
                searchable: false, 
                render: function(data, type, row) {
                    return `<button class="btn btn-sm btn-primary view-details" data-id="${row.id}"><i class="bi bi-eye"></i> View</button>`;
                } 
            }
        ],
        responsive: true
    });

    // Handle View button click
    $('#retention-table').on('click', '.view-details', function () {
        let id = $(this).data('id');
        $.ajax({
            url: `/cp/payments/retention/${id}`, // backend route to fetch details
            type: 'GET',
            success: function (res) {
                // Populate Service Request details
                let sr = res.service_request || {};
                $('#serviceRequestDetails').html(`
                    <li class="list-group-item"><strong>Title:</strong> ${sr.problem_title ?? 'N/A'}</li>
                    <li class="list-group-item"><strong>Description:</strong> ${sr.problem_description ?? 'N/A'}</li>
                    <li class="list-group-item"><strong>Status:</strong> ${sr.request_status ?? 'N/A'}</li>
                    <li class="list-group-item"><strong>Created At:</strong> ${sr.created_at ?? 'N/A'}</li>
                `);

                // Populate Apportionment details
                let ap = res;
                $('#apportionmentDetails').html(`
                    <li class="list-group-item"><strong>Subtotal:</strong> ${ap.subtotal ?? 0}</li>
                    <li class="list-group-item"><strong>Service Provider Earnings:</strong> ${ap.service_provider_earnings ?? 0}</li>
                    <li class="list-group-item"><strong>Management Fee:</strong> ${ap.call2fix_management_fee ?? 0}</li>
                    <li class="list-group-item"><strong>Call2Fix Earnings:</strong> ${ap.call2fix_earnings ?? 0}</li>
                    <li class="list-group-item"><strong>Warranty Retention:</strong> ${ap.warranty_retention ?? 0}</li>
                    <li class="list-group-item"><strong>Artisan Earnings:</strong> ${ap.artisan_earnings ?? 0}</li>
                    <li class="list-group-item"><strong>Date Created:</strong> ${ap.created_at ?? 'N/A'}</li>
                `);

                // Show modal
                let modal = new bootstrap.Modal(document.getElementById('retentionDetailsModal'));
                modal.show();
            },
            error: function () {
                alert('Unable to fetch details.');
            }
        });
    });
});
</script>
@endpush
