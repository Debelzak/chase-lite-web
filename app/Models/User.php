<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    private $database;

    public function __construct()
    {
        $this->database = $this->getGameDatabase();
    }

    public function getInfo($arguments = [])
    {
        if(!isset($arguments["username"])) {
            return;
        }

        $this->database->query("SELECT * FROM users WHERE Login=:username");
        $this->database->bind(1, $arguments["username"]);
        $this->database->execute();
        return json_encode($this->database->fetch());
    }

    public function getCharacters($username)
    {
        $this->database->query("SELECT CharType, Promotion, dbo.zbLevel_Exp(ExpS4) AS Level, Win, Lose, ExpS4 FROM Characters WHERE Login=:username");
        $this->database->bind(1, $username);
        $this->database->execute();
        return $this->database->fetchAll();
    }

    public function getNickname($username)
    {
        $userInfo = json_decode($this->getInfo(["username" => $username]));
        $loginUID = $userInfo->LoginUID;
        $this->database->query("SELECT * FROM UNGAUserNickname WHERE LoginUID=:uid");
        $this->database->bind(1, $loginUID);
        $this->database->execute();

        $return = $this->database->fetch();

        if(!empty($return))
        {
            return $this->database->fetch()->Nickname;
        }

        return false;
    }

    public function getUserGuild($loginUid)
    {
        $this->database->query("SELECT * FROM GSGAGuildSystemMember WHERE LoginUID=:loginUid");
        $this->database->bind(1, $loginUid);
        $this->database->execute();
        return $this->database->fetch();
    }

    public function getGuild($nid)
    {
        $this->database->query("SELECT * FROM GSGAGuildSystem WHERE NID=:nid");
        $this->database->bind(1, $nid);
        $this->database->execute();
        return $this->database->fetch();
    }

    public function getGuildSTR($nid)
    {
        $this->database->query("SELECT * FROM GSGAGuildSystemSTR WHERE NID=:nid");
        $this->database->bind(1, $nid);
        $this->database->execute();
        return $this->database->fetch();
    }

    public function checkEmailInUse($arguments = [])
    {
        if(!isset($arguments["email"])) {
            return;
        }

        $this->database->query("SELECT * FROM users WHERE email=:email");
        $this->database->bind(1, $arguments["email"]);
        $this->database->execute();
        return json_encode($this->database->rowsAffected() > 0);
    }

    public function searchAccountByEmailAndUser($arguments = [])
    {
        if(!isset($arguments["email"]) && !isset($arguments["username"])) {
            return;
        }

        $this->database->query("SELECT * FROM users WHERE email=:email AND Login=:username");
        $this->database->bind(1, $arguments["email"]);
        $this->database->bind(2, $arguments["username"]);
        $this->database->execute();
        return json_encode($this->database->rowsAffected() > 0);
    }

    public function insert($arguments = [])
    {
        if(!isset($arguments["username"]) || !isset($arguments["password"]) || !isset($arguments["email"]) || !isset($arguments["gender"])) {
            return;
        }

        $this->database->query("INSERT INTO users (Login, passwd, email, sex) VALUES (:username, :password, :email, :gender);");
        $this->database->bind(1, $arguments["username"]);
        $this->database->bind(2, md5($arguments["password"]));
        $this->database->bind(3, $arguments["email"]);
        $this->database->bind(4, $arguments["gender"]);
        $this->database->execute();

        return json_encode($this->database->rowsAffected() > 0);
    }

    public function getCashUser($username)
    {
        $this->database->query("SELECT * FROM CashUsers WHERE Login=:username");
        $this->database->bind(1, $username);
        $this->database->execute();
        return $this->database->fetch();
    }

    public function setPassword($username, $newPassword)
    {
        $newPassword = md5($newPassword);
        $this->database->query("UPDATE users SET passwd=:password WHERE Login=:username");
        $this->database->bind(1, $newPassword);
        $this->database->bind(2, $username);
        $this->database->execute();
        return ($this->database->rowsAffected() > 0);
    }

    public function addCash($username, $value)
    {
        if(empty($this->getCashUser($username)))
        {
            $this->database->query("INSERT INTO CashUsers (Cash, Login) VALUES (:value, :username)");
        }
        else
        {
            $this->database->query("UPDATE CashUsers SET Cash=Cash+:value WHERE Login=:username");
        }

        $this->database->bind(1, $value);
        $this->database->bind(2, $username);
        $this->database->execute();
        return ($this->database->rowsAffected() > 0);
    }

    public function getItemName($itemId)
    {
        $query = "SELECT GoodsName FROM GoodsInfoList WHERE GoodsID=:itemId";
        $database = $this->getGameDatabase();
        $database->query($query);
        $database->bind(1, $itemId);
        $database->execute();
        $item = $database->fetch();
        if(!empty($item))
        {
            return $item->GoodsName;
        }

        return "Unknown";
    }

    public function insertPostMail($loginUid, $title, $body, $gp)
    {

        $query = "INSERT INTO PSGAPostSystem (LoginUID, CharType, SLoginUID, Type, Status, RegDateA, MaintenancePeriod, Title, Contents, GamePoint) VALUES (:LoginUID, -1, 0, 1, 0, getDate(), 0, :Title, :Body, :GP)";
        $database = $this->getGameDatabase();
        $database->query($query);
        $database->bind(1, $loginUid);
        $database->bind(2, $title);
        $database->bind(3, $body);
        $database->bind(4, $gp);
        $database->execute();
        return $this->getLastPostMailId($loginUid);
    }

    public function insertPostMailItem($postUid, $loginUid, $wigaId, $count)
    {

        $query = "INSERT INTO PSGAPostSystemItem (LoginUID, CharType, PostUID, ReferenceItemUID, CNT) VALUES (:loginUid, -1, :postUid, :wigaId, :count)";
        $database = $this->getGameDatabase();
        $database->query($query);
        $database->bind(1, $loginUid);
        $database->bind(2, $postUid);
        $database->bind(3, $wigaId);
        $database->bind(4, $count);
        $database->execute();
        return ($database->rowsAffected() > 0);
    }

    public function insertPostWaitItem($loginUID, $itemId, $period, $count)
    {
        $query = "INSERT INTO WIGAWaitItem20130402 (LoginUID, CharType, GetType, Status, RegDateA, BuyerNickname, ItemID, Grade, Period, CNT, ItemLevel, StrengthLevel) VALUES ($loginUID, -1, 0, 12, getDate(), '__RedeemToken__', $itemId, -1, $period, $count, 0, 0)";
        $database = $this->getGameDatabase();
        $database->query($query);
        $database->bind(1, $loginUID);
        $database->bind(2, $itemId);
        $database->bind(3, $period);
        $database->bind(4, $count);
        $database->execute();
        return $this->getLastWigaId($loginUID);
    }

    public function getLastPostMailId($loginUid)
    {
        $query = "SELECT PostUID FROM PSGAPostSystem WHERE LoginUID=:LoginUID ORDER By PostUID DESC";
        $database = $this->getGameDatabase();
        $database->query($query);
        $database->bind(1, $loginUid);
        $database->execute();
        return $database->fetch()->PostUID;
    }

    public function getLastWigaId($loginUid)
    {
        $query = "SELECT WIGAUID FROM WIGAWaitItem20130402 WHERE LoginUID=:LoginUID ORDER By WIGAUID DESC";
        $database = $this->getGameDatabase();
        $database->query($query);
        $database->bind(1, $loginUid);
        $database->execute();
        return $database->fetch()->WIGAUID;
    }

}