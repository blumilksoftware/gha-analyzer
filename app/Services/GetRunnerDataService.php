<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class GetRunnerDataService
{
    protected array $runnerType;

    public function getRunnerData(array $runnerLabels): array
    {
        $this->runnerType = $this->getMatchingLabelInfo($runnerLabels);
        $this->runnerType["multiplier"] = $this->getMultiplier($this->runnerType["os"]);
        $this->runnerType["pricing"] = $this->getPricing($this->runnerType["os"], $this->runnerType["cores"]);

        return $this->runnerType;
    }

    private function parseLabel(string $label): ?array
    {
        $pattern = "/^(?<os>[a-zA-Z0-9.-]+?)-(?<version>[a-zA-Z0-9.]+)(-(?<cores>[0-9]+)core)?$/";

        if (preg_match($pattern, $label, $matches)) {
            return [
                "os" => $matches["os"],
                "cores" => isset($matches["cores"]) ? $matches["cores"] : "standard",
            ];
        }

        return null;
    }

    private function getMatchingLabelInfo(array $runnerLabels): array
    {
        foreach ($runnerLabels as $label) {
            $runnerInfo = $this->parseLabel($label);

            if ($runnerInfo !== null) {
                return $runnerInfo;
            }
        }

        throw new InvalidArgumentException("None of the labels match the expected format.");
    }

    private function getPricing(string $os, string $cores): float
    {
        return config("services.runners.pricing." . $os . "." . $cores);
    }

    private function getMultiplier(string $os): int
    {
        return config("services.runners.multiplier." . $os);
    }
}
