<?php

namespace Nat\DeployBundle\Service;

class Message
{
    //singleton

    private $input;
    private $output;
    private static $_instance;

    //to call the unique instance of this class form anywhere else you have to use : Message::getInstance();

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            return self::$_instance = new Message();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        $infos = NatInfos::getInstance();
        $this->input = $infos->input;
        $this->output = $infos->output;
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
