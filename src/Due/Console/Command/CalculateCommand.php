<?php

namespace Shakahl\Due\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Shakahl\Due\DueDateCalculator;

/**
 * Dummy command controller
 */
class CalculateCommand extends Command
{
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('calculate')
            ->setDescription('Calculates due date')
            ->addArgument(
                'date',
                InputArgument::REQUIRED,
                'Issue date'
            )
            ->addArgument(
                'hours',
                InputArgument::REQUIRED,
                'Turnaround time in working hours'
            )
        ;
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $date = $input->getArgument('date');
            $hours = $input->getArgument('hours');

            if (!is_numeric($hours)) {
                $this->error($output, 'Turnaround time must be a number.');
            }

            $calculator = new DueDateCalculator();

            $due = $calculator($input->getArgument('date'),$hours);

            $result = [
                'date' => date('c', strtotime($date)),
                'turnaround_hours' => (int)$hours,
                'due_date' => $due ? $due->format('c') : $due
            ];

            $output->writeln(json_encode($result, JSON_PRETTY_PRINT));

        } catch (\InvalidDateTimeFormatException $e) {
            $this->error($output, 'Invalid date format: ' . $date);
        } catch (\DueDateCalculatorException $e) {
            $this->error($output, $e->getMessage());
        }

        exit(0);
    }

    /**
     * @return DueDateCalculator
     */
    protected function createCalculator()
    {
        return new DueDateCalculator();
    }

    /**
     * Shows an error message and exit.
     * @param  OutputInterface $output
     * @param  string $msg
     * @return void
     */
    protected function error(OutputInterface $output, string $msg)
    {
        $output->writeln('ERROR. ' . $msg);
        exit(1);
    }
}
