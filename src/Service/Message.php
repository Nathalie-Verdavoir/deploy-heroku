<?php

namespace Nat\DeployBundle\Service;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Message
{
    //singleton

    private $input;
    private $output;
    private static $_instance;

    //to call the unique instance of this class form anywhere else you have to use : Message::getInstance();

    public static function getInstance(InputInterface $input, OutputInterface $output)
    {
        if (is_null(self::$_instance)) {
            return self::$_instance = new Message($input, $output);
        }
        return self::$_instance;
    }

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    public function getColoredMessage(string|array $message, string $color)
    {
        $lignes = [];

        $lignes[] = '';
        $lignes[] = '<bg=' . $color . '>  ============================================================================  ';
        if (is_array($message)) {
            foreach ($message as $mes) {
                $separator = str_repeat(' ', 71 - strlen($mes));
                $lignes[] = '  |   ' . $mes . $separator . '|  ';
            }
        } else {
            $separator = str_repeat(' ', 71 - strlen($message));
            $lignes[] = '  |   ' . $message . $separator . '|  ';
        }
        $lignes[] = '  ======================================================================<bg=bright-magenta>by Nat</>  </>';
        $lignes[] = '';
        $this->output->writeln($lignes);
    }
}
