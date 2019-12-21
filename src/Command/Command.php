<?php

/*
 * This file is part of the Active Collab Bootstrap project.
 *
 * (c) A51 doo <info@activecollab.com>. All rights reserved.
 */

declare(strict_types=1);

namespace ActiveCollab\Bootstrap\Command;

use ActiveCollab\ContainerAccess\ContainerAccessInterface\Implementation as ContainerAccessInterfaceImplementation;
use Doctrine\Common\Inflector\Inflector;
use Exception;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends BaseCommand implements CommandInterface
{
    use ContainerAccessInterfaceImplementation;

    protected function configure()
    {
        parent::configure();

        $bits = explode('\\', get_class($this));

        $last_bit = Inflector::tableize(array_pop($bits));
        $last_bit_len = strlen($last_bit);

        if (substr($last_bit, $last_bit_len - 8) == '_command') {
            $last_bit = substr($last_bit, 0, $last_bit_len - 8);
        }

        $this
            ->setName($this->getCommandNamePrefix() . $last_bit)
            ->addOption('debug', '', InputOption::VALUE_NONE, 'Output debug details')
            ->addOption('json', '', InputOption::VALUE_NONE, 'Output JSON');
    }

    public function getCommandNamePrefix(): string
    {
        return '';
    }

    /**
     * Abort due to error.
     *
     * @param  string          $message
     * @param  int             $error_code
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function abort($message, $error_code, InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('json')) {
            $output->writeln(
                json_encode(
                    [
                        'ok' => false,
                        'error_message' => $message,
                        'error_code' => $error_code,
                    ]
                )
            );
        } else {
            $output->writeln("<error>Error #{$error_code}:</error> " . $message);
        }

        return $error_code < 1 ? 1 : $error_code;
    }

    /**
     * Show success message.
     *
     * @param  string          $message
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function success($message, InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('json')) {
            $output->writeln(json_encode([
                'ok' => true,
                'message' => $message,
            ]));
        } else {
            $output->writeln('<info>OK:</info> ' . $message);
        }

        return 0;
    }

    /**
     * Abort due to an exception.
     *
     * @param  Exception       $e
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return int
     */
    protected function abortDueToException(Exception $e, InputInterface $input, OutputInterface $output)
    {
        $message = $e->getMessage();
        $code = $this->exceptionToErrorCode($e);

        if ($input->getOption('json')) {
            $response = [
                'ok' => false,
                'error_message' => $message,
                'error_code' => $code,
            ];

            if ($input->getOption('debug')) {
                $response['error_class'] = get_class($e);
                $response['error_file'] = $e->getFile();
                $response['error_line'] = $e->getLine();
                $response['error_trace'] = $e->getTraceAsString();
            }

            $output->writeln(json_encode($response));
        } else {
            $output->writeln('');

            if ($input->getOption('debug') || $output->getVerbosity()) {
                $output->writeln("<error>Error #{$code}:</error> <" . get_class($e) . '> ' . $message . ', in file ' . $e->getFile() . ' on line ' . $e->getLine());
                $output->writeln('');
                $output->writeln('Backtrace');
                $output->writeln('');
                $output->writeln($e->getTraceAsString());
            } else {
                $output->writeln("<error>Error #{$code}:</error> " . $message);
            }
        }

        return $code;
    }

    /**
     * Get command error code from exception.
     *
     * @param  Exception $e
     * @return int
     */
    protected function exceptionToErrorCode(Exception $e)
    {
        return $e->getCode() ? $e->getCode() : 1;
    }
}
