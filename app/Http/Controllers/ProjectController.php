<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct(Project $model) {
        $this->model = $model;
    }

    public function getMunicipalities() {
        return $this->model->orderBy('municipality')->groupBy('municipality')->pluck('municipality');
    }

    public function generateBarangays(Request $request) {
        return $this->model
                    ->where('municipality', $request->municipality)
                    ->orderBy('barangay')
                    ->groupBy('barangay')
                    ->pluck('barangay');
    }

    public function generateData(Request $request) {
        $data = $this->model
                    ->where('municipality', $request->municipality);
                    
        
        if ($request->barangay != "") {
            $data = $data->where('barangay', $request->barangay);
        }
                    
        $data = $data->orderBy('municipality')
                ->orderBy('barangay')
                ->get();

        return $data;
    }

    public function generateChart(Request $request) {
        return $this->model
                    ->selectRaw(
                        'SUM(amount_approp) as total,
                         municipality')
                    ->groupBy('municipality')
                    ->get();
    }
}
