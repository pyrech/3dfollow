<?php

namespace App\Data;

use App\Entity\Filament;
use App\Entity\PrintObject;
use App\Entity\PrintRequest;
use App\Entity\User;
use Vich\UploaderBundle\Storage\StorageInterface;

class Exporter
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function export(User $user): string
    {
        $date = new \DateTime();
        $name = sprintf('%s/3dfollow-export-%s-%s.zip', sys_get_temp_dir(), $user->getUsername(), $date->format('Ymd-His'));

        $archive = $this->buildArchive($name);

        $this->exportAccount($user, $archive);
        $this->exportFilament($user, $archive);
        $this->exportPrintObjects($user, $archive);
        $this->exportTeam($user, $archive);
        $this->exportPrintRequests($user, $archive);

        $archive->close();

        return $name;
    }

    private function buildArchive(string $name): \ZipArchive
    {
        $archive = new \ZipArchive();

        if (true !== $archive->open($name, \ZipArchive::CREATE)) {
            throw new \RuntimeException('Could not generate archive.');
        }

        $archive->addFromString('README.txt', 'This archive contains all your data from 3D Follow and was generated at ' . (new \DateTime())->format('Y-m-d H:i:s'));

        return $archive;
    }

    /**
     * @see https://stackoverflow.com/a/30533173/1917092
     *
     * @param array<string,mixed>[] $data
     */
    private function addCsvToArchive(\ZipArchive $archive, string $filename, array $data): void
    {
        if (!$data) {
            return;
        }

        // Let use a threshold of 1 MB (1024 * 1024)
        $fd = fopen('php://temp/maxmemory:1048576', 'w');
        if (false === $fd) {
            throw new \RuntimeException('Fail to fopen csv');
        }

        fputcsv($fd, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($fd, $row);
        }

        rewind($fd);
        $csv = stream_get_contents($fd);
        fclose($fd); // releases the memory (or tempfile)

        if ($csv) {
            $archive->addFromString($filename, $csv);
        }
    }

    private function exportAccount(User $user, \ZipArchive $archive): void
    {
        $this->addCsvToArchive($archive, 'account.csv', [
            [
                'username' => $user->getUsername(),
                'has 3D printer' => $user->getIsPrinter() ? 'yes' : 'no',
                'registered at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    private function exportFilament(User $user, \ZipArchive $archive): void
    {
        $this->addCsvToArchive($archive, 'filaments.csv', $user->getFilaments()->map(function (Filament $filament) {
            return [
                'filament' => $filament->getName(),
                'weight (g)' => $filament->getWeight(),
                'price' => $filament->getPrice(),
                'density (g/cm³)' => $filament->getDensity(),
                'diameter (mm)' => $filament->getDiameter(),
                'weight used outside 3dfollow (g)' => $filament->getWeightUsed(),
                'pourcentage used' => $filament->computeUsagePercentage(),
                'comment' => $filament->getComment(),
            ];
        })->toArray());
    }

    private function exportPrintObjects(User $user, \ZipArchive $archive): void
    {
        $storage = $this->storage;

        $this->addCsvToArchive($archive, 'prints.csv', $user->getPrintObjects()->map(function (PrintObject $printObject) use ($archive, $storage) {
            $filament = $printObject->getFilament();
            $printedAt = $printObject->getPrintedAt();
            $gcode = $printObject->getGCode();
            $gcodeFilename = '';

            if ($gcode && $gcode->getName()) {
                $path = $storage->resolvePath($printObject, 'gCodeFile');

                if ($path) {
                    $gcodeFilename = 'uploads/' . $gcode->getName();
                    $archive->addFile($path, $gcodeFilename);
                }
            }

            return [
                'name' => $printObject->getName(),
                'filament' => $filament ? $filament->getName() : '',
                'gcode file' => $gcodeFilename,
                'quantity' => $printObject->getQuantity(),
                'weight (g)' => $printObject->getWeight(),
                'length (m)' => $printObject->getLength(),
                'cost' => $printObject->getCost(),
                'print date' => $printedAt ? $printedAt->format('Y-m-d H:i:s') : '',
            ];
        })->toArray());
    }

    private function exportTeam(User $user, \ZipArchive $archive): void
    {
        $team = $user->getTeamCreated();

        if (!$team) {
            return;
        }

        $this->addCsvToArchive($archive, 'group.csv', $team->getMembers()->map(function (User $user) {
            return [
                'member username' => $user->getUsername(),
                'print requests count' => $user->getPrintRequests()->count(),
            ];
        })->toArray());
    }

    private function exportPrintRequests(User $user, \ZipArchive $archive): void
    {
        $this->addCsvToArchive($archive, 'requests.csv', $user->getPrintRequests()->map(function (PrintRequest $printRequest) {
            $createdAt = $printRequest->getCreatedAt();

            return [
                'name' => $printRequest->getName(),
                'link' => $printRequest->getLink(),
                'quantity' => $printRequest->getQuantity(),
                'prints' => implode(', ', $printRequest->getPrintObjects()->map(function (PrintObject $printObject) {
                    return $printObject->getName();
                })->toArray()),
                'is printed' => $printRequest->getIsPrinted() ? 'yes' : 'no',
                'request date' => $createdAt ? $createdAt->format('Y-m-d H:i:s') : '',
                'comment' => $printRequest->getComment(),
            ];
        })->toArray());
    }
}
