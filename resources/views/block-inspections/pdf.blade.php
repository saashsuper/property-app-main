<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspection Report - {{ $inspection->ref_no }}</title>
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
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-scheduled {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        
        th {
            background-color: #4a90e2;
            color: white;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .asset-status-working {
            color: #28a745;
            font-weight: bold;
        }
        
        .asset-status-not-working {
            color: #dc3545;
            font-weight: bold;
        }
        
        .asset-status-na {
            color: #6c757d;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 9px;
            color: #7f8c8d;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-muted {
            color: #6c757d;
            font-style: italic;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-primary {
            background-color: #cfe2ff;
            color: #084298;
        }
        
        .badge-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        
        .notes-box {
            background-color: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
        }
        
        .asset-images {
            margin-top: 5px;
        }
        
        .asset-image {
            max-width: 180px;
            max-height: 180px;
            border: 2px solid #dee2e6;
            margin: 5px;
            padding: 3px;
            background: white;
        }
        
        .images-section {
            margin-top: 15px;
            page-break-inside: avoid;
        }
        
        .images-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
        }
        
        .image-item {
            display: inline-block;
            text-align: center;
            margin: 5px;
            vertical-align: top;
        }
        
        .image-caption {
            font-size: 8px;
            color: #6c757d;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>BLOCK INSPECTION REPORT</h1>
        <h2>{{ $inspection->ref_no }}</h2>
        <p>PROMAN - Property Management System</p>
        <p>Generated on: {{ now()->format('F d, Y H:i') }}</p>
    </div>

    <!-- Inspection Information -->
    <div class="section">
        <div class="section-title">Inspection Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Reference Number</div>
                <div class="info-value">{{ $inspection->ref_no }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $inspection->status_text)) }}">
                        {{ $inspection->status_text }}
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Scheduled Date & Time</div>
                <div class="info-value">{{ $inspection->scheduled_date_time->format('F d, Y \a\t H:i') }}</div>
            </div>
            @if($inspection->start_date_time)
            <div class="info-row">
                <div class="info-label">Start Date & Time</div>
                <div class="info-value">{{ $inspection->start_date_time->format('F d, Y \a\t H:i') }}</div>
            </div>
            @endif
            @if($inspection->end_date_time)
            <div class="info-row">
                <div class="info-label">End Date & Time</div>
                <div class="info-value">{{ $inspection->end_date_time->format('F d, Y \a\t H:i') }}</div>
            </div>
            @if($inspection->start_date_time && $inspection->end_date_time)
            <div class="info-row">
                <div class="info-label">Duration</div>
                <div class="info-value">
                    {{ $inspection->start_date_time->diff($inspection->end_date_time)->format('%h hours %i minutes') }}
                </div>
            </div>
            @endif
            @endif
            <div class="info-row">
                <div class="info-label">Created By</div>
                <div class="info-value">{{ $inspection->creator->name ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Created Date</div>
                <div class="info-value">{{ $inspection->created_at->format('F d, Y \a\t H:i') }}</div>
            </div>
        </div>
        
        @if($inspection->notes)
        <div class="notes-box">
            <strong>Notes:</strong><br>
            {{ $inspection->notes }}
        </div>
        @endif
    </div>

    <!-- Block Information -->
    <div class="section">
        <div class="section-title">Block Information</div>
        @if($inspection->block)
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Block Name</div>
                <div class="info-value">{{ $inspection->block->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Address</div>
                <div class="info-value">
                    {{ $inspection->block->address1 }}
                    @if($inspection->block->address2), {{ $inspection->block->address2 }}@endif
                    @if($inspection->block->address3), {{ $inspection->block->address3 }}@endif
                </div>
            </div>
            @if($inspection->block->management_company)
            <div class="info-row">
                <div class="info-label">Management Company</div>
                <div class="info-value">{{ $inspection->block->management_company }}</div>
            </div>
            @endif
            @if($inspection->block->blockType)
            <div class="info-row">
                <div class="info-label">Block Type</div>
                <div class="info-value">{{ $inspection->block->blockType->name }}</div>
            </div>
            @endif
            @if($inspection->block->no_of_units)
            <div class="info-row">
                <div class="info-label">Number of Units</div>
                <div class="info-value">{{ $inspection->block->no_of_units }}</div>
            </div>
            @endif
        </div>
        @else
        <p class="text-muted">Block information not available</p>
        @endif
    </div>

    <!-- Inspection Team -->
    <div class="section">
        <div class="section-title">Inspection Team</div>
        @if($inspection->inspectionTeams->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 35%;">Name</th>
                    <th style="width: 35%;">Email</th>
                    <th style="width: 20%;">Role</th>
                    <th style="width: 10%;">Lead</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inspection->inspectionTeams as $teamMember)
                <tr>
                    <td>{{ $teamMember->user ? $teamMember->user->name : 'N/A' }}</td>
                    <td>{{ $teamMember->user ? $teamMember->user->email : 'N/A' }}</td>
                    <td>{{ $teamMember->role }}</td>
                    <td class="text-center">
                        @if($teamMember->is_lead)
                            ✓
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted">No team members assigned</p>
        @endif
    </div>

    <!-- Inspection Assets -->
    @if($inspection->inspectionAssets->count() > 0)
    <div class="section">
        <div class="section-title">Inspection Assets ({{ $inspection->inspectionAssets->count() }} items)</div>
        
        @php
            $generalAssets = $inspection->inspectionAssets->where('block_general_asset_id', '!=', null);
            $buildingAssets = $inspection->inspectionAssets->where('building_asset_id', '!=', null);
        @endphp
        
        @if($generalAssets->count() > 0)
        <h3 style="font-size: 12px; margin: 15px 0 10px 0; color: #2c3e50;">General Assets</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Asset Name</th>
                    <th style="width: 15%;">Building</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 45%;">Comments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($generalAssets as $asset)
                <tr>
                    <td><strong>{{ $asset->generalAsset->name ?? 'N/A' }}</strong></td>
                    <td>{{ $asset->blockBuilding->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            $valueName = $asset->inspectionValue->name ?? 'N/A';
                            if(in_array(strtolower($valueName), ['good', 'working', 'operational'])) {
                                $statusClass = 'asset-status-working';
                            } elseif(in_array(strtolower($valueName), ['poor', 'not working', 'non-operational'])) {
                                $statusClass = 'asset-status-not-working';
                            } else {
                                $statusClass = 'asset-status-na';
                            }
                        @endphp
                        <span class="{{ $statusClass }}">{{ $valueName }}</span>
                    </td>
                    <td>{{ $asset->comments ?? '-' }}</td>
                </tr>
                @if($asset->images->count() > 0)
                <tr>
                    <td colspan="4" style="background-color: #f8f9fa; padding: 10px;">
                        <strong style="font-size: 10px; color: #495057;">Images ({{ $asset->images->count() }}):</strong>
                        <div style="margin-top: 8px;">
                            @foreach($asset->images as $image)
                                @php
                                    $imagePath = storage_path('app/public/' . $image->image_path . '/' . $image->image_name);
                                @endphp
                                @if(file_exists($imagePath))
                                <div class="image-item">
                                    <img src="{{ $imagePath }}" class="asset-image" alt="Asset Image">
                                    <div class="image-caption">{{ $loop->iteration }}/{{ $asset->images->count() }}</div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
        
        @if($buildingAssets->count() > 0)
        <h3 style="font-size: 12px; margin: 15px 0 10px 0; color: #2c3e50;">Building Assets</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 25%;">Asset Name</th>
                    <th style="width: 15%;">Building</th>
                    <th style="width: 15%;">Status</th>
                    <th style="width: 45%;">Comments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($buildingAssets as $asset)
                <tr>
                    <td><strong>{{ $asset->buildingAsset->name ?? 'N/A' }}</strong></td>
                    <td>{{ $asset->blockBuilding->name ?? 'N/A' }}</td>
                    <td>
                        @php
                            $statusClass = '';
                            $valueName = $asset->inspectionValue->name ?? 'N/A';
                            if(in_array(strtolower($valueName), ['good', 'working', 'operational'])) {
                                $statusClass = 'asset-status-working';
                            } elseif(in_array(strtolower($valueName), ['poor', 'not working', 'non-operational'])) {
                                $statusClass = 'asset-status-not-working';
                            } else {
                                $statusClass = 'asset-status-na';
                            }
                        @endphp
                        <span class="{{ $statusClass }}">{{ $valueName }}</span>
                    </td>
                    <td>{{ $asset->comments ?? '-' }}</td>
                </tr>
                @if($asset->images->count() > 0)
                <tr>
                    <td colspan="4" style="background-color: #f8f9fa; padding: 10px;">
                        <strong style="font-size: 10px; color: #495057;">Images ({{ $asset->images->count() }}):</strong>
                        <div style="margin-top: 8px;">
                            @foreach($asset->images as $image)
                                @php
                                    $imagePath = storage_path('app/public/' . $image->image_path . '/' . $image->image_name);
                                @endphp
                                @if(file_exists($imagePath))
                                <div class="image-item">
                                    <img src="{{ $imagePath }}" class="asset-image" alt="Asset Image">
                                    <div class="image-caption">{{ $loop->iteration }}/{{ $asset->images->count() }}</div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <!-- Inspection Summary -->
    <div class="section">
        <div class="section-title">Inspection Summary</div>
        @php
            $totalAssets = $inspection->inspectionAssets->count();
            $workingAssets = $inspection->inspectionAssets->filter(function($asset) {
                $valueName = strtolower($asset->inspectionValue->name ?? '');
                return in_array($valueName, ['good', 'working', 'operational']);
            })->count();
            $notWorkingAssets = $inspection->inspectionAssets->filter(function($asset) {
                $valueName = strtolower($asset->inspectionValue->name ?? '');
                return in_array($valueName, ['poor', 'not working', 'non-operational']);
            })->count();
            $naAssets = $totalAssets - $workingAssets - $notWorkingAssets;
            $assetsWithComments = $inspection->inspectionAssets->filter(function($asset) {
                return !empty($asset->comments);
            })->count();
            $totalImages = $inspection->inspectionAssets->sum(function($asset) {
                return $asset->images->count();
            });
        @endphp
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Total Assets Inspected</div>
                <div class="info-value">{{ $totalAssets }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Working/Good Condition</div>
                <div class="info-value"><span class="asset-status-working">{{ $workingAssets }} ({{ $totalAssets > 0 ? round(($workingAssets/$totalAssets)*100, 1) : 0 }}%)</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Not Working/Poor Condition</div>
                <div class="info-value"><span class="asset-status-not-working">{{ $notWorkingAssets }} ({{ $totalAssets > 0 ? round(($notWorkingAssets/$totalAssets)*100, 1) : 0 }}%)</span></div>
            </div>
            <div class="info-row">
                <div class="info-label">N/A or Other Status</div>
                <div class="info-value">{{ $naAssets }} ({{ $totalAssets > 0 ? round(($naAssets/$totalAssets)*100, 1) : 0 }}%)</div>
            </div>
            <div class="info-row">
                <div class="info-label">Assets with Comments</div>
                <div class="info-value">{{ $assetsWithComments }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Total Images Captured</div>
                <div class="info-value">{{ $totalImages }}</div>
            </div>
        </div>
    </div>

    @else
    <div class="section">
        <div class="section-title">Inspection Assets</div>
        <p class="text-muted">No assets were inspected during this inspection.</p>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>PROMAN - Property Management System</strong></p>
        <p>This is a computer-generated report and does not require a signature.</p>
        <p>Report generated on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>

