<?php

namespace intern3;

class CtrlData
{
    private $arg;
    private $pos;
    private $aktivBruker;
    private $adminBruker;
    private $rot;
    private $base;
    private $db;

    public function __construct($arg, $pos = 0, $rot = 0)
    {
        $this->arg = (array)$arg;
        $this->pos = $pos;
        $this->aktivBruker = null;
        $this->adminBruker = null;
        $this->rot = $rot;
        $this->base = array();
        $this->db = DB::getDB();
    }

    public function getArg($pos)
    {
        return isset($this->arg[$pos]) ? $this->arg[$pos] : null;
    }

    public function getAllArgs()
    {
        return isset($this->arg) ? $this->arg : null;
    }

    public function getAktuellArgPos()
    {
        $len = count($this->arg);
        return $len > $this->pos ? $this->pos : -1;
    }

    public function getAktueltArg()
    {
        $pos = $this->getAktuellArgPos();
        return $pos == -1 ? null : $this->arg[$pos];
    }

    public function getSisteArg()
    {
        $len = count($this->arg);
        return $len > 0 ? $this->arg[$len - 1] : null;
    }

    public function skiftArg()
    {
        $kopi = new self($this->arg, $this->pos + 1, $this->rot);
        $kopi->setAktivBruker($this->aktivBruker);
        $kopi->setAdminBruker($this->adminBruker);
        return $kopi;
    }

    public function skiftArgMedRot($aktiverBruker)
    {
        $kopi = new self($this->arg, $this->pos + 1, $this->pos);
        $kopi->setAdminBruker($this->aktivBruker);
        $kopi->setAktivBruker($aktiverBruker);
        return $kopi;
    }

    public function setAktivBruker($aktivBruker)
    {
        $this->aktivBruker = $aktivBruker;
    }

    public function getAktivBruker()
    {

        if (is_null($this->aktivBruker) && isset($_SESSION['brid'])) {
            $this->aktivBruker = Session::getAktivBruker();
        }

        return $this->aktivBruker;
    }

    public function setAdminBruker($adminBruker)
    {
        $this->adminBruker = $adminBruker;
    }

    public function getAdminBruker()
    {
        return $this->adminBruker;
    }

    public function getBase($pos = 0)
    {
        $pos += $this->rot - 1;
        if (!isset($this->base[$pos])) {
            if ($this->rot == 0 || count($this->arg) < $pos) {
                $this->base[$pos] = '';
            } else {
                $this->base[$pos] = implode('/', array_slice($this->arg, 0, $pos + $this->rot)) . '/';
            }
        }
        return '?a=' . $this->base[$pos];
    }
}