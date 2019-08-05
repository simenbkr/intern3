<?php


namespace intern3;


class Session
{
    public static function start()
    {
        session_start();
    }

    public static function destroy()
    {
        session_destroy();
    }

    public static function loginUser(Bruker $bruker)
    {
        self::destroy();
        self::start();
        self::setBruker($bruker);
        self::set('logged_in', time());
        self::set('last_active', time());
    }

    public static function setBruker(Bruker $bruker)
    {
        $beboer = $bruker->getPerson();
        $_SESSION['epost'] = $beboer->getEpost();
        $_SESSION['brid'] = $bruker->getId();
        $_SESSION['beid'] = $beboer->getId();
        $_SESSION['utvalg'] = $beboer->harUtvalgVerv() ? 1 : 0;
        $_SESSION['helga'] = $beboer->erHelgaGeneral() ? 1 : 0;
        $_SESSION['helga_inngang'] = $beboer->harHelgaTilgang() ? 1 : 0;
        $_SESSION['kjellermester'] = $beboer->erKjellerMester() ? 1 : 0;
        $_SESSION['data'] = $beboer->harDataVerv() ? 1 : 0;
    }

    public static function updatePrivileges()
    {
        $bruker = Bruker::medId(self::get('brid'));
        self::setBruker($bruker);
    }

    public static function refresh()
    {
        if (self::get('token')) {
            return;
        }

        $bruker = self::getAktivBruker();
        if (is_null($bruker) || is_null($bruker->getPerson()) || !$bruker->getPerson()->erAktiv()) {
            self::destroy();
            return;
        }

        //7 dager uten aktivitet eller hver 6. måned.
        if (time() - self::get('last_active') > 604800 || time() - self::get('logged_in') > 15778463) {
            self::destroy();
            self::start();
            Funk::setError('Du ble logget ut som følge av inaktivitet.');
            self::redirect();
            return;
        }

        self::updatePrivileges();
        self::set('last_active', time());
    }

    public static function set(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): ?string
    {
        return $_SESSION[$key];
    }

    public static function getAktivBruker(): ?Bruker
    {
        if (empty(self::get('brid'))) {

            if (empty(self::get('token'))) {
                self::destroy();
            }
            return null;
        }

        return Bruker::medId($_SESSION['brid']);
    }

    public static function redirect() {
        header("Refresh:0");
        exit();
    }

}