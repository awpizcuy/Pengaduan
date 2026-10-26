<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\Report;
use Illuminate\Support\Facades\DB;

class ComplaintNumberService
{
    public function createComplaint(Report $report, int $createdBy, string $message): Complaint
    {
        return DB::transaction(function () use ($report, $createdBy, $message) {

            // urutan komplain berikutnya per report (aman dari submit bersamaan)
            $nextSeq = (int) Complaint::where('report_id', $report->id)
                ->lockForUpdate()
                ->max('sequence') + 1;

            $complaintNo = sprintf('CMP-%06d-%03d', $report->id, $nextSeq);

            return Complaint::create([
                'report_id'    => $report->id,
                'created_by'   => $createdBy,
                'sequence'     => $nextSeq,
                'complaint_no' => $complaintNo,
                'message'      => $message,
                'status'       => 'submitted',
            ]);
        });
    }
}
