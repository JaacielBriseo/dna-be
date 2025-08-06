<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\DnaRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\DnaAnalyzerService;
use Illuminate\Support\Facades\Cache;

class DnaRecordController extends Controller
{
    public function analyze(Request $request, DnaAnalyzerService $dnaAnalyzer)
    {
        try {
            $validated = $request->validate([
                'dna' => 'required|array',
                'dna.*' => 'required|string'
            ]);

            $hasMutation = $dnaAnalyzer->hasMutation($validated['dna']);

            return response(null, $hasMutation ? 200 : 403);
        } catch (\Throwable $th) {
            Log::info($th);
            return response(null, 403);
        }
    }

    public function stats()
    {
        try {
            $data = Cache::remember('dna_stats', 2, function () {
                $countMutations = DnaRecord::where('has_mutation', true)->count();
                $countNoMutations = DnaRecord::where('has_mutation', false)->count();

                $ratio = $countNoMutations > 0
                    ? $countMutations / $countNoMutations
                    : 0;

                return [
                    'count_mutations' => $countMutations,
                    'count_no_mutation' => $countNoMutations,
                    'ratio' => round($ratio, 2)
                ];
            });

            return response()->json($data);
        } catch (\Throwable $th) {
            Log::info($th);
            return response()->json([
                'count_mutations' => 0,
                'count_no_mutation' => 0,
                'ratio' => 0,
            ]);
        }
    }

    public function list()
    {
        try {
            $records = DnaRecord::latest()->take(10)->get()->map(fn($record) => [
                'dna' => json_decode($record->dna_sequence),
                'has_mutation' => $record->has_mutation,
                'created_at' => Carbon::parse($record->created_at)->setTimezone('America/Hermosillo')->format('d-m-Y H:i:s')
            ]);

            return response()->json([
                'data' => $records
            ]);
        } catch (\Throwable $th) {
            Log::info($th);
            return response()->json([
                'data' => []
            ]);
        }
    }
}
