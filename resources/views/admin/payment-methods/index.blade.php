<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Methods</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Payment Methods</h1>
                    <div>
                        <form action="{{ route('myfatoorah.admin.payment-methods.sync') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-info">Sync from API</button>
                        </form>
                        <a href="{{ route('myfatoorah.admin.payment-methods.create') }}" class="btn btn-primary">Add New</a>
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name (EN)</th>
                                <th>Name (AR)</th>
                                <th>Code</th>
                                <th>Direct Payment</th>
                                <th>Service Charge</th>
                                <th>iOS</th>
                                <th>Android</th>
                                <th>Web</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentMethods as $method)
                                <tr>
                                    <td>{{ $method->payment_method_id }}</td>
                                    <td>{{ $method->payment_method_en }}</td>
                                    <td>{{ $method->payment_method_ar }}</td>
                                    <td>{{ $method->payment_method_code }}</td>
                                    <td>
                                        @if($method->is_direct_payment)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $method->service_charge }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input platform-toggle" 
                                                   type="checkbox" 
                                                   data-method-id="{{ $method->id }}"
                                                   data-platform="ios"
                                                   {{ $method->is_active_ios ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input platform-toggle" 
                                                   type="checkbox" 
                                                   data-method-id="{{ $method->id }}"
                                                   data-platform="android"
                                                   {{ $method->is_active_android ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input platform-toggle" 
                                                   type="checkbox" 
                                                   data-method-id="{{ $method->id }}"
                                                   data-platform="web"
                                                   {{ $method->is_active_web ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        @if($method->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No payment methods found. Please sync from API.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.platform-toggle').forEach(function(toggle) {
                toggle.addEventListener('change', function() {
                    const methodId = this.dataset.methodId;
                    const platform = this.dataset.platform;
                    const status = this.checked;

                    fetch(`{{ url('admin/myfatoorah/payment-methods') }}/${methodId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            platform: platform,
                            status: status
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            this.checked = !status;
                            alert('Failed to update status');
                        }
                    })
                    .catch(error => {
                        this.checked = !status;
                        alert('Error updating status');
                    });
                });
            });
        });
    </script>
</body>
</html>

