<?php

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function getCharacterName($chartype)
{
    switch($chartype)
    {
        case -1: $name = "All"; break;
        case 0: $name = "Elesis"; break;
        case 1: $name = "Lire"; break;
        case 2: $name = "Arme"; break;
        case 3: $name = "Lass"; break;
        case 4: $name = "Ryan"; break;
        case 5: $name = "Ronan"; break;
        case 6: $name = "Amy"; break;
        case 7: $name = "Jin"; break;
        case 8: $name = "Sieghart"; break;
        case 9: $name = "Mari"; break;
        case 10: $name = "Dio"; break;
        case 11: $name = "Zero"; break;
        case 12: $name = "Ley"; break;
        case 13: $name = "Rufus"; break;
        case 14: $name = "Rin"; break;
        case 15: $name = "Asin"; break;
        case 16: $name = "Lime"; break;
        case 17: $name = "Edel"; break;
        case 18: $name = "Veigas"; break;
        case 19: $name = "Uno"; break;
        default: $name = "Unknown"; break;
    }

    return $name;
}

function getCharacterNumber($charname)
{
    switch($charname)
    {
        case "all": $number = -1; break;
        case "elesis": $number = 0; break;
        case "lire": $number = 1; break;
        case "arme": $number = 2; break;
        case "lass": $number = 3; break;
        case "ryan": $number = 4; break;
        case "ronan": $number = 5; break;
        case "amy": $number = 6; break;
        case "jin": $number = 7; break;
        case "sieghart": $number = 8; break;
        case "mari": $number = 9; break;
        case "dio": $number = 10; break;
        case "zero": $number = 11; break;
        case "ley": $number = 12; break;
        case "rufus": $number = 13; break;
        case "rin": $number = 14; break;
        case "asin": $number = 15; break;
        case "lime": $number = 16; break;
        case "edel": $number = 17; break;
        case "veigas": $number = 18; break;
        case "uno": $number = 19; break;
        default: $number = -1; break;
    }

    return $number;
}

function getCharacterJobName($chartype, $promotion)
{
    switch($chartype)
    {
        case 0:
            switch($promotion)
            {
                case 0: $jobname = "Knight"; break;
                case 1: $jobname = "Spearman"; break;
                case 2: $jobname = "Sword Master"; break;
                case 3: $jobname = "Savior"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 1:
            switch($promotion)
            {
                case 0: $jobname = "Archer"; break;
                case 1: $jobname = "Crossbowman"; break;
                case 2: $jobname = "Arch Ranger"; break;
                case 3: $jobname = "Nova"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 2:
            switch($promotion)
            {
                case 0: $jobname = "Magician"; break;
                case 1: $jobname = "Alchemist"; break;
                case 2: $jobname = "Warlock"; break;
                case 3: $jobname = "Battle Mage"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 3:
            switch($promotion)
            {
                case 0: $jobname = "Thief"; break;
                case 1: $jobname = "Assassin"; break;
                case 2: $jobname = "Dark Assassin"; break;
                case 3: $jobname = "Striker"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 4:
            switch($promotion)
            {
                case 0: $jobname = "Druid"; break;
                case 1: $jobname = "Sentinel"; break;
                case 2: $jobname = "Viken"; break;
                case 3: $jobname = "Vanquisher"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 5:
            switch($promotion)
            {
                case 0: $jobname = "Spell Knight"; break;
                case 1: $jobname = "Dragon Knight"; break;
                case 2: $jobname = "Aegis Knight"; break;
                case 3: $jobname = "Abyss Knight"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 6:
            switch($promotion)
            {
                case 0: $jobname = "Dancer"; break;
                case 1: $jobname = "Muse"; break;
                case 2: $jobname = "Siren"; break;
                case 3: $jobname = "Starlet"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 7:
            switch($promotion)
            {
                case 0: $jobname = "Fighter"; break;
                case 1: $jobname = "Shisa"; break;
                case 2: $jobname = "Asura"; break;
                case 3: $jobname = "Rama"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 8:
            switch($promotion)
            {
                case 0: $jobname = "Gladiator"; break;
                case 1: $jobname = "Warlord"; break;
                case 2: $jobname = "Duelist"; break;
                case 3: $jobname = "Prime Knight"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 9:
            switch($promotion)
            {
                case 0: $jobname = "Rune Caster"; break;
                case 1: $jobname = "Gunslinger"; break;
                case 2: $jobname = "Polaris"; break;
                case 3: $jobname = "Geas"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 10:
            switch($promotion)
            {
                case 0: $jobname = "Stygian"; break;
                case 1: $jobname = "Drakar"; break;
                case 2: $jobname = "Leviathan"; break;
                case 3: $jobname = "Dusk Bringer"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 11:
            switch($promotion)
            {
                case 0: $jobname = "Wanderer"; break;
                case 1: $jobname = "Seeker"; break;
                case 2: $jobname = "Vanisher"; break;
                case 3: $jobname = "Advancer"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 12:
            switch($promotion)
            {
                case 0: $jobname = "Summoner"; break;
                case 1: $jobname = "Harbinger"; break;
                case 2: $jobname = "Evoker"; break;
                case 3: $jobname = "Dark Matriarch"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 13:
            switch($promotion)
            {
                case 0: $jobname = "Bounty Hunter"; break;
                case 1: $jobname = "Soul Stalker"; break;
                case 2: $jobname = "Executioner"; break;
                case 3: $jobname = "Arbiter"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 14:
            switch($promotion)
            {
                case 0: $jobname = "Phoenix"; break;
                case 1: $jobname = "Awakened"; break;
                case 2: $jobname = "Chaotic"; break;
                case 3: $jobname = "Chosen"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 15:
            switch($promotion)
            {
                case 0: $jobname = "Disciple"; break;
                case 1: $jobname = "Mugen"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 16:
            switch($promotion)
            {
                case 0: $jobname = "Holy Knight"; break;
                case 1: $jobname = "Saint"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 17:
            switch($promotion)
            {
                case 0: $jobname = "Captain"; break;
                case 1: $jobname = "Major"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 18:
            switch($promotion)
            {
                case 0: $jobname = "Magi"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
        case 19:
            switch($promotion)
            {
                case 0: $jobname = "Bloodless"; break;
                default: $jobname = "Unknown"; break;
            }
            break;
    }

    return $jobname;
}
