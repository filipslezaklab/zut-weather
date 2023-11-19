<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:location',
    description: 'Add a short description for your command',
)]
class WeatherLocationCommand extends Command
{
    private $measurementRepo;
    private $locationRepo;
    public function __construct(MeasurementRepository $measurement, LocationRepository $location)
    {
        parent::__construct();

        $this->measurementRepo = $measurement;
        $this->locationRepo = $location;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('locationId', InputArgument::REQUIRED, 'Location entity ID as int')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $locationId = (int)$input->getArgument('locationId');
        $location = $this->locationRepo->find($locationId);
        if(!$location) {
            $io->error("Location not found");
            return Command::FAILURE;
        }
        $measurements = $this->measurementRepo->findByLocation($location);
        if(empty($measurements)) {
            $io->success("No records for that location");
            return Command::SUCCESS;
        }
        $io->success("Location: " . $location->getCity());
        foreach ($measurements as $measurement) {
            $date = $measurement->getDate();
            $temperature = $measurement->getTemperature();
            $io->success($date->format("Y-m-d H:i:s" . " " . $temperature . " C"));
        }
        return Command::SUCCESS;
    }
}
