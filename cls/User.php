<?php

class User
{
    private $_db,
        $_data,
        $_sessionName,
        $_cookieName,
        $_isLoggedIn;

    public function __construct($user = null)
    {
        $this->_db = DB::getInstance();
        $this->_sessionName = Config::get("session/name");
        $this->_cookieName = Config::get("cookie/name");
        if (!$user) {
            if (Session::exists($this->_sessionName)) {
                if ($this->find(Session::get($this->_sessionName))) {
                    $this->_isLoggedIn = true;
                }
            }
        } else {
            $this->find($user);
        }
    }

    public function create($fields = [])
    {
        if (!empty($fields)) {
            if (!$this->_db->insert("Users", $fields)) {
                throw new Exception("Insert Exception");
            }
        }
        return false;
    }

    public function update($fields = [], $id = null)
    {
        if (!$id && $this->isLoggedIn()) {
            $id = $this->data()->UserId;
        }
        if (!empty($fields) && $id) {
            if (!$this->_db->update("Users", ["UserId" => $id], $fields)) {
                throw new Exception("Update Exception");
            }
        }
        return false;
    }

    public function login($username = null, $password = null, $remember = false)
    {
        if (!$username && !$password && $this->exists()) {
            Session::put($this->_sessionName, $this->data()->UserId);
        } else {
            $user = $this->find($username);
            if ($user) {
                if ($this->data()->Password === Hash::make($password, $this->data()->Salt)) {
                    Session::put($this->_sessionName, $this->data()->UserId);
                    if ($remember) {
                        $hash = Hash::unique();
                        $hashCheck = $this->_db->get("Users_Session", ["UserId", "=", $this->data()->UserId]);
                        if (!$hashCheck->count()) {
                            $this->_db->insert("Users_Session", [
                                "UserId" => $this->data()->UserId,
                                "Hash" => $hash
                            ]);
                        } else {
                            $hash = $hashCheck->first()->Hash;
                        }
                        Cookie::put($this->_cookieName, $hash, Config::get("cookie/expiry"));
                    }
                    return true;
                }
            }
        }
        return false;
    }

    public function logout()
    {
        $this->_db->delete("Users_Session", ["UserId", "=", $this->data()->id]);
        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }

    public function hasPermission($key)
    {
        $group = $this->_db->get("Groups", ["GroupId", "=", $this->data()->GroupId]);
        if ($group->count()) {
            $permissions = json_decode($group->first()->Permission, true);
            if (isset($permissions[$key]) && $permissions[$key] == true) {
                return true;
            }
        }
        return false;
    }

    protected function find($user = null)
    {
        if ($user) {
            $field = (is_numeric($user)) ? "UserId" : "Username";
            $data = $this->_db->get("Users", [$field, "=", $user]);
            if ($data->count()) {
                $this->_data = $data->first();
                return true;
            }
        }
        return false;
    }

    public function exists()
    {
        return (!empty($this->_data)) ? true : false;
    }

    public function data()
    {
        return $this->_data;
    }

    public function isLoggedIn()
    {
        return $this->_isLoggedIn;
    }

    public function userType() {
        $group = $this->_db->get("Groups", ["GroupId", "=", $this->data()->GroupId]);
        if ($group->count()) {
            return $group->first()->Name;
        }
        return false;
    }

    public function fetch() {
        $data = $this->_db->query("SELECT `u`.*, `g`.`Name` AS `GroupName` FROM `Users` `u` INNER JOIN `Groups` `g` ON `u`.`GroupId` = `g`.GroupId");
        if ($data->count())
            return $data->results();
        return false;
    }

    public function delete($id = null) {
        if ($id) {
            $data = $this->_db->delete("Users", ["UserId", "=", $id]);
            if (!$data->error()) {
                return true;
            }
        }
        return false;
    }
}