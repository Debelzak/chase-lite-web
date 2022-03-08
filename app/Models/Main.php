<?php
namespace App\Models;

use App\Core\Model;

class Main extends Model
{
    public function consultRewardToken($serial)
    {
        $query = "SELECT * FROM rewardToken WHERE serial=:serial";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $serial);
        $database->execute();
        return $database->fetch();
    }

    public function getAdminAuth($username)
    {
        $query = "SELECT * FROM adminAuthLevel WHERE username=:username";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $username);
        $database->execute();
        $object = $database->fetch();;
        if(isset($object->authLevel))
        {
            return $object->authLevel;
        }

        return 0;
    }

    public function consultRewardTokenItems($tokenId)
    {
        $query = "SELECT * FROM rewardTokenItem WHERE tokenUid=:tokenUid";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $tokenId);
        $database->execute();
        return $database->fetchAll();
    }

    public function consultAlreadyUsedToken($username, $tokenId)
    {
        $query = "SELECT * FROM rewardTokenLog WHERE username=:username AND tokenId=:tokenId";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $username);
        $database->bind(2, $tokenId);
        $database->execute();
        return ($database->rowsAffected() > 0);
    }

    public function useToken($username, $tokenId)
    {
        $query = "INSERT INTO rewardTokenLog (tokenId, username, regDate) VALUES (:tokenId, :username, getDate()); UPDATE rewardToken SET useCount=useCount+1 WHERE id=:id";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $tokenId);
        $database->bind(2, $username);
        $database->bind(3, $tokenId);
        $database->execute();
        return ($database->rowsAffected() > 0);
    }

    public function consultAccountRecoveryToken($token)
    {
        $query = "SELECT * FROM recoverPassword WHERE token=:token";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $token);
        $database->execute();
        return $database->fetch();
    }

    public function consumeAccountRecoveryToken($token)
    {
        $query = "UPDATE recoverPassword SET used=1, useDate=getDate() WHERE token=:token";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $token);
        $database->execute();
        return ($database->rowsAffected() > 0);
    }

    public function insertAccountRecoveryToken($token, $username)
    {
        $query = "INSERT INTO recoverPassword (token, username) VALUES (:token, :username)";
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->bind(1, $token);
        $database->bind(2, $username);
        $database->execute();
        return ($database->rowsAffected() > 0);
    }

    public function getPatchnotes($limit = 100, $importantOnly = false, $regularOnly = false)
    {
        $query = "SELECT TOP ".$limit." * FROM patchnotes ORDER by id DESC";
        if($importantOnly)
        {
            $query = "SELECT TOP ".$limit." * FROM patchnotes WHERE important=1 ORDER by id DESC";
        }
        else if($regularOnly)
        {
            $query = "SELECT TOP ".$limit." * FROM patchnotes WHERE important=0 ORDER by id DESC";
        }
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->execute();

        return $database->fetchAll();
    }

    public function getPatchnote($id)
    {
        $database = $this->getWebDatabase();
        $database->query("SELECT * FROM patchnotes WHERE id=?");
        $database->bind(1, $id);
        $database->execute();

        return $database->fetch();
    }

    public function updatePatchnote($id, $title, $body, $banner, $important)
    {
        $database = $this->getWebDatabase();
        $database->query("UPDATE patchnotes SET title=?, body=?, banner=?, important=? WHERE id=?");
        $database->bind(1, $title);
        $database->bind(2, $body);
        $database->bind(3, $banner);
        $database->bind(4, $important);
        $database->bind(5, $id);
        $database->execute();

        return ($database->rowsAffected() > 0);
    }

    public function insertPatchnote($title, $body, $banner, $important)
    {
        $database = $this->getWebDatabase();
        $database->query("INSERT INTO patchnotes (title, body, banner, important) VALUES (?,?,?,?)");
        $database->bind(1, $title);
        $database->bind(2, $body);
        $database->bind(3, $banner);
        $database->bind(4, $important);
        $database->execute();

        return ($database->rowsAffected() > 0);
    }

    public function deletePatchnote($id)
    {
        $database = $this->getWebDatabase();
        $database->query("DELETE FROM patchnotes WHERE id=?");
        $database->bind(1, $id);
        $database->execute();

        return ($database->rowsAffected() > 0);
    }

    public function getSliders()
    {
        $limit = (isset($arguments["limit"])) ? $arguments["limit"] : 10;
        $query = "SELECT TOP ".$limit." * FROM sliders WHERE hidden=0 ORDER by id DESC";
        if(isset($arguments["all"]) && $arguments["all"] == true)
        {
            $query = "SELECT TOP ".$limit." * FROM sliders ORDER by id DESC";
        }
        $database = $this->getWebDatabase();
        $database->query($query);
        $database->execute();
        
        return json_encode($database->fetchAll());
    }
}