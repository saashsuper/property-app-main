<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Docket - {{ $workOrder->ref_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #4a90e2;
        }
        
        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 16px;
            color: #4a90e2;
            margin-bottom: 10px;
            font-weight: normal;
        }
        
        .header p {
            font-size: 10px;
            color: #7f8c8d;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            background-color: #ecf0f1;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-left: 4px solid #4a90e2;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            width: 35%;
            padding: 6px 10px;
            font-weight: bold;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
        }
        
        .info-value {
            display: table-cell;
            width: 65%;
            padding: 6px 10px;
            border: 1px solid #dee2e6;
            border-left: none;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .priority-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 10px;
        }
        
        .priority-low {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .priority-normal {
            background-color: #d4edda;
            color: #155724;
        }
        
        .priority-high {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .priority-urgent {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .priority-critical {
            background-color: #f5c6cb;
            color: #721c24;
        }
        
        .image-container {
            margin: 10px 0;
            page-break-inside: avoid;
        }
        
        .image-wrapper {
            display: inline-block;
            margin: 5px;
            border: 1px solid #dee2e6;
            padding: 5px;
            text-align: center;
        }
        
        .image-wrapper img {
            max-width: 150px;
            max-height: 150px;
            display: block;
        }
        
        .image-caption {
            font-size: 9px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .notes-section {
            background-color: #f8f9fa;
            padding: 10px;
            border-left: 3px solid #4a90e2;
            margin: 10px 0;
        }
        
        .note-item {
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .note-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .note-meta {
            font-size: 9px;
            color: #6c757d;
            margin-bottom: 3px;
        }
        
        .note-content {
            font-size: 10px;
        }
        
        .team-member {
            display: table-row;
        }
        
        .team-member-label {
            display: table-cell;
            width: 50%;
            padding: 4px 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        
        .team-member-value {
            display: table-cell;
            width: 50%;
            padding: 4px 10px;
            border: 1px solid #dee2e6;
            border-left: none;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #6c757d;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mb-2 {
            margin-bottom: 8px;
        }
        
        .mt-3 {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>WORK DOCKET</h1>
        <h2>{{ $workOrder->ref_no }}</h2>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>

    <!-- Work Order Basic Information -->
    <div class="section">
        <div class="section-title">Work Order Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Reference Number:</div>
                <div class="info-value"><strong>{{ $workOrder->ref_no }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $workOrder->status_text)) }}">
                        {{ $workOrder->status_text }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Priority:</div>
                <div class="info-value">
                    <span class="priority-badge priority-{{ strtolower($workOrder->priority_text) }}">
                        {{ $workOrder->priority_text }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Issue Description:</div>
                <div class="info-value">{{ $workOrder->issue ?? 'N/A' }}</div>
            </div>
            @if($workOrder->comment)
            <div class="info-row">
                <div class="info-label">Comments:</div>
                <div class="info-value">{{ $workOrder->comment }}</div>
            </div>
            @endif
            <div class="info-row">
                <div class="info-label">Issued Date:</div>
                <div class="info-value">{{ $workOrder->issued_date_time ? $workOrder->issued_date_time->format('M d, Y H:i') : 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Deadline:</div>
                <div class="info-value">{{ $workOrder->deadline_date ? $workOrder->deadline_date->format('M d, Y') : 'N/A' }}</div>
            </div>
            @if($workOrder->preferred_start_date_time)
            <div class="info-row">
                <div class="info-label">Preferred Start:</div>
                <div class="info-value">{{ $workOrder->preferred_start_date_time->format('M d, Y H:i') }}</div>
            </div>
            @endif
            @if($workOrder->preferred_end_date_time)
            <div class="info-row">
                <div class="info-label">Preferred End:</div>
                <div class="info-value">{{ $workOrder->preferred_end_date_time->format('M d, Y H:i') }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Block Information -->
    <div class="section">
        <div class="section-title">Block Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Block Name:</div>
                <div class="info-value">{{ $workOrder->block->name ?? 'N/A' }}</div>
            </div>
            @if($workOrder->blockUnit)
            <div class="info-row">
                <div class="info-label">Unit:</div>
                <div class="info-value">{{ $workOrder->blockUnit->unit_name }}</div>
            </div>
            @endif
            @if($workOrder->blockBuilding)
            <div class="info-row">
                <div class="info-label">Building:</div>
                <div class="info-value">{{ $workOrder->blockBuilding->name }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Contact Information -->
    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="info-grid">
            @if($workOrder->contact_name)
            <div class="info-row">
                <div class="info-label">Contact Name:</div>
                <div class="info-value">{{ $workOrder->contact_name }}</div>
            </div>
            @endif
            @if($workOrder->contact_mobile)
            <div class="info-row">
                <div class="info-label">Contact Mobile:</div>
                <div class="info-value">{{ $workOrder->contact_mobile }}</div>
            </div>
            @endif
            @if($workOrder->contact_email)
            <div class="info-row">
                <div class="info-label">Contact Email:</div>
                <div class="info-value">{{ $workOrder->contact_email }}</div>
            </div>
            @endif
            @if($workOrder->note_for_access)
            <div class="info-row">
                <div class="info-label">Access Notes:</div>
                <div class="info-value">{{ $workOrder->note_for_access }}</div>
            </div>
            @endif
        </div>
    </div>

    <!-- Contractor/Team Information -->
    <div class="section">
        <div class="section-title">Contractor & Team Members</div>
        <div class="info-grid">
            @if($workOrder->contractCompany)
            <div class="info-row">
                <div class="info-label">Contract Company:</div>
                <div class="info-value">{{ $workOrder->contractCompany->name ?? 'N/A' }}</div>
            </div>
            @elseif($workOrder->contractor)
            <div class="info-row">
                <div class="info-label">Contractor:</div>
                <div class="info-value">{{ $workOrder->contractor->name ?? 'N/A' }}</div>
            </div>
            @endif
            @if($workOrder->issuedBy)
            <div class="info-row">
                <div class="info-label">Issued By:</div>
                <div class="info-value">{{ $workOrder->issuedBy->name ?? 'N/A' }}</div>
            </div>
            @endif
        </div>
        
        @if($workOrder->teamMembers && $workOrder->teamMembers->count() > 0)
        <div class="mt-3">
            <strong>Team Members:</strong>
            <div class="info-grid" style="margin-top: 8px;">
                @foreach($workOrder->teamMembers as $member)
                <div class="team-member">
                    <div class="team-member-label">
                        @if($member->is_lead) <strong>Lead:</strong> @endif
                        {{ $member->user->name ?? 'N/A' }}
                    </div>
                    <div class="team-member-value">
                        {{ $member->role ?? 'Team Member' }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Related Issue Details -->
    @if($workOrder->blockIssue)
    <div class="section">
        <div class="section-title">Related Issue Details</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Issue Reference:</div>
                <div class="info-value"><strong>{{ $workOrder->blockIssue->ref_no }}</strong></div>
            </div>
            <div class="info-row">
                <div class="info-label">Issue Title:</div>
                <div class="info-value">{{ $workOrder->blockIssue->issue ?? 'N/A' }}</div>
            </div>
            @if($workOrder->blockIssue->issueType)
            <div class="info-row">
                <div class="info-label">Issue Type:</div>
                <div class="info-value">{{ $workOrder->blockIssue->issueType->name }}</div>
            </div>
            @endif
            @if($workOrder->blockIssue->priority)
            <div class="info-row">
                <div class="info-label">Issue Priority:</div>
                <div class="info-value">
                    <span class="priority-badge priority-{{ strtolower($workOrder->blockIssue->priority->name ?? 'normal') }}">
                        {{ $workOrder->blockIssue->priority->name ?? 'Normal' }}
                    </span>
                </div>
            </div>
            @endif
            @if($workOrder->blockIssue->issueStatus)
            <div class="info-row">
                <div class="info-label">Issue Status:</div>
                <div class="info-value">{{ $workOrder->blockIssue->issueStatus->name ?? 'N/A' }}</div>
            </div>
            @endif
            @if($workOrder->blockIssue->issue_details || $workOrder->blockIssue->description)
            <div class="info-row">
                <div class="info-label">Issue Description:</div>
                <div class="info-value">{{ $workOrder->blockIssue->issue_details ?? $workOrder->blockIssue->description ?? 'N/A' }}</div>
            </div>
            @endif
            @if($workOrder->blockIssue->reportedBy)
            <div class="info-row">
                <div class="info-label">Reported By:</div>
                <div class="info-value">{{ $workOrder->blockIssue->reportedBy->name ?? 'N/A' }}</div>
            </div>
            @endif
            @if($workOrder->blockIssue->assignedTo)
            <div class="info-row">
                <div class="info-label">Assigned To:</div>
                <div class="info-value">{{ $workOrder->blockIssue->assignedTo->name ?? 'N/A' }}</div>
            </div>
            @endif
            @if($workOrder->blockIssue->created_at)
            <div class="info-row">
                <div class="info-label">Issue Created:</div>
                <div class="info-value">{{ $workOrder->blockIssue->created_at->format('M d, Y H:i') }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Work Order Notes -->
    @if($workOrder->notes && $workOrder->notes->count() > 0)
    <div class="section">
        <div class="section-title">Work Order Notes</div>
        <div class="notes-section">
            @foreach($workOrder->notes as $note)
            <div class="note-item">
                <div class="note-meta">
                    @if($note->creator)
                        {{ $note->creator->name ?? 'System' }}
                    @else
                        System
                    @endif
                    - {{ $note->created_at->format('M d, Y H:i') }}
                    @if($note->note_type)
                        <span class="text-muted">({{ $note->note_type }})</span>
                    @endif
                </div>
                <div class="note-content">{{ $note->note }}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Work Order Photos -->
    @if($workOrder->images && $workOrder->images->count() > 0)
    <div class="section">
        <div class="section-title">Work Order Photos ({{ $workOrder->images->count() }})</div>
        <div class="image-container">
            @foreach($workOrder->images as $image)
            @if($image->image_path && $image->image_name)
            <div class="image-wrapper">
                @php
                    $imagePath = storage_path('app/public/' . $image->image_path . '/' . $image->image_name);
                    $imageExists = file_exists($imagePath);
                @endphp
                @if($imageExists)
                <img src="{{ $imagePath }}" alt="Work Order Photo">
                @else
                <div style="width: 150px; height: 150px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    <span class="text-muted">Image not found</span>
                </div>
                @endif
                <div class="image-caption">
                    {{ $image->image_name }}
                    @if(isset($image->latitude) && isset($image->longitude) && $image->latitude && $image->longitude)
                        <br>Location: {{ number_format($image->latitude, 6) }}, {{ number_format($image->longitude, 6) }}
                    @endif
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Issue Photos -->
    @if($workOrder->blockIssue && $workOrder->blockIssue->images && $workOrder->blockIssue->images->count() > 0)
    <div class="section">
        <div class="section-title">Issue Photos ({{ $workOrder->blockIssue->images->count() }})</div>
        <div class="image-container">
            @foreach($workOrder->blockIssue->images as $image)
            @if($image->image_path && $image->image_name)
            <div class="image-wrapper">
                @php
                    $imagePath = storage_path('app/public/' . $image->image_path . '/' . $image->image_name);
                    $imageExists = file_exists($imagePath);
                @endphp
                @if($imageExists)
                <img src="{{ $imagePath }}" alt="Issue Photo">
                @else
                <div style="width: 150px; height: 150px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                    <span class="text-muted">Image not found</span>
                </div>
                @endif
                <div class="image-caption">{{ $image->image_name ?? 'Issue Photo' }}</div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This work docket was automatically generated when the work order was marked as completed.</p>
        <p>Work Order Reference: {{ $workOrder->ref_no }} | Generated: {{ now()->format('F d, Y \a\t H:i') }}</p>
    </div>
</body>
</html>

