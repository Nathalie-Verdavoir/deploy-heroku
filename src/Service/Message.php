<?php

namespace Nat\DeployBundle\Service;

class Message
{
    public function getColoredMessage(string|array $message, string $color)
    {
        $lignes = [];
        
            $lignes[] = '';
            $lignes[] = '<bg='. $color .'>  ============================================================================  ';
            if( is_array($message) ) {
                foreach($message as $mes){
                    $separator = str_repeat(' ',71-strlen($mes));
                    $lignes[] = '  |   ' . $mes . $separator . '|  ';
                }
            }else{
                $separator = str_repeat(' ',71-strlen($message));
                $lignes[] = '  |   ' . $message . $separator . '|  ';
            }
            $lignes[] = '  ======================================================================<bg=bright-magenta>by Nat</>  </>';
            $lignes[] = '';
        $this->output->writeln($lignes);
    }
}