@extends('layouts.admin')

@section('title', 'GL Postings')

@section('content')
<div class="nk-content ">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">General Ledger Postings</h3>
                            <div class="nk-block-des text-soft">
                                <p>Double-Entry General Ledger journal lines automatically posted from approved payroll periods.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats summary card -->
                <div class="row g-gs mb-4">
                    <div class="col-sm-6">
                        <div class="card card-bordered bg-light">
                            <div class="card-inner">
                                <div class="card-title text-soft mb-1">Total Journal Debits</div>
                                <div class="amount text-success">@currency($stats['total_debits'])</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-bordered bg-light">
                            <div class="card-inner">
                                <div class="card-title text-soft mb-1">Total Journal Credits</div>
                                <div class="amount text-primary">@currency($stats['total_credits'])</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GL Entries Table -->
                <div class="card card-bordered card-stretch">
                    <div class="card-inner-group">
                        <div class="card-inner p-0">
                            <table class="table table-tranx">
                                <thead>
                                    <tr class="tb-tnx-head">
                                        <th class="tb-tnx-id">Date</th>
                                        <th class="tb-tnx-info">Reference</th>
                                        <th class="tb-tnx-info">Account Name</th>
                                        <th class="tb-tnx-info">Type</th>
                                        <th class="tb-tnx-info text-end">Debit</th>
                                        <th class="tb-tnx-info text-end">Credit</th>
                                        <th class="tb-tnx-info">Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($entries as $entry)
                                    <tr class="tb-tnx-item">
                                        <td class="tb-tnx-id">
                                            <span class="sub-text">{{ $entry->posting_date->format('Y-m-d') }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <span class="badge badge-dim bg-outline-primary">{{ $entry->reference }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <span class="lead-text">{{ $entry->account_name }}</span>
                                        </td>
                                        <td class="tb-tnx-info">
                                            <span class="sub-text text-soft">{{ $entry->account_type }}</span>
                                        </td>
                                        <td class="tb-tnx-info text-end text-success">
                                            @if($entry->debit > 0)
                                                <span class="lead-text">@currency($entry->debit)</span>
                                            @else
                                                <span class="text-soft">-</span>
                                            @endif
                                        </td>
                                        <td class="tb-tnx-info text-end text-primary">
                                            @if($entry->credit > 0)
                                                <span class="lead-text">@currency($entry->credit)</span>
                                            @else
                                                <span class="text-soft">-</span>
                                            @endif
                                        </td>
                                        <td class="tb-tnx-info">
                                            <span class="sub-text text-soft">{{ $entry->description }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-4">
                                            <div class="text-muted">No General Ledger postings found. Run and approve a payroll to generate entries.</div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
