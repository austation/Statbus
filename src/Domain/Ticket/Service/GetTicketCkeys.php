<?php

namespace App\Domain\Ticket\Service;

class GetTicketCkeys
{
    public static function find(array $tickets): array
    {
        $ckeys = [];
        foreach($tickets as $t) {
            if($t->getSenderBadge()) {
                $ckeys[] = $t->getSenderBadge()->getCkey();
            }
            if($t->getRecipientBadge()) {
                $ckeys[] = $t->getRecipientBadge()->getCkey();
            }
        }
        return array_unique($ckeys);
    }

    public static function isCkeyInTicket(array $tickets, string $ckey): bool
    {
        $ckeys = self::find($tickets);
        if(in_array($ckey, $ckeys)) {
            return true;
        }
        return false;
    }

}
