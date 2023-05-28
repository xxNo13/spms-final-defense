<?php

namespace App\Http\Controllers;

use App\Models\Pmt;
use App\Models\Ttma;
use App\Models\User;
use App\Models\Funct;
use App\Models\Office;
use App\Models\Duration;
use App\Models\PrintInfo;
use App\Models\Percentage;
use App\Models\PrintImage;
use Illuminate\Http\Request;
use App\Models\SubPercentage;
use App\Models\ScoreEquivalent;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PdfController extends Controller
{
    public function ipcrFaculty($id, Request $request) {
        $faculty = false;
        $head = false;
        $yours = false;
        $approval_reviewer = [];
        $assess_reviewer = [];
        foreach (auth()->user()->account_types as $account_type) {
            if (str_contains(strtolower($account_type), 'faculty')) {
                $faculty = true;
                break;
            } 
        }
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
                break;
            }
        }
        if ($id == auth()->user()->id) {
            $yours = true;
        }

        if (($yours && $faculty) || $head || auth()->user()->id == 1) {
            
        } else {
            abort(403);
        }


        if (isset($request->duration_id)) {
            $duration = Duration::find($request->duration_id);
        } else {
            $duration = Duration::orderBy('id', 'DESC')->where('type', 'faculty')->where('start_date', '<=', date('Y-m-d'))->first();
        }

        $scoreEquivalent = ScoreEquivalent::first();

        $user = User::find($id);

        if ($duration) {
            $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $duration->id)->first();

            if (!$percentage) {
                $array = [
                    'core' => 0,
                    'strategic' => 0,
                    'support' => 0
                ];

                $percentage = (object) $array;
            }
            $approval = $user->approvals()->orderBy('id', 'DESC')->where('name', 'approval')->where('type', 'ipcr')->where('duration_id', $duration->id)->where('user_type', 'faculty')->first();
            
            $approval_approver = '';
            if ($approval) {
                foreach ($approval->reviewers as $reviewer) {
                    array_push($approval_reviewer, $reviewer);
                }
                $approval_approver = User::find($approval->approve_id);
            }

            $assess = $user->approvals()->orderBy('id', 'DESC')->where('name', 'assess')->where('type', 'ipcr')->where('duration_id', $duration->id)->where('user_type', 'faculty')->first();
            
            $assess_approver = '';
            if ($assess) {
                foreach ($assess->reviewers as $reviewer) {
                    array_push($assess_reviewer, $reviewer);
                }
                $assess_approver = User::find($assess->approve_id);
            }
        }

        $printInfo = PrintInfo::where('user_id', $user->id)
                            ->where('duration_id', $duration->id)
                            ->where('type', 'faculty')
                            ->first();


        $printImage = PrintImage::first();

        $data = [
            'functs' => Funct::all(),
            'approval' => $approval,
            'approval_reviewer' => $approval_reviewer,
            'approval_approver' => $approval_approver,
            'assess' => $assess,
            'assess_reviewer' => $assess_reviewer,
            'assess_approver' => $assess_approver,
            'duration' => $duration,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'user_id' => $user->id,
            'user' => $user,
            'title' => $request->title,
            'office' => $request->office,
            'printInfo' => $printInfo,
            'printImage' => $printImage,
        ];

        $pdf = PDF::loadView('print.ipcr-faculty', $data)->setPaper('a4','landscape');
        return $pdf->stream('ipcr-faculty.pdf', array("Attachment" => false));
    }
    
    public function standardFaculty($id, Request $request) {
        $faculty = false;
        $head = false;
        $yours = false;
        foreach (auth()->user()->account_types as $account_type) {
            if (str_contains(strtolower($account_type), 'faculty')) {
                $faculty = true;
                break;
            } 
        }
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
                break;
            }
        }
        if ($id == auth()->user()->id) {
            $yours = true;
        }

        if (($yours && $faculty) || $head || auth()->user()->id == 1) {
            
        } else {
            abort(403);
        }


        if (isset($request->duration_id)) {
            $duration = Duration::find($request->duration_id);
        } else {
            $duration = Duration::orderBy('id', 'DESC')->where('type', 'faculty')->where('start_date', '<=', date('Y-m-d'))->first();
        }
        $scoreEquivalent = ScoreEquivalent::first();

        if($duration) {
            $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $duration->id)->first();
        
            if (!$percentage) {
                $array = [
                    'core' => 0,
                    'strategic' => 0,
                    'support' => 0
                ];

                $percentage = (object) $array;
            }
        }

        $user = User::find($id);

        $printImage = PrintImage::first();

        $data = [
            'functs' => Funct::all(),
            'duration' => $duration,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'user_id' => $user->id,
            'user' => $user,
            'printImage' => $printImage
        ];

        $pdf = PDF::loadView('print.standard-faculty', $data)->setPaper('a4','landscape');
        return $pdf->stream('standard-faculty.pdf', array("Attachment" => false));
    }
    
    public function ipcrStaff($id, Request $request) {
        $staff = false;
        $head = false;
        $yours = false;
        $approval_reviewer = [];
        $assess_reviewer = [];
        foreach (auth()->user()->account_types as $account_type) {
            if (str_contains(strtolower($account_type), 'staff')) {
                $staff = true;
                break;
            } 
        }
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
                break;
            }
        }
        if ($id == auth()->user()->id) {
            $yours = true;
        }

        if (($yours && $staff) || $head || auth()->user()->id == 1) {
            
        } else {
            abort(403);
        }


        if (isset($request->duration_id)) {
            $duration = Duration::find($request->duration_id);
        } else {
            $duration = Duration::orderBy('id', 'DESC')->where('type', 'staff')->where('start_date', '<=', date('Y-m-d'))->first();
        }

        $scoreEquivalent = ScoreEquivalent::first();

        $user = User::find($id);

        if ($duration) {
            $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'staff')->where('user_id', $user->id)->where('duration_id', $duration->id)->first();

            if (!$percentage) {
                $array = [
                    'core' => 0,
                    'strategic' => 0,
                    'support' => 0
                ];

                $percentage = (object) $array;
            }
            $approval = $user->approvals()->orderBy('id', 'DESC')->where('name', 'approval')->where('type', 'ipcr')->where('duration_id', $duration->id)->where('user_type', 'staff')->first();
            
            $approval_approver = '';
            if ($approval) {
                foreach ($approval->reviewers as $reviewer) {
                    array_push($approval_reviewer, $reviewer);
                }
                $approval_approver = User::find($approval->approve_id);
            }

            $assess = $user->approvals()->orderBy('id', 'DESC')->where('name', 'assess')->where('type', 'ipcr')->where('duration_id', $duration->id)->where('user_type', 'staff')->first();
            
            $assess_approver = '';
            if ($assess) {
                foreach ($assess->reviewers as $reviewer) {
                    array_push($assess_reviewer, $reviewer);
                }
                $assess_approver = User::find($assess->approve_id);
            }
        }

        $printInfo = PrintInfo::where('user_id', $user->id)
                            ->where('duration_id', $duration->id)
                            ->where('type', 'staff')
                            ->first();

        $printImage = PrintImage::first();

        $data = [
            'functs' => Funct::all(),
            'approval' => $approval,
            'approval_reviewer' => $approval_reviewer,
            'approval_approver' => $approval_approver,
            'assess' => $assess,
            'assess_reviewer' => $assess_reviewer,
            'assess_approver' => $assess_approver,
            'duration' => $duration,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'user_id' => $user->id,
            'user' => $user,
            'title' => $request->title,
            'office' => $request->office,
            'printInfo' => $printInfo,
            'printImage' => $printImage
        ];

        $pdf = PDF::loadView('print.ipcr-staff', $data)->setPaper('a4','landscape');
        return $pdf->stream('ipcr-staff.pdf', array("Attachment" => false));
    }
    
    public function standardStaff($id, Request $request) {
        $staff = false;
        $head = false;
        $yours = false;
        foreach (auth()->user()->account_types as $account_type) {
            if (str_contains(strtolower($account_type), 'staff')) {
                $staff = true;
                break;
            } 
        }
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
                break;
            }
        }
        if ($id == auth()->user()->id) {
            $yours = true;
        }

        if (($yours && $staff) || $head || auth()->user()->id == 1) {
            
        } else {
            abort(403);
        }


        if (isset($request->duration_id)) {
            $duration = Duration::find($request->duration_id);
        } else {
            $duration = Duration::orderBy('id', 'DESC')->where('type', 'staff')->where('start_date', '<=', date('Y-m-d'))->first();
        }
        $scoreEquivalent = ScoreEquivalent::first();

        $user = User::find($id);
        
        if($duration) {
            $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'staff')->where('user_id', $user->id)->where('duration_id', $duration->id)->first();
        
            if (!$percentage) {
                $array = [
                    'core' => 0,
                    'strategic' => 0,
                    'support' => 0
                ];

                $percentage = (object) $array;
            }
        }


        $printImage = PrintImage::first();
        $data = [
            'functs' => Funct::all(),
            'duration' => $duration,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'user_id' => $user->id,
            'user' => $user,
            'printImage' => $printImage
        ];

        $pdf = PDF::loadView('print.standard-staff', $data)->setPaper('a4','landscape');
        return $pdf->stream('standard-staff.pdf', array("Attachment" => false));
    }

    public function opcr($id, Request $request) {
        $head = false;
        $yours = false;
        $approval_reviewer = [];
        $assess_reviewer = [];
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
                break;
            }
        }
        if ($id == auth()->user()->id) {
            $yours = true;
        }

        if ($yours || $head || auth()->user()->id == 1) {
        } else {
            abort(403);
        }

        
        if (isset($request->duration_id)) {
            $duration = Duration::find($request->duration_id);
        } else {
            $duration = Duration::orderBy('id', 'DESC')->where('type', 'office')->where('start_date', '<=', date('Y-m-d'))->first();
        }

        $scoreEquivalent = ScoreEquivalent::first();

        $user = User::find($id);

        if ($duration) {
            $percentage = Percentage::where('type', 'opcr')->where('user_type', 'office')->where('user_id', $user->id)->where('duration_id', $duration->id)->first();
            
            if (!$percentage) {
                $array = [
                    'core' => 0,
                    'strategic' => 0,
                    'support' => 0
                ];

                $percentage = (object) $array;
            }
            $approval = $user->approvals()->orderBy('id', 'DESC')->where('name', 'approval')->where('type', 'opcr')->where('duration_id', $duration->id)->where('user_type', 'office')->first();
            
            $approval_approver = '';
            $pmtHead = Pmt::where('isHead', 1)->pluck('id')->first();
            if ($approval) {
                foreach ($approval->reviewers()->where('id', $pmtHead)->get() as $reviewer) {
                    array_push($approval_reviewer, $reviewer);
                }
                $approval_approver = User::find($approval->approve_id);
            }

            $assess = $user->approvals()->orderBy('id', 'DESC')->where('name', 'assess')->where('type', 'opcr')->where('duration_id', $duration->id)->where('user_type', 'office')->first();
            
            $assess_approver = '';
            if ($assess) {
                foreach ($assess->reviewers()->where('id', $pmtHead)->get() as $reviewer) {
                    array_push($assess_reviewer, $reviewer);
                }
                $assess_approver = User::find($assess->approve_id);
            }
        }

        $printInfo = PrintInfo::where('user_id', $user->id)
                            ->where('duration_id', $duration->id)
                            ->where('type', 'office')
                            ->first();

        $printImage = PrintImage::first();

        $data = [
            'functs' => Funct::all(),
            'approval' => $approval,
            'approval_reviewer' => $approval_reviewer,
            'approval_approver' => $approval_approver,
            'assess' => $assess,
            'assess_reviewer' => $assess_reviewer,
            'assess_approver' => $assess_approver,
            'duration' => $duration,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'user_id' => $user->id,
            'user' => $user,
            'title' => $request->title,
            'office' => $request->office,
            'printInfo' => $printInfo,
            'pmtHead' => $pmtHead,
            'printImage' => $printImage
        ];

        $pdf = PDF::loadView('print.opcr', $data)->setPaper('a4','landscape');
        return $pdf->stream('opcr.pdf', array("Attachment" => false));
    }
    
    public function standardOpcr($id, Request $request) {
        $head = false;
        $yours = false;
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
                break;
            }
        }
        if ($id == auth()->user()->id) {
            $yours = true;
        }

        if ($yours || $head || auth()->user()->id == 1) {
        } else {
            abort(403);
        }


        if (isset($request->duration_id)) {
            $duration = Duration::find($request->duration_id);
        } else {
            $duration = Duration::orderBy('id', 'DESC')->where('type', 'office')->where('start_date', '<=', date('Y-m-d'))->first();
        }
        $scoreEquivalent = ScoreEquivalent::first();

        $user = User::find($id);

        if($duration) {
            $percentage = Percentage::where('type', 'opcr')->where('user_type', 'office')->where('user_id', $user->id)->where('duration_id', $duration->id)->first();
            
            if (!$percentage) {
                $array = [
                    'core' => 0,
                    'strategic' => 0,
                    'support' => 0
                ];

                $percentage = (object) $array;
            }
        }

        $printImage = PrintImage::first();

        $data = [
            'functs' => Funct::all(),
            'duration' => $duration,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'user_id' => $user->id,
            'user' => $user,
            'printImage' => $printImage
        ];

        $pdf = PDF::loadView('print.standard-opcr', $data)->setPaper('a4','landscape');
        return $pdf->stream('standard-opcr.pdf', array("Attachment" => false));
    }

    public function ttma() {
        $head = false;
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
                break;
            }
        }
        if ($head || auth()->user()->id == 1) {
            
        } else {
            abort(403);
        }


        $duration = Duration::orderBy('id', 'DESC')->where('type', 'office')->where('start_date', '<=', date('Y-m-d'))->first();
        $ttmas = Ttma::where('duration_id', $duration->id)->where('head_id', auth()->user()->id)->get();

        $printImage = PrintImage::first();
        $data = [
            'ttmas' => $ttmas,
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.ttma', $data)->setPaper('a4');
        return $pdf->stream('ttma.pdf', array("Attachment" => false));
    }

    public function listingPerOffice($office_id) {
        $head = false;
        $office = false;
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
            }
            if ($office->id == $office_id) {
                $office = true;
            }
        }

        if (($head && $office) || auth()->user()->id == 1) {
        } else {
            abort(403);
        }


        $durationS = Duration::orderBy('id', 'DESC')->where('type', 'staff')->where('start_date', '<=', date('Y-m-d'))->first();
        $durationF = Duration::orderBy('id', 'DESC')->where('type', 'faculty')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $durationF->id)->first();

        $users = User::query();

        $users->orwhereHas('offices', function(\Illuminate\Database\Eloquent\Builder $query) use ($office_id) {
            return $query->where('id', $office_id);
        });
        
        $users = $users->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'durationS' => $durationS,
            'durationF' => $durationF,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'offices' => Office::where('id', $office_id)->get(),
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.listings', $data)->setPaper('a4');
        return $pdf->stream('listings.pdf', array("Attachment" => false));
    }

    public function listingFaculty() {
        $durationF = Duration::orderBy('id', 'DESC')->where('type', 'faculty')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $durationF->id)->first();

        $users = User::query();

        $users->whereHas('account_types', function(\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where('account_type', 'LIKE', '%faculty%');
        });
        
        $users = $users->orderBy('name', 'ASC')->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'durationF' => $durationF,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'offices' => Office::where('office_name', 'like', '%dean%')->get(),
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.listings', $data)->setPaper('a4');
        return $pdf->stream('listings.pdf', array("Attachment" => false));
    }

    public function listingStaff() {
        $pmo = false;
        foreach (auth()->user()->offices as $office) {
            if (str_contains(strtolower($office->office_name), 'planning')) {
                $pmo = true;
            }
        }

        if ($pmo || auth()->user()->id == 1) {
        } else {
            abort(403);
        }


        $durationS = Duration::orderBy('id', 'DESC')->where('type', 'staff')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentages = Percentage::where('type', 'ipcr')->where('user_type', 'staff')->where('duration_id', $durationS->id)->get();

        $users = User::query();

        $users->whereHas('account_types', function(\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where('account_type', 'LIKE', '%staff%');
        });
        
        $users = $users->orderBy('name', 'ASC')->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'durationS' => $durationS,
            'percentages' => $percentages,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'offices' => Office::wherenot('office_name', 'like', '%dean%')->wherenot('id', 1)->get(),
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.listings', $data)->setPaper('a4');
        return $pdf->stream('listings.pdf', array("Attachment" => false));
    }

    public function listingOpcr() {
        $pmo = false;
        foreach (auth()->user()->offices as $office) {
            if (str_contains(strtolower($office->office_name), 'planning')) {
                $pmo = true;
            }
        }
        if ($pmo || auth()->user()->id == 1) {
        } else {
            abort(403);
        }


        $duration = Duration::orderBy('id', 'DESC')->where('type', 'office')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentage = Percentage::where('type', 'opcr')->where('user_type', 'office')->where('user_id', null)->where('duration_id', $duration->id)->first();
        $sub_percentages = SubPercentage::where('type', 'opcr')->where('user_type', 'office')->where('user_id', null)->where('duration_id', $duration->id)->get();

        $users = User::query();

        $users->whereHas('offices', function(\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where('isHead', true);
        });
        
        $users = $users->orderBy('name', 'ASC')->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'duration' => $duration,
            'percentage' => $percentage,
            'sub_percentages' => $sub_percentages,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.listings-opcr', $data)->setPaper('a4');
        return $pdf->stream('listings.pdf', array("Attachment" => false));
    }

    public function rankingPerOffice($office_id) {
        $head = false;
        $office = false;
        foreach (auth()->user()->offices as $office) {
            if ($office->pivot->isHead) {
                $head = true;
            }
            if ($office->id == $office_id) {
                $office = true;
            }
        }

        if (($head && $office) || auth()->user()->id == 1) {
        } else {
            abort(403);
        }


        $durationS = Duration::orderBy('id', 'DESC')->where('type', 'staff')->where('start_date', '<=', date('Y-m-d'))->first();
        $durationF = Duration::orderBy('id', 'DESC')->where('type', 'faculty')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $durationF->id)->first();

        $users = User::query();

        $users->orwhereHas('offices', function(\Illuminate\Database\Eloquent\Builder $query) use ($office_id) {
            return $query->where('id', $office_id);
        });
        
        $users = $users->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'durationS' => $durationS,
            'durationF' => $durationF,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'offices' => Office::where('id', $office_id)->get(),
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.rankings', $data)->setPaper('a4');
        return $pdf->stream('rankings.pdf', array("Attachment" => false));
    }

    public function rankingFaculty() {
        $durationF = Duration::orderBy('id', 'DESC')->where('type', 'faculty')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentage = Percentage::where('type', 'ipcr')->where('user_type', 'faculty')->where('user_id', null)->where('duration_id', $durationF->id)->first();

        $users = User::query();

        $users->whereHas('account_types', function(\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where('account_type', 'LIKE', '%faculty%');
        });
        
        $users = $users->orderBy('name', 'ASC')->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'durationF' => $durationF,
            'percentage' => $percentage,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'offices' => Office::where('office_name', 'like', '%dean%')->get(),
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.rankings', $data)->setPaper('a4');
        return $pdf->stream('rankings.pdf', array("Attachment" => false));
    }

    public function rankingStaff() {
        $pmo = false;
        foreach (auth()->user()->offices as $office) {
            if (str_contains(strtolower($office->office_name), 'planning')) {
                $pmo = true;
            }
        }

        if ($pmo || auth()->user()->id == 1) {
        } else {
            abort(403);
        }


        $durationS = Duration::orderBy('id', 'DESC')->where('type', 'staff')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentages = Percentage::where('type', 'ipcr')->where('user_type', 'staff')->where('duration_id', $durationS->id)->get();

        $users = User::query();

        $users->whereHas('account_types', function(\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where('account_type', 'LIKE', '%staff%');
        });
        
        $users = $users->orderBy('name', 'ASC')->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'durationS' => $durationS,
            'percentages' => $percentages,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'offices' => Office::wherenot('office_name', 'like', '%dean%')->wherenot('id', 1)->get(),
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.rankings', $data)->setPaper('a4');
        return $pdf->stream('rankings.pdf', array("Attachment" => false));
    }

    public function rankingOpcr() {
        $pmo = false;
        foreach (auth()->user()->offices as $office) {
            if (str_contains(strtolower($office->office_name), 'planning')) {
                $pmo = true;
            }
        }
        if ($pmo || auth()->user()->id == 1) {
        } else {
            abort(403);
        }


        $duration = Duration::orderBy('id', 'DESC')->where('type', 'office')->where('start_date', '<=', date('Y-m-d'))->first();
        $percentage = Percentage::where('type', 'opcr')->where('user_type', 'office')->where('user_id', null)->where('duration_id', $duration->id)->first();
        $sub_percentages = SubPercentage::where('type', 'opcr')->where('user_type', 'office')->where('user_id', null)->where('duration_id', $duration->id)->get();

        $users = User::query();

        $users->whereHas('offices', function(\Illuminate\Database\Eloquent\Builder $query) {
            return $query->where('isHead', true);
        });
        
        $users = $users->orderBy('name', 'ASC')->distinct()->get();

        $functs = Funct::all();
        $scoreEquivalent = ScoreEquivalent::first();

        $printImage = PrintImage::first();
        $data = [
            'functs' => $functs,    
            'duration' => $duration,
            'percentage' => $percentage,
            'sub_percentages' => $sub_percentages,
            'scoreEquivalent' => $scoreEquivalent,
            'users' => $users,
            'printImage' => $printImage
        ];

        
        $pdf = PDF::loadView('print.rankings-opcr', $data)->setPaper('a4');
        return $pdf->stream('rankings.pdf', array("Attachment" => false));
    }
}
