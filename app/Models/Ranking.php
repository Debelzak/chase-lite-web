<?php
namespace App\Models;

use App\Core\Model;

class Ranking extends Model
{
    private $database;

    public function __construct()
    {
        $this->database = $this->getGameDatabase();
    }

    public function getRanking($type, $tabtype, $start, $end)
    {
        switch($type)
        {
            case "daily": $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserDaily WHERE TabType='$tabtype') SELECT RowNumber, Rank, CharType, Nick, LoginUID, GuildID, Win, Lose, WinRate FROM RankQuery WHERE RowNumber BETWEEN $start AND $end"; break;
            case "weekly": $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserWeekly WHERE TabType='$tabtype') SELECT RowNumber, Rank, CharType, Nick, LoginUID, GuildID, Win, Lose, WinRate FROM RankQuery WHERE RowNumber BETWEEN $start AND $end"; break;
            case "total": $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserMonthly WHERE TabType='$tabtype') SELECT RowNumber, Rank, CharType, Nick, LoginUID, GuildID, Win, Lose, WinRate FROM RankQuery WHERE RowNumber BETWEEN $start AND $end"; break;
            default: $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserExp WHERE TabType='$tabtype') SELECT RowNumber,Rank, CharType, Nick, LoginUID, GuildID, dbo.zbLevel_Exp(Exp) AS Level, EXP FROM RankQuery WHERE RowNumber BETWEEN $start AND $end"; break;
        }

        $this->database->query($sql);
        $this->database->execute();
        return $this->database->fetchAll();
    }

    public function getRankingByUser($type, $tabtype, $nick)
    {
        switch($type)
        {
            case "daily": $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserDaily WHERE TabType='$tabtype') SELECT RowNumber, Rank, CharType, Nick, LoginUID, GuildID, Win, Lose, WinRate FROM RankQuery WHERE Nick = '$nick'"; break;
            case "weekly": $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserWeekly WHERE TabType='$tabtype') SELECT RowNumber, Rank, CharType, Nick, LoginUID, GuildID, Win, Lose, WinRate FROM RankQuery WHERE Nick = '$nick'"; break;
            case "total": $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserMonthly WHERE TabType='$tabtype') SELECT RowNumber, Rank, CharType, Nick, LoginUID, GuildID, Win, Lose, WinRate FROM RankQuery WHERE Nick = '$nick'"; break;
            default: $sql = "WITH RankQuery AS (SELECT ROW_NUMBER() OVER(ORDER BY Rank ASC) AS RowNumber, * FROM dbo.RGRRankGrandchaserExp WHERE TabType='$tabtype') SELECT RowNumber,Rank, CharType, Nick, LoginUID, GuildID, dbo.zbLevel_Exp(Exp) AS Level, EXP FROM RankQuery WHERE Nick = '$nick'"; break;
        }

        $this->database->query($sql);
        $this->database->execute();
        return $this->database->fetch();
    }

    public function getRankingCount($type, $tabtype)
    {
        switch($type)
        {
            case "daily" : $table = "RGRRankGrandchaserDaily"; break;
            case "weekly" : $table = "RGRRankGrandchaserWeekly"; break;
            case "total" : $table = "RGRRankGrandchaserMonthly"; break;
            default: $table = "RGRRankGrandchaserExp"; break;
        }

        $this->database->query("SELECT * FROM ".$table." WHERE TabType=:tabType");
        $this->database->bind(1, $tabtype);
        $this->database->execute();
        return $this->database->rowsAffected();
    }

    public function generateDebugRanking($type)
    {
        $pvp = false;
        $exp = false;

        switch($type)
        {
            case "daily" : $table = "RGRRankGrandchaserDaily"; $pvp=true; break;
            case "weekly" : $table = "RGRRankGrandchaserWeekly"; $pvp=true; break;
            case "total" : $table = "RGRRankGrandchaserMonthly"; $pvp=true; break;
            default: $table = "RGRRankGrandchaserExp"; $exp=true; break;
        }

        $this->database->query("DELETE FROM ".$table."");
        $this->database->execute();

        for($h=0; $h<21; $h++)
        {
            for($i=0; $i<1000; $i++)
            {
                $TabType = $h;
                $Rank = $i+1;
                $LoginUID = rand(1000, 10000);
                $GuildID = rand(100, 10000);
                $GuildMark = rand(1, 100);
                $GuildName = "Guild_" . rand(100, 1000);
                if($TabType == 0)
                {
                    $CharType = rand(0, 19);
                } else {
                    $CharType = $TabType - 1;
                }
                $Nick = "Player_" . rand(1000, 9999);
                if($exp)
                {
                    $Exp = 999999999/$Rank;
                    $this->database->query("INSERT INTO ".$table." (TabType, Rank, LoginUID, GuildID, GuildMark, GuildName, CharType, Nick, Exp) VALUES (:TabType, :Rank, :LoginUID, :GuildID, :GuildMark, :GuildName, :CharType, :Nick, :Exp)");
                    $this->database->bind(1, $TabType);
                    $this->database->bind(2, $Rank);
                    $this->database->bind(3, $LoginUID);
                    $this->database->bind(4, $GuildID);
                    $this->database->bind(5, $GuildMark);
                    $this->database->bind(6, $GuildName);
                    $this->database->bind(7, $CharType);
                    $this->database->bind(8, $Nick);
                    $this->database->bind(9, (int)$Exp);
                }
                else
                {
                    $Win = 1000;
                    $Lose = 1*$Rank;
                    $this->database->query("INSERT INTO ".$table." (TabType, Rank, LoginUID, GuildID, GuildMark, GuildName, CharType, Nick, Win, Lose) VALUES (:TabType, :Rank, :LoginUID, :GuildID, :GuildMark, :GuildName, :CharType, :Nick, :Win, :Lose)");
                    $this->database->bind(1, $TabType);
                    $this->database->bind(2, $Rank);
                    $this->database->bind(3, $LoginUID);
                    $this->database->bind(4, $GuildID);
                    $this->database->bind(5, $GuildMark);
                    $this->database->bind(6, $GuildName);
                    $this->database->bind(7, $CharType);
                    $this->database->bind(8, $Nick);
                    $this->database->bind(9, $Win);
                    $this->database->bind(10, $Lose);
                }

                $this->database->execute();
            }
            
            print(getCharacterName($TabType-1) . " " . $type . " ranking generated.\n");         
        }

        return true;
    }
}