<?php

namespace App\Enum;

use App\Service\LuminosityContrast;

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
    case PRISONER = 'Prisoner';
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

    //Special roles for timekeeping
    case LIVING = 'Living';
    case GHOST = 'Ghost';
    case ADMIN = 'Admin';

    //Baddies
    case ABDUCTOR = 'Abductor';
    case XENOMORPH = 'Xenomorph';
    case BLOB = 'Blob';
    case BLOOD_BROTHER = 'Blood Brother';
    case CHANGELING = 'Changeling';
    case CULTIST = 'Cultist';
    case INTERNAL_AFFAIRS_AGENT = 'Internal Affairs Agent';
    case MALF = 'Malf AI';
    case MONKEY = 'Monkey';
    case SPACE_NINJA = 'Space Ninja';
    case OPERATIVE = 'Operative';
    case SYNDICATE_MUTINEER = 'Syndicate Mutineer';
    case REVOLUTIONARY = 'Revolutionary';
    case REVENANT = 'Revenant';
    case HEAD_REVOLUTIONARY = 'Head Revolutionary';
    case SYNDICATE = 'Syndicate';
    case TRAITOR = 'Traitor';
    case WIZARD = 'Wizard';
    case HIVEMIND_HOST = 'Hivemind Host';
    case HERETIC = 'Heretic';
    case NIGHTMARE = 'Nightmare';

    public function getColor(): string
    {
        return match($this) {
            default => '#6E6E6E', //Jobs::ASSISTANT
            Jobs::AI, Jobs::CYBORG => '#000',
            Jobs::ATMOS_TECH, Jobs::ENGINEER => '#FFA62B',
            Jobs::BITRUNNER, Jobs::MINER, Jobs::CARGO_TECH => '#B18644',
            Jobs::BARTENDER, Jobs::BOTANIST, Jobs::CHAPLAIN, Jobs::CLOWN, Jobs::COOK, Jobs::CURATOR, Jobs::JANITOR, Jobs::LAWYER, Jobs::LIBRARIAN,  Jobs::MIME, Jobs::PSYCHOLOGIST => '#58C800',
            Jobs::CHEMIST, Jobs::CORONER, Jobs::PARAMEDIC, Jobs::DOCTOR, Jobs::VIROLOGIST => '#5B97BC',
            Jobs::GENETICIST, Jobs::SCIENTIST, Jobs::ROBOTICIST => '#C96DBF',
            Jobs::SECURITY, Jobs::DETECTIVE, Jobs::WARDEN => '#CB0000',
            Jobs::CAPTAIN, Jobs::CHIEF_ENGIE, Jobs::CMO, Jobs::RD, Jobs::HOP, Jobs::QM, Jobs::HOS, => '#1B67A5',
            Jobs::ADMIN => '#df0afb',
            Jobs::LIVING => '#AAA',
            Jobs::GHOST => '#000',
            Jobs::PRISONER => '#FF9900',
            Jobs::ABDUCTOR, Jobs::XENOMORPH, Jobs::BLOB, Jobs::BLOOD_BROTHER, Jobs::CHANGELING, Jobs::CULTIST, Jobs::INTERNAL_AFFAIRS_AGENT, Jobs::MALF, Jobs::MONKEY, Jobs::SPACE_NINJA, Jobs::OPERATIVE, Jobs::SYNDICATE_MUTINEER, Jobs::REVOLUTIONARY, Jobs::REVENANT, Jobs::HEAD_REVOLUTIONARY, Jobs::SYNDICATE, Jobs::TRAITOR, Jobs::WIZARD, Jobs::HIVEMIND_HOST, Jobs::HERETIC, Jobs::NIGHTMARE => '#830000'
        };
    }

    public function getForeColor(): string
    {
        return match($this) {
            default => LuminosityContrast::getContrastColor($this->getColor()),
            Jobs::AI, Jobs::CYBORG => '#00FF00'
        };
    }

    public function includeInGraph(): bool
    {
        return match($this) {
            default => true,
            Jobs::LIVING, Jobs::ADMIN, Jobs::GHOST,Jobs::ABDUCTOR, Jobs::XENOMORPH, Jobs::BLOB, Jobs::BLOOD_BROTHER, Jobs::CHANGELING, Jobs::CULTIST, Jobs::INTERNAL_AFFAIRS_AGENT, Jobs::MALF, Jobs::MONKEY, Jobs::SPACE_NINJA, Jobs::OPERATIVE, Jobs::SYNDICATE_MUTINEER, Jobs::REVOLUTIONARY, Jobs::REVENANT, Jobs::HEAD_REVOLUTIONARY, Jobs::SYNDICATE, Jobs::TRAITOR, Jobs::WIZARD, Jobs::HIVEMIND_HOST, Jobs::HERETIC, Jobs::NIGHTMARE => false
        };
    }
}
