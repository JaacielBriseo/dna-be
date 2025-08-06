<?php

namespace App\Services;

use App\Models\DnaRecord;
use InvalidArgumentException;

class DnaAnalyzerService
{
    protected array $dna;
    protected int $n;

    public function hasMutation(array $dna): bool
    {
        $this->validateDna($dna);
        $dnaString = implode('', $dna);
        $dnaHash = hash('sha256', $dnaString);

        $existing = DnaRecord::where('hash', $dnaHash)->first();
        if ($existing) {
            return $existing->has_mutation;
        }

        $this->dna = $dna;
        $this->n = count($dna);

        $sequenceCount = 0;

        for ($row = 0; $row < $this->n; $row++) {
            for ($col = 0; $col < $this->n; $col++) {
                if ($this->hasSequenceFrom($row, $col)) {
                    $sequenceCount++;
                    if ($sequenceCount >= 2) {
                        DnaRecord::create([
                            'dna_sequence' => json_encode($dna),
                            'hash' => $dnaHash,
                            'has_mutation' => true
                        ]);
                        return true;
                    }
                }
            }
        }

        DnaRecord::create([
            'dna_sequence' => json_encode($dna),
            'hash' => $dnaHash,
            'has_mutation' => false
        ]);

        return false;
    }

    protected function validateDna(array $dna): void
    {
        $n = count($dna);
        if ($n === 0) {
            throw new InvalidArgumentException('Secuencia inválida');
        }

        foreach ($dna as $row) {
            if (strlen($row) !== $n) {
                throw new InvalidArgumentException('Secuencia inválida');
            }

            if (!preg_match('/^[ATCG]+$/', $row)) {
                throw new InvalidArgumentException('Solo "A, T, C, G" son válidos.');
            }
        }
    }

    protected function hasSequenceFrom(int $row, int $col): bool
    {
        $directions = [
            [0, 1],
            [1, 0],
            [1, 1],
            [1, -1],
        ];

        foreach ($directions as [$dr, $dc]) {
            if ($this->checkSequence($row, $col, $dr, $dc)) {
                return true;
            }
        }

        return false;
    }

    protected function checkSequence(int $row, int $col, int $dr, int $dc): bool
    {
        $char = $this->dna[$row][$col];
        for ($i = 1; $i < 4; $i++) {
            $newRow = $row + $dr * $i;
            $newCol = $col + $dc * $i;

            if ($newRow < 0 || $newRow >= $this->n || $newCol < 0 || $newCol >= $this->n) {
                return false;
            }

            if ($this->dna[$newRow][$newCol] !== $char) {
                return false;
            }
        }

        return true;
    }
}
