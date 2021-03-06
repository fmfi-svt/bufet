<?php

class BufetData {
    private $PDO;
    private static $instance;
    
    private function __construct() {
        $this->PDO = new PDO(
            'mysql:host=localhost;dbname=m25bufet',
            'bufet',
            '34t411th3c4nd135',
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );
    }

    public static function getInstance() {
        
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    private function fetchUserData(&$value) {
        $cred = explode(':', $value['credentials_source']);
        if (count($cred) !== 2) {
            return;
        }
        if ($cred[0] !== 'fb') {
            return;
        }
        $encoded_json = 
            file_get_contents("http://graph.facebook.com/$cred[1]?");
        $dec = json_decode($encoded_json);
        if ($value['name'] === '<FETCH>') {
            $value['name'] = $dec->{'name'};
        }
        if ($value['username'] === '<FETCH>') {
            $value['username'] = $dec->{'username'};
        }
        if ($value['picture_url'] === '<FETCH>') {
            $value['picture_url'] = 
                "http://graph.facebook.com/$cred[1]/picture?type=normal";
        }
    }

    public function getUsers() {
        $sel = $this->PDO->prepare('SELECT * FROM users;');
        $sel->execute();
        $data = $sel->fetchAll(PDO::FETCH_ASSOC);
        foreach ($data as &$value) {
            $this->fetchUserData($value);
        }
        return $data;
    }

    public function getUser($uid) {
        $sel = $this->PDO->prepare('SELECT * FROM users WHERE uid = :uid');
        $sel->bindValue(':uid', $uid, PDO::PARAM_INT);
        $sel->execute();
        $ret = $sel->fetch(PDO::FETCH_ASSOC);
        $this->fetchUserData($ret);
        return $ret;

    }

    public function getInventory() {
        $sel = $this->PDO->query(
            'SELECT * from actual_price where iid in (select * from inventory);'
        );
        return $sel->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItem($iid) {
        $sel = $this->PDO->prepare(
            'SELECT * FROM actual_price WHERE iid = :iid '
        );
        $sel->bindValue(':iid', $iid, PDO::PARAM_INT);
        $sel->execute();
        return $sel->fetch(PDO::FETCH_ASSOC);
    }

    public function buyItem($uid, $iid, $amount, $ppu) {
        $item = $this->getItem($iid);
        if (
            $item['price'] != $ppu ||
            ($amount % $item['divisible']) != 0
        )   {
          return false;
        }
        $sel = $this->PDO->prepare(
            'INSERT INTO transactions (date,uid,iid,amount,price,type) 
            VALUES (NOW(), :uid, :iid, :amount, :ppu, \'purchase\');');
        $sel->bindValue(':uid', $uid, PDO::PARAM_INT);
        $sel->bindValue(':iid', $iid, PDO::PARAM_INT);
        $sel->bindValue(':amount', $amount, PDO::PARAM_INT);
        $sel->bindValue(':ppu', $ppu, PDO::PARAM_INT);
        if($sel->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getTransactions() {
        $sel = $this->PDO->prepare('SELECT * FROM transactions;');
        $sel->execute();
        return $sel->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTransactionsOfUser($uid) {
        $sel = $this->PDO->prepare(
            'SELECT * from transactions t join items i ON (t.iid = i.iid)
            WHERE uid = :uid ORDER BY date DESC;'
        );
        $sel->bindValue(':uid', $uid, PDO::PARAM_INT);
        $sel->execute();
        return $sel->fetchAll(PDO::FETCH_ASSOC);
    }
};
