<?php

namespace App\Enum;

enum Jobs: string
{
    case AI = 'AI';
    case ASSISTANT = 'Assistant';
    case ATMOS_TECH = 'Atmospheric Technician';
    case BARTENDER = 'Bartender';
    case BITRUNNER = 'Bitrunner';
    case BOTANIST = 'Botanist';
    case CAPTAIN = 'Captain';
    case CARGO_TECH = 'Cargo Technician';
    case CHAPLAIN = 'Chaplain';
    case CHEMIST = 'Chemist';
    case CHIEF_ENGIE = 'Chief Engineer';
    case CMO = 'Chief Medical Officer';
    case CLOWN = 'Clown';
    case COOK = 'Cook';
    case CORONER = 'Coroner';
    case CURATOR = 'Curator';
    case CYBORG = 'Cyborg';
    case DETECTIVE = 'Detective';
    case GENETICIST = 'Geneticist';
    case HOP = 'Head of Personnel';
    case HOS = 'Head of Security';
    case JANITOR = 'Janitor';
    case LAWYER = 'Lawyer';
    case LIBRARIAN = 'Librarian';
    case DOCTOR = 'Medical Doctor';
    case MIME = 'Mime';
    case PARAMEDIC = 'Paramedic';
    case PSYCHOLOGIST = 'Psychologist';
    case QM = 'Quartermaster';
    case RD = 'Research Director';
    case ROBOTICIST = 'Roboticist';
    case SCIENTIST = 'Scientist';
    case SECURITY = 'Security Officer';
    case MINER = 'Shaft Miner';
    case ENGINEER = 'Station Engineer';
    case VIROLOGIST = 'Virologist';
    case WARDEN = 'Warden';
    case LIVING = 'Living';
    case GHOST = 'Ghost';
    case ADMIN = 'Admin';

    public function getColor(): string
    {
        return match($this) {
            default => '#6E6E6E', //Jobs::ASSISTANT
            Jobs::AI, Jobs::CYBORG => '#1B4594',
            Jobs::ATMOS_TECH, Jobs::ENGINEER => '#FFA62B',
            Jobs::BITRUNNER, Jobs::MINER, Jobs::CARGO_TECH => '#B18644',
            Jobs::BARTENDER, Jobs::BOTANIST, Jobs::CHAPLAIN, Jobs::CLOWN, Jobs::COOK, Jobs::CURATOR, Jobs::JANITOR, Jobs::LAWYER, Jobs::LIBRARIAN,  Jobs::MIME, Jobs::PSYCHOLOGIST => '#58C800',
            Jobs::CHEMIST, Jobs::CORONER, Jobs::PARAMEDIC, Jobs::DOCTOR, Jobs::VIROLOGIST => '#5B97BC',
            Jobs::GENETICIST, Jobs::SCIENTIST, Jobs::ROBOTICIST => '#C96DBF',
            Jobs::SECURITY, Jobs::DETECTIVE, Jobs::WARDEN => '#CB0000',
            Jobs::CAPTAIN, Jobs::CHIEF_ENGIE, Jobs::CMO, Jobs::RD, Jobs::HOP, Jobs::QM, Jobs::HOS, => '#1B67A5',
            Jobs::ADMIN => '#df0afb', //COLOR_CARP_GRAPE
            Jobs::LIVING => '#AAA', //COLOR_CARP_RUSTY
            Jobs::GHOST => '#000' //COLOR_CARP_RED
        };
    }

    public function includeInGraph(): bool
    {
        return match($this) {
            default => true,
            Jobs::LIVING, Jobs::ADMIN, Jobs::GHOST => false
        };
    }
}
