<?php

namespace App\Command;

use App\Repository\LocationRepository;
use App\Repository\MeasurementRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Add a short description for your command',
)]
class WeatherCityCommand extends Command
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
            ->addArgument('city', InputArgument::REQUIRED, 'Location city')
            ->addArgument( 'country', InputArgument::OPTIONAL, 'Location country code')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $city = (string)$input->getArgument('city');
        $country = (string)$input->getArgument('country');

        $location = $this->locationRepo->findOneByCity($city, $country);
        if(!$location) {
            $io->error("Location not found");
            return Command::FAILURE;
        }
        $measurements = $this->measurementRepo->findByLocation($location);
        if(empty($measurements)) {
            $io->success("No records for that location");
            return Command::SUCCESS;
        }
        $res = "Location: " . $location->getCity();
        foreach ($measurements as $measurement) {
            $date = $measurement->getDate();
            $temperature = $measurement->getTemperature();
            $res = $res . $date->format("Y-m-d H:i:s" . " " . $temperature . " C");
            $io->success($res);
        }
        return Command::SUCCESS;
    }
}
