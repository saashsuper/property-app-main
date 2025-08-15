<tbody>
    @foreach($blockInformation as $info)
        <tr>
            <td>
                <span class="fw-semibold">{{ $info->informationType->name ?? 'N/A' }}</span>
            </td>
            <td>{{ $info->description ?? 'No description provided' }}</td>
            <td>{{ $info->created_at ? $info->created_at->format('M d, Y') : 'N/A' }}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="editBlockInformation({{ $info->id }})">
                    <i class="ri-edit-line"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteBlockInformation({{ $info->id }})">
                    <i class="ri-delete-bin-line"></i> Delete
                </button>
            </td>
        </tr>
    @endforeach
</tbody>
