<div class="row">
    <div class="col-md-6">
        <div class="card border">
            <div class="card-header bg-light">
                <h6 class="mb-0">Block Details</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-semibold">Block ID:</td>
                        <td>#{{ $block->id }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Created:</td>
                        <td>{{ $block->created_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Last Updated:</td>
                        <td>{{ $block->updated_at->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Status:</td>
                        <td><span class="badge bg-success">Active</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border">
            <div class="card-header bg-light">
                <h6 class="mb-0">Management Info</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="fw-semibold">Owner:</td>
                        <td>{{ $block->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Block Manager:</td>
                        <td>{{ $block->blockManager->name ?? 'Not Assigned' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Block Type:</td>
                        <td>{{ $block->blockType->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Country:</td>
                        <td>{{ $block->country->country_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">State:</td>
                        <td>{{ $block->state->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Block Address:</td>
                        <td>{{ $block->block_address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Company Address:</td>
                        <td>{{ $block->management_company_address ?? 'Not Provided' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
