<?php
namespace App\Models;

use App\Core\Model;

class Donation extends Model
{
    private $database;

    public function __construct()
    {
        $this->database = $this->getWebDatabase();
    }

    public function getByReference($reference)
    {
        $this->database->query("SELECT * FROM donationLog WHERE reference=:reference");
        $this->database->bind(1, $reference);
        $this->database->execute();
        return $this->database->fetch();
    }

    public function getUserDonations($username)
    {
        $this->database->query("SELECT TOP 100 * FROM donationLog WHERE username=:username");
        $this->database->bind(1, $username);
        $this->database->execute();
        return $this->database->fetchAll();
    }    

    public function insertPaymentLog($username, $reference, $payer_email, $status, $amount, $method)
    {
        $this->database->query("INSERT INTO donationLog (username, reference, credited, payer_email, status, amount, method) VALUES (:username, :reference, 0, :payer_email, :status, :amount, :method)");
        $this->database->bind(1, $username);
        $this->database->bind(2, $reference);
        $this->database->bind(3, $payer_email);
        $this->database->bind(4, $status);
        $this->database->bind(5, $amount);
        $this->database->bind(6, $method);
        $this->database->execute();
        return ($this->database->rowsAffected() > 0);
    }

    public function changePaymentStatus($reference, $status)
    {
        $this->database->query("UPDATE donationLog SET status=:status WHERE reference=:reference");
        $this->database->bind(1, $status);
        $this->database->bind(2, $reference);
        $this->database->execute();
        return ($this->database->rowsAffected() > 0);
    }

    public function applyDonation($reference)
    {
        $model = $this->getByReference($reference);
        if(!empty($model) && !$model->credited)
        {
            $username = $model->username;

            foreach(PAYMENT_TO_CASH as $v => $c)
            {
                if($v == $model->amount)
                {
                    $value = $c;
                }
            }

            $user = new \App\Models\User();
            if($user->addCash($username, $value))
            {
                $this->database->query("UPDATE donationLog SET credited=1, lastUpdateTime=getDate() WHERE reference=:reference");
                $this->database->bind(1, $reference);
                $this->database->execute();
                return true;
            }
        } else {
            return false;
        }
    }
}