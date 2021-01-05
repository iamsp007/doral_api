<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    use HasFactory;

    public function insertOTP($data){

        $otp = new Otp();
        $otp->occasion = $data['occasion'];
        $otp->token = $data['token'];
        $otp->contact = $data['contact'];
        $otp->user_id = $data['user_id'];
        $otp->save();

    }

    public function verifyOTP($data){

        $sql = "SELECT * FROM otps WHERE occasion = :occasion AND token = :token AND contact = :contact";

        $sth = $this->connection->prepare($sql);

        $sth->setFetchMode(PDO::FETCH_ASSOC);

        $sth->execute(array(
            ':occasion' => $data['occasion'],
            ':token' => $data['token'],
            ':contact' => $data['contact']
        ));

        return $response = $sth->fetch();


    }


}
